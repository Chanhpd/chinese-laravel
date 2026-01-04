#!/usr/bin/env python3
"""
Update Topic Image URLs

Simply generates Bing image URLs for topics and updates them in the database.
No actual image downloads - just URL generation and database updates.
"""

import mysql.connector
import hashlib
import urllib.parse
import argparse
import sys

class TopicImageURLUpdater:
    def __init__(self, db_config):
        self.db_config = db_config

    def generate_bing_image_url(self, query, width=720, height=406):
        """Generate Bing image URL."""
        encoded_query = urllib.parse.quote(query)
        url = f"https://th.bing.com/th?q={encoded_query}&w={width}&h={height}&c=7&rs=1&p=0&o=5&dpr=2&pid=1.7&mkt=zh-WW&cc=VN&setlang=zh-CN&adlt=moderate&t=1"
        return url

    def get_topics_from_db(self):
        """Fetch all active topics from database."""
        conn = mysql.connector.connect(**self.db_config)
        cursor = conn.cursor(dictionary=True)

        query = """
        SELECT id, name, name_zh, image_url 
        FROM topics 
        WHERE is_active = 1
        ORDER BY id
        """
        
        cursor.execute(query)
        topics = cursor.fetchall()
        
        cursor.close()
        conn.close()

        return topics

    def update_topic_image_url(self, topic_id, new_url):
        """Update topic's image_url in database."""
        conn = mysql.connector.connect(**self.db_config)
        cursor = conn.cursor()

        query = "UPDATE topics SET image_url = %s WHERE id = %s"
        cursor.execute(query, (new_url, topic_id))
        
        conn.commit()
        cursor.close()
        conn.close()

    def update_all_topics(self):
        """Update image URLs for all topics."""
        print("Fetching topics from database...")
        topics = self.get_topics_from_db()

        if not topics:
            print("No topics found in the database.")
            return

        print(f"Found {len(topics)} active topics")
        print("=" * 80)
        print()

        updated_count = 0
        skipped_count = 0

        for topic in topics:
            topic_id = topic['id']
            topic_name = topic['name']
            topic_name_zh = topic['name_zh']
            old_url = topic['image_url']

            # Generate search query - use Chinese name if available, otherwise English
            search_query = topic_name_zh if topic_name_zh else topic_name
            search_query += " 学习"  # Add "learning" in Chinese

            # Generate new Bing URL
            new_url = self.generate_bing_image_url(search_query)

            # Check if URL is different
            if old_url == new_url:
                print(f"[SKIP] ID {topic_id}: {topic_name} - URL unchanged")
                skipped_count += 1
                continue

            # Update database
            try:
                self.update_topic_image_url(topic_id, new_url)
                print(f"[UPDATE] ID {topic_id}: {topic_name}")
                print(f"  Old: {old_url}")
                print(f"  New: {new_url}")
                print()
                updated_count += 1
            except Exception as e:
                print(f"[ERROR] ID {topic_id}: {topic_name} - {e}")
                print()

        # Summary
        print("=" * 80)
        print(f"Update completed!")
        print(f"Total topics: {len(topics)}")
        print(f"Updated: {updated_count}")
        print(f"Skipped: {skipped_count}")
        print("=" * 80)


def main():
    parser = argparse.ArgumentParser(description='Update topic image URLs with Bing URLs')
    parser.add_argument('--host', default='localhost', help='MySQL host')
    parser.add_argument('--user', default='root', help='MySQL user')
    parser.add_argument('--password', required=True, help='MySQL password')
    parser.add_argument('--database', required=True, help='MySQL database name')
    parser.add_argument('--port', type=int, default=3306, help='MySQL port')

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
        print("Testing database connection...")
        conn = mysql.connector.connect(**db_config)
        conn.close()
        print("✓ Database connection successful!")
        print()
    except Exception as e:
        print(f"✗ Error: Could not connect to database: {e}")
        return 1

    # Create updater and run
    updater = TopicImageURLUpdater(db_config=db_config)

    try:
        updater.update_all_topics()
    except KeyboardInterrupt:
        print("\n\nUpdate interrupted by user.")
        return 1
    except Exception as e:
        print(f"\nUnexpected error: {e}")
        import traceback
        traceback.print_exc()
        return 1

    return 0


if __name__ == "__main__":
    sys.exit(main())
