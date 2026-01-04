#!/usr/bin/env python3
"""
Topic Image Downloader

Downloads images for topics using Bing image search API (similar to word images).
This script fetches topics from the Laravel database and downloads images for each topic.
"""

import mysql.connector
import hashlib
import requests
import os
import threading
import time
import argparse
import json
import urllib.parse
from pathlib import Path
from concurrent.futures import ThreadPoolExecutor, as_completed

class TopicImageDownloader:
    def __init__(self, db_config, img_folder="img_topic", progress_file="topic_download_progress.json"):
        self.db_config = db_config
        self.img_folder = Path(img_folder)
        self.progress_file = progress_file

        # Create session with headers to mimic browser
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36',
            'Accept': 'image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language': 'zh-CN,zh;q=0.9,en;q=0.8',
            'Accept-Encoding': 'gzip, deflate, br'
        })

        # Create folders
        self.img_folder.mkdir(exist_ok=True)

        # Load progress
        self.downloaded_topics = self.load_progress()

        # Stats
        self.stats = {
            'total': 0,
            'downloaded': 0,
            'skipped': 0,
            'failed': 0,
            'start_time': time.time()
        }

        self.lock = threading.Lock()

    def generate_topic_hash(self, topic_name):
        """Generate MD5 hash for a topic name."""
        return hashlib.md5(topic_name.encode('utf-8')).hexdigest().lower()

    def generate_bing_image_url(self, query, width=720, height=406):
        """Generate Bing image URL using the same pattern as word images."""
        encoded_query = urllib.parse.quote(query)
        url = f"https://th.bing.com/th?q={encoded_query}&w={width}&h={height}&c=7&rs=1&p=0&o=5&dpr=2&pid=1.7&mkt=zh-WW&cc=VN&setlang=zh-CN&adlt=moderate&t=1"
        return url

    def load_progress(self):
        """Load previously downloaded topic IDs."""
        if os.path.exists(self.progress_file):
            try:
                with open(self.progress_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    return set(data.get('downloaded_ids', []))
            except (json.JSONDecodeError, FileNotFoundError):
                pass
        return set()

    def save_progress(self):
        """Save download progress."""
        progress_data = {
            'downloaded_ids': list(self.downloaded_topics),
            'total_downloaded': len(self.downloaded_topics),
            'last_updated': time.time()
        }

        with open(self.progress_file, 'w', encoding='utf-8') as f:
            json.dump(progress_data, f, indent=2, ensure_ascii=False)

    def get_topics_from_db(self):
        """Fetch all topics from database."""
        conn = mysql.connector.connect(**self.db_config)
        cursor = conn.cursor(dictionary=True)

        query = """
        SELECT id, name, name_zh, description, image_url 
        FROM topics 
        WHERE is_active = 1
        ORDER BY id
        """
        
        cursor.execute(query)
        topics = cursor.fetchall()
        
        cursor.close()
        conn.close()

        return topics

    def download_topic_image(self, topic):
        """Download image for a single topic."""
        topic_id = topic['id']
        topic_name = topic['name']
        topic_name_zh = topic['name_zh']

        # Check if already downloaded
        if topic_id in self.downloaded_topics:
            with self.lock:
                self.stats['skipped'] += 1
            return {'status': 'skipped', 'reason': 'already_downloaded', 'topic': topic_name}

        # Generate hash and filename
        topic_hash = self.generate_topic_hash(topic_name)
        filename = f"{topic_hash}_topic.jpg"
        filepath = self.img_folder / filename

        # Check if file exists
        if filepath.exists():
            with self.lock:
                self.downloaded_topics.add(topic_id)
                self.stats['skipped'] += 1
            return {'status': 'skipped', 'reason': 'file_exists', 'topic': topic_name}

        try:
            # Generate search query - use Chinese name if available, otherwise English
            search_query = topic_name_zh if topic_name_zh else topic_name
            
            # Add context to get better images
            search_query += " 学习 教学"  # Add "learning teaching" in Chinese
            
            # Generate Bing URL
            bing_url = self.generate_bing_image_url(search_query)

            # Download the image
            response = self.session.get(bing_url, timeout=30, stream=True)
            response.raise_for_status()

            # Check if it's actually an image
            content_type = response.headers.get('content-type', '')
            if not content_type.startswith('image/'):
                return {'status': 'failed', 'reason': 'not_image', 'topic': topic_name, 'content_type': content_type}

            # Check if we got a valid image
            content_length = response.headers.get('content-length')
            if content_length and int(content_length) < 1000:
                return {'status': 'failed', 'reason': 'too_small', 'topic': topic_name, 'size': content_length}

            # Save the image
            with open(filepath, 'wb') as f:
                for chunk in response.iter_content(chunk_size=8192):
                    if chunk:
                        f.write(chunk)

            # Verify the saved file
            if filepath.stat().st_size < 1000:
                filepath.unlink()
                return {'status': 'failed', 'reason': 'saved_too_small', 'topic': topic_name}

            # Update database with local image path
            local_image_url = f"/storage/topics/{filename}"
            self.update_topic_image_url(topic_id, local_image_url)

            # Update progress
            with self.lock:
                self.downloaded_topics.add(topic_id)
                self.stats['downloaded'] += 1

            return {
                'status': 'success', 
                'topic': topic_name, 
                'file': filename, 
                'url': bing_url,
                'local_url': local_image_url
            }

        except requests.exceptions.RequestException as e:
            with self.lock:
                self.stats['failed'] += 1
            return {'status': 'failed', 'reason': str(e), 'topic': topic_name}
        except Exception as e:
            with self.lock:
                self.stats['failed'] += 1
            return {'status': 'failed', 'reason': f'Unexpected error: {str(e)}', 'topic': topic_name}

    def update_topic_image_url(self, topic_id, image_url):
        """Update topic's image_url in database."""
        try:
            conn = mysql.connector.connect(**self.db_config)
            cursor = conn.cursor()

            query = "UPDATE topics SET image_url = %s WHERE id = %s"
            cursor.execute(query, (image_url, topic_id))
            
            conn.commit()
            cursor.close()
            conn.close()
        except Exception as e:
            print(f"\nWarning: Could not update database for topic {topic_id}: {e}")

    def print_progress(self):
        """Print current progress."""
        elapsed = time.time() - self.stats['start_time']
        processed = self.stats['downloaded'] + self.stats['skipped'] + self.stats['failed']

        if processed > 0:
            rate = processed / elapsed if elapsed > 0 else 0
            eta = (self.stats['total'] - processed) / rate if rate > 0 else 0

            print(f"\rProgress: {processed}/{self.stats['total']} "
                  f"(Downloaded: {self.stats['downloaded']}, "
                  f"Skipped: {self.stats['skipped']}, "
                  f"Failed: {self.stats['failed']}) "
                  f"Rate: {rate:.1f}/s "
                  f"ETA: {eta/60:.1f}m", end='', flush=True)

    def download_all_topics(self, max_workers=4, save_interval=10):
        """Download images for all topics with threading."""
        print("Fetching topics from database...")
        topics = self.get_topics_from_db()

        if not topics:
            print("No topics found in the database.")
            return

        self.stats['total'] = len(topics)
        print(f"Found {len(topics)} topics to process")
        print(f"Using {max_workers} threads")
        print(f"Images will be saved to: {self.img_folder.absolute()}")
        print(f"Progress will be saved to: {self.progress_file}")
        print("-" * 80)

        # Use ThreadPoolExecutor for concurrent downloads
        with ThreadPoolExecutor(max_workers=max_workers) as executor:
            # Submit all tasks
            future_to_topic = {executor.submit(self.download_topic_image, topic): topic for topic in topics}

            # Process completed tasks
            processed_count = 0
            for future in as_completed(future_to_topic):
                topic = future_to_topic[future]

                try:
                    result = future.result()

                    # Print detailed results
                    if result['status'] == 'failed':
                        print(f"\nFAILED: {result['topic']} - {result['reason']}")
                    elif result['status'] == 'success':
                        print(f"\nSUCCESS: {result['topic']} -> {result['file']}")

                except Exception as e:
                    with self.lock:
                        self.stats['failed'] += 1
                    print(f"\nException processing {topic['name']}: {e}")

                processed_count += 1

                # Print progress every 5 items or save progress every save_interval
                if processed_count % 5 == 0:
                    self.print_progress()

                if processed_count % save_interval == 0:
                    self.save_progress()

        # Final progress update
        self.print_progress()
        print()  # New line

        # Save final progress
        self.save_progress()

        # Print final stats
        elapsed = time.time() - self.stats['start_time']
        print("-" * 80)
        print(f"Topic image download completed!")
        print(f"Total time: {elapsed/60:.1f} minutes")
        print(f"Total topics: {self.stats['total']}")
        print(f"Successfully downloaded: {self.stats['downloaded']}")
        print(f"Skipped (already exists): {self.stats['skipped']}")
        print(f"Failed: {self.stats['failed']}")
        if self.stats['total'] > 0:
            print(f"Success rate: {(self.stats['downloaded'] / self.stats['total'] * 100):.1f}%")
        print(f"Average rate: {self.stats['total']/elapsed:.1f} topics/second")

        # Print database update reminder
        print("\n" + "=" * 80)
        print("REMINDER: Images have been saved to local storage.")
        print("Make sure to:")
        print("1. Move images to public/storage/topics/ folder")
        print("2. Run: php artisan storage:link (if not already linked)")
        print("3. Check that image URLs in database are correct")


def main():
    parser = argparse.ArgumentParser(description='Download images for topics using Bing')
    parser.add_argument('--host', default='localhost',
                        help='MySQL host (default: localhost)')
    parser.add_argument('--user', default='root',
                        help='MySQL user (default: root)')
    parser.add_argument('--password', required=True,
                        help='MySQL password')
    parser.add_argument('--database', required=True,
                        help='MySQL database name')
    parser.add_argument('--port', type=int, default=3306,
                        help='MySQL port (default: 3306)')
    parser.add_argument('--output', default='img_topic',
                        help='Output folder for images (default: img_topic)')
    parser.add_argument('--threads', type=int, default=4,
                        help='Number of download threads (default: 4)')
    parser.add_argument('--progress', default='topic_download_progress.json',
                        help='Progress file (default: topic_download_progress.json)')
    parser.add_argument('--save-interval', type=int, default=10,
                        help='Save progress every N downloads (default: 10)')

    args = parser.parse_args()

    # Database configuration
    db_config = {
        'host': args.host,
        'user': args.user,
        'password': args.password,
        'database': args.database,
        'port': args.port,
        'charset': 'utf8mb4',
        'collation': 'utf8mb4_unicode_ci'
    }

    # Test database connection
    try:
        conn = mysql.connector.connect(**db_config)
        conn.close()
        print("Database connection successful!")
    except Exception as e:
        print(f"Error: Could not connect to database: {e}")
        return 1

    # Create downloader and start
    downloader = TopicImageDownloader(
        db_config=db_config,
        img_folder=args.output,
        progress_file=args.progress
    )

    try:
        downloader.download_all_topics(
            max_workers=args.threads,
            save_interval=args.save_interval
        )
    except KeyboardInterrupt:
        print("\n\nDownload interrupted by user. Progress has been saved.")
        downloader.save_progress()
        return 1
    except Exception as e:
        print(f"\nUnexpected error: {e}")
        downloader.save_progress()
        return 1

    return 0


if __name__ == "__main__":
    exit(main())
