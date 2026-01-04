#!/usr/bin/env python3
"""
Hanzii Failed Images Downloader

Downloads failed images using Bing fallback URLs.
This script identifies words that failed during the original download
and attempts to download them using Bing's image search API.
"""

import sqlite3
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

class HanziiFailedImageDownloader:
    def __init__(self, db_path, original_img_folder="img_word", failed_img_folder="img_word_failed",
                 progress_file="download_progress.json", failed_progress_file="failed_download_progress.json"):
        self.db_path = db_path
        self.original_img_folder = Path(original_img_folder)
        self.failed_img_folder = Path(failed_img_folder)
        self.progress_file = progress_file
        self.failed_progress_file = failed_progress_file

        # Create session with headers to mimic the app
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36',
            'Accept': 'image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language': 'zh-CN,zh;q=0.9,en;q=0.8',
            'Accept-Encoding': 'gzip, deflate, br'
        })

        # Create folders
        self.failed_img_folder.mkdir(exist_ok=True)

        # Load progress
        self.successfully_downloaded = self.load_original_progress()
        self.failed_downloaded = self.load_failed_progress()

        # Stats
        self.stats = {
            'total_failed': 0,
            'downloaded': 0,
            'skipped': 0,
            'failed_again': 0,
            'start_time': time.time()
        }

        self.lock = threading.Lock()

    def generate_hanzii_image_hash(self, word):
        """Generate MD5 hash for a word (same as original algorithm)."""
        return hashlib.md5(word.encode('utf-8')).hexdigest().lower()

    def generate_bing_image_url(self, word, width=720, height=406):
        """Generate Bing image URL using the app's fallback pattern."""
        encoded_word = urllib.parse.quote(word)
        url = f"https://th.bing.com/th?q={encoded_word}&w={width}&h={height}&c=7&rs=1&p=0&o=5&dpr=2&pid=1.7&mkt=zh-WW&cc=VN&setlang=zh-CN&adlt=moderate&t=1"
        return url

    def load_original_progress(self):
        """Load successfully downloaded hashes from original progress file."""
        if os.path.exists(self.progress_file):
            try:
                with open(self.progress_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    return set(data.get('downloaded_hashes', []))
            except (json.JSONDecodeError, FileNotFoundError):
                pass
        return set()

    def load_failed_progress(self):
        """Load previously downloaded failed image hashes."""
        if os.path.exists(self.failed_progress_file):
            try:
                with open(self.failed_progress_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    return set(data.get('downloaded_hashes', []))
            except (json.JSONDecodeError, FileNotFoundError):
                pass
        return set()

    def save_failed_progress(self):
        """Save failed download progress."""
        progress_data = {
            'downloaded_hashes': list(self.failed_downloaded),
            'total_downloaded': len(self.failed_downloaded),
            'last_updated': time.time()
        }

        with open(self.failed_progress_file, 'w', encoding='utf-8') as f:
            json.dump(progress_data, f, indent=2)

    def get_failed_words_from_db(self, start_index=None, end_index=None):
        """Get words that failed during original download."""
        conn = sqlite3.connect(self.db_path)

        if start_index is not None and end_index is not None:
            query = """
            SELECT rowid, word FROM cnvi
            WHERE rowid >= ? AND rowid <= ?
            ORDER BY rowid
            """
            cursor = conn.execute(query, (start_index, end_index))
        elif start_index is not None:
            query = """
            SELECT rowid, word FROM cnvi
            WHERE rowid >= ?
            ORDER BY rowid
            """
            cursor = conn.execute(query, (start_index,))
        elif end_index is not None:
            query = """
            SELECT rowid, word FROM cnvi
            WHERE rowid <= ?
            ORDER BY rowid
            """
            cursor = conn.execute(query, (end_index,))
        else:
            query = "SELECT rowid, word FROM cnvi ORDER BY rowid"
            cursor = conn.execute(query)

        all_words = cursor.fetchall()
        conn.close()

        # Filter to only failed words
        failed_words = []
        for row_id, word in all_words:
            if not word or not word.strip():
                continue

            word_hash = self.generate_hanzii_image_hash(word)
            original_file = self.original_img_folder / f"{word_hash}_h.jpg"

            # Check if it failed in original download (not in successful downloads and file doesn't exist)
            if word_hash not in self.successfully_downloaded and not original_file.exists():
                failed_words.append((row_id, word))

        return failed_words

    def download_failed_image(self, word_data):
        """Download a single failed image using Bing URL."""
        row_id, word = word_data

        if not word or not word.strip():
            return {'status': 'skipped', 'reason': 'empty_word', 'word': word}

        word_hash = self.generate_hanzii_image_hash(word)
        filename = f"{word_hash}_h.jpg"  # Same filename format as original
        filepath = self.failed_img_folder / filename

        # Check if already downloaded in failed folder
        if word_hash in self.failed_downloaded or filepath.exists():
            with self.lock:
                self.stats['skipped'] += 1
            return {'status': 'skipped', 'reason': 'already_exists', 'word': word}

        # Check if it exists in original folder (shouldn't happen, but double check)
        original_file = self.original_img_folder / f"{word_hash}_h.jpg"
        if original_file.exists():
            with self.lock:
                self.stats['skipped'] += 1
            return {'status': 'skipped', 'reason': 'exists_in_original', 'word': word}

        try:
            # Generate Bing URL
            bing_url = self.generate_bing_image_url(word)

            # Download the image
            response = self.session.get(bing_url, timeout=30, stream=True)
            response.raise_for_status()

            # Check if it's actually an image
            content_type = response.headers.get('content-type', '')
            if not content_type.startswith('image/'):
                return {'status': 'failed', 'reason': 'not_image', 'word': word, 'content_type': content_type}

            # Check if we got a valid image (not a placeholder or error image)
            content_length = response.headers.get('content-length')
            if content_length and int(content_length) < 1000:  # Less than 1KB is probably an error
                return {'status': 'failed', 'reason': 'too_small', 'word': word, 'size': content_length}

            # Save the image
            with open(filepath, 'wb') as f:
                for chunk in response.iter_content(chunk_size=8192):
                    if chunk:
                        f.write(chunk)

            # Verify the saved file is not too small
            if filepath.stat().st_size < 1000:
                filepath.unlink()  # Delete the small file
                return {'status': 'failed', 'reason': 'saved_too_small', 'word': word}

            # Update progress
            with self.lock:
                self.failed_downloaded.add(word_hash)
                self.stats['downloaded'] += 1

            return {'status': 'success', 'word': word, 'file': filename, 'url': bing_url}

        except requests.exceptions.RequestException as e:
            with self.lock:
                self.stats['failed_again'] += 1
            return {'status': 'failed', 'reason': str(e), 'word': word}
        except Exception as e:
            with self.lock:
                self.stats['failed_again'] += 1
            return {'status': 'failed', 'reason': f'Unexpected error: {str(e)}', 'word': word}

    def print_progress(self):
        """Print current progress."""
        elapsed = time.time() - self.stats['start_time']
        processed = self.stats['downloaded'] + self.stats['skipped'] + self.stats['failed_again']

        if processed > 0:
            rate = processed / elapsed if elapsed > 0 else 0
            eta = (self.stats['total_failed'] - processed) / rate if rate > 0 else 0

            print(f"\rProgress: {processed}/{self.stats['total_failed']} "
                  f"(Downloaded: {self.stats['downloaded']}, "
                  f"Skipped: {self.stats['skipped']}, "
                  f"Failed Again: {self.stats['failed_again']}) "
                  f"Rate: {rate:.1f}/s "
                  f"ETA: {eta/60:.1f}m", end='', flush=True)

    def download_failed_images(self, start_index=None, end_index=None, max_workers=4, save_interval=100):
        """Download failed images with threading."""
        print("Analyzing database to find failed images...")
        failed_words = self.get_failed_words_from_db(start_index, end_index)

        if not failed_words:
            print("No failed images found in the specified range.")
            return

        self.stats['total_failed'] = len(failed_words)
        print(f"Found {len(failed_words)} failed images to download using Bing fallback")
        print(f"Using {max_workers} threads")

        if start_index or end_index:
            print(f"Index range: {start_index or 'start'} to {end_index or 'end'}")

        print(f"Images will be saved to: {self.failed_img_folder.absolute()}")
        print(f"Progress will be saved to: {self.failed_progress_file}")
        print("-" * 80)

        # Use ThreadPoolExecutor for concurrent downloads
        with ThreadPoolExecutor(max_workers=max_workers) as executor:
            # Submit all tasks
            future_to_word = {executor.submit(self.download_failed_image, word_data): word_data for word_data in failed_words}

            # Process completed tasks
            processed_count = 0
            for future in as_completed(future_to_word):
                word_data = future_to_word[future]

                try:
                    result = future.result()

                    # Print detailed results for failures and successes
                    if result['status'] == 'failed':
                        print(f"\nFAILED AGAIN: {result['word']} - {result['reason']}")
                    elif result['status'] == 'success':
                        if processed_count < 10:  # Show first few successes
                            print(f"\nSUCCESS: {result['word']} -> {result['file']}")

                except Exception as e:
                    with self.lock:
                        self.stats['failed_again'] += 1
                    print(f"\nException processing {word_data[1]}: {e}")

                processed_count += 1

                # Print progress every 10 items or save progress every save_interval
                if processed_count % 10 == 0:
                    self.print_progress()

                if processed_count % save_interval == 0:
                    self.save_failed_progress()

        # Final progress update
        self.print_progress()
        print()  # New line

        # Save final progress
        self.save_failed_progress()

        # Print final stats
        elapsed = time.time() - self.stats['start_time']
        print("-" * 80)
        print(f"Failed image download completed!")
        print(f"Total time: {elapsed/60:.1f} minutes")
        print(f"Failed images found: {self.stats['total_failed']}")
        print(f"Successfully downloaded: {self.stats['downloaded']}")
        print(f"Skipped (already exists): {self.stats['skipped']}")
        print(f"Failed again: {self.stats['failed_again']}")
        print(f"Success rate: {(self.stats['downloaded'] / self.stats['total_failed'] * 100):.1f}%")
        print(f"Average rate: {self.stats['total_failed']/elapsed:.1f} words/second")


def main():
    parser = argparse.ArgumentParser(description='Download failed Hanzii images using Bing fallback')
    parser.add_argument('--db', default='/Users/gon/Downloads/hanzii_databases/cnru.db',
                        help='Path to the cnru.db database file')
    parser.add_argument('--original-folder', default='img_word',
                        help='Original images folder (default: img_word)')
    parser.add_argument('--failed-folder', default='img_word_failed',
                        help='Failed images output folder (default: img_word_failed)')
    parser.add_argument('--start', type=int,
                        help='Start index (rowid) in the database')
    parser.add_argument('--end', type=int,
                        help='End index (rowid) in the database')
    parser.add_argument('--threads', type=int, default=4,
                        help='Number of download threads (default: 4)')
    parser.add_argument('--progress', default='download_progress.json',
                        help='Original progress file')
    parser.add_argument('--failed-progress', default='failed_download_progress.json',
                        help='Failed downloads progress file')
    parser.add_argument('--save-interval', type=int, default=100,
                        help='Save progress every N downloads (default: 100)')

    args = parser.parse_args()

    # Validate database file
    if not os.path.exists(args.db):
        print(f"Error: Database file not found: {args.db}")
        return 1

    # Validate original progress file
    if not os.path.exists(args.progress):
        print(f"Warning: Original progress file not found: {args.progress}")
        print("This script will try to download all images as 'failed'")

    # Validate index range
    if args.start and args.end and args.start > args.end:
        print("Error: Start index must be less than or equal to end index")
        return 1

    # Create downloader and start
    downloader = HanziiFailedImageDownloader(
        db_path=args.db,
        original_img_folder=args.original_folder,
        failed_img_folder=args.failed_folder,
        progress_file=args.progress,
        failed_progress_file=args.failed_progress
    )

    try:
        downloader.download_failed_images(
            start_index=args.start,
            end_index=args.end,
            max_workers=args.threads,
            save_interval=args.save_interval
        )
    except KeyboardInterrupt:
        print("\n\nDownload interrupted by user. Progress has been saved.")
        downloader.save_failed_progress()
        return 1
    except Exception as e:
        print(f"\nUnexpected error: {e}")
        downloader.save_failed_progress()
        return 1

    return 0


if __name__ == "__main__":
    exit(main())