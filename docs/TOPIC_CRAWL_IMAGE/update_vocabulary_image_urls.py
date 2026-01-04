#!/usr/bin/env python3
"""
Update Vocabulary Image URLs

Generates Bing image URLs for vocabularies and updates them in the database.
Uses the Chinese word (simplified or word field) to search for images.
"""

import mysql.connector
import hashlib
import urllib.parse
import argparse
import sys

class VocabularyImageURLUpdater:
    def __init__(self, db_config):
        self.db_config = db_config

    def generate_bing_image_url(self, query, width=720, height=406):
        """Generate Bing image URL."""
        encoded_query = urllib.parse.quote(query)
        url = f"https://th.bing.com/th?q={encoded_query}&w={width}&h={height}&c=7&rs=1&p=0&o=5&dpr=2&pid=1.7&mkt=zh-WW&cc=VN&setlang=zh-CN&adlt=moderate&t=1"
        return url

    def get_vocabularies_from_db(self, batch_size=1000, offset=0, limit=None):
        """Fetch vocabularies from database in batches."""
        conn = mysql.connector.connect(**self.db_config)
        cursor = conn.cursor(dictionary=True)

        if limit:
            query = """
            SELECT id, word, simplified, traditional, pinyin, meaning
            FROM vocabularies 
            ORDER BY id
            LIMIT %s OFFSET %s
            """
            cursor.execute(query, (min(batch_size, limit - offset), offset))
        else:
            query = """
            SELECT id, word, simplified, traditional, pinyin, meaning
            FROM vocabularies 
            ORDER BY id
            LIMIT %s OFFSET %s
            """
            cursor.execute(query, (batch_size, offset))
        
        vocabularies = cursor.fetchall()
        
        cursor.close()
        conn.close()

        return vocabularies

    def update_vocabulary_image_url(self, vocab_id, new_url):
        """Update vocabulary's image_url in database."""
        conn = mysql.connector.connect(**self.db_config)
        cursor = conn.cursor()

        query = "UPDATE vocabularies SET image_url = %s WHERE id = %s"
        cursor.execute(query, (new_url, vocab_id))
        
        conn.commit()
        cursor.close()
        conn.close()

    def get_total_count(self):
        """Get total vocabulary count."""
        conn = mysql.connector.connect(**self.db_config)
        cursor = conn.cursor()
        
        cursor.execute("SELECT COUNT(*) FROM vocabularies")
        total = cursor.fetchone()[0]
        
        cursor.close()
        conn.close()
        
        return total

    def update_all_vocabularies(self, batch_size=1000, limit=None):
        """Update image URLs for all vocabularies."""
        print("Fetching vocabulary count from database...")
        total_count = self.get_total_count()
        
        if limit:
            total_count = min(total_count, limit)
        
        print(f"Total vocabularies to process: {total_count}")
        print("=" * 80)
        print()

        updated_count = 0
        skipped_count = 0
        failed_count = 0
        offset = 0

        while offset < total_count:
            # Fetch batch
            print(f"Processing batch {offset + 1} to {min(offset + batch_size, total_count)}...")
            vocabularies = self.get_vocabularies_from_db(batch_size, offset, limit)
            
            if not vocabularies:
                break

            for vocab in vocabularies:
                vocab_id = vocab['id']
                word = vocab['word']
                simplified = vocab['simplified']
                traditional = vocab['traditional']
                
                # Use simplified if available, otherwise use word
                search_word = simplified if simplified else word
                
                if not search_word or not search_word.strip():
                    print(f"[SKIP] ID {vocab_id}: No valid word found")
                    skipped_count += 1
                    continue

                # Generate Bing URL - just the word without extra context
                new_url = self.generate_bing_image_url(search_word)

                # Update database
                try:
                    self.update_vocabulary_image_url(vocab_id, new_url)
                    
                    if updated_count < 20:  # Show first 20 updates
                        print(f"[UPDATE] ID {vocab_id}: {search_word} ({vocab['pinyin'] or 'no pinyin'})")
                        if updated_count == 19:
                            print("... (showing first 20, continuing silently)")
                    
                    updated_count += 1
                    
                    # Show progress every 100 items
                    if updated_count % 100 == 0:
                        print(f"Progress: {updated_count}/{total_count} ({updated_count * 100 / total_count:.1f}%)")
                        
                except Exception as e:
                    print(f"[ERROR] ID {vocab_id}: {search_word} - {e}")
                    failed_count += 1

            offset += batch_size

        # Summary
        print()
        print("=" * 80)
        print(f"Update completed!")
        print(f"Total vocabularies: {total_count}")
        print(f"Updated: {updated_count}")
        print(f"Skipped: {skipped_count}")
        print(f"Failed: {failed_count}")
        print("=" * 80)


def main():
    parser = argparse.ArgumentParser(description='Update vocabulary image URLs with Bing URLs')
    parser.add_argument('--host', default='localhost', help='MySQL host')
    parser.add_argument('--user', default='root', help='MySQL user')
    parser.add_argument('--password', required=True, help='MySQL password')
    parser.add_argument('--database', required=True, help='MySQL database name')
    parser.add_argument('--port', type=int, default=3306, help='MySQL port')
    parser.add_argument('--batch-size', type=int, default=1000, help='Batch size for processing')
    parser.add_argument('--limit', type=int, help='Limit number of vocabularies to process (for testing)')

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
    updater = VocabularyImageURLUpdater(db_config=db_config)

    try:
        updater.update_all_vocabularies(batch_size=args.batch_size, limit=args.limit)
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
