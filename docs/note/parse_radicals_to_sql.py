import json
import os

def escape_sql_string(value):
    """Escape special characters in SQL strings"""
    if value is None or value == '':
        return 'NULL'
    value = str(value)
    # Escape single quotes and backslashes
    value = value.replace('\\', '\\\\').replace("'", "\\'")
    return f"'{value}'"

def parse_radicals_to_sql(input_file='HSK_Radicals.json', output_file='radicals_data.sql'):
    """Parse HSK_Radicals.json and generate SQL INSERT statements"""
    
    if not os.path.exists(input_file):
        print(f"Error: File {input_file} not found!")
        return
    
    print(f"Processing {input_file}...")
    
    with open(input_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    # Check for null hanzi entries first
    print("\nChecking for null/empty hanzi entries...")
    null_entries = []
    for idx, item in enumerate(data):
        if not item.get('hanzi') or item.get('hanzi') == '':
            null_entries.append({
                'index': idx,
                'line_estimate': idx + 2,  # Approximate line in JSON
                'data': item
            })
    
    if null_entries:
        print(f"\n⚠️  WARNING: Found {len(null_entries)} entries with null/empty hanzi:\n")
        for entry in null_entries:
            print(f"Entry Index: {entry['index']}")
            print(f"Approximate JSON line: ~{entry['line_estimate']}")
            print(f"  hanzi: {entry['data'].get('hanzi')}")
            print(f"  traditional: {entry['data'].get('traditional')}")
            print(f"  pinyin: {entry['data'].get('pinyin')}")
            print(f"  meaning: {entry['data'].get('meaning')}")
            print(f"  meaning_vi: {entry['data'].get('meaning_vi')}")
            print(f"  hsk_level: {entry['data'].get('hsk_level')}")
            print(f"  frequency_rank: {entry['data'].get('frequency_rank')}")
            print("-" * 70)
        
        response = input("\n❓ Continue generating SQL anyway? These entries will cause MySQL errors. (y/n): ")
        if response.lower() != 'y':
            print("Aborted. Please fix the null entries first.")
            return
    
    with open(output_file, 'w', encoding='utf-8') as f:
        # Note: level table should already exist from word script
        # We'll just reference it here
        
        # Write radical table creation SQL
        f.write("-- Create radical table (assumes level table already exists)\n")
        f.write("DROP TABLE IF EXISTS `radical`;\n\n")
        f.write("CREATE TABLE `radical` (\n")
        f.write("  `id` INT AUTO_INCREMENT PRIMARY KEY,\n")
        f.write("  `hanzi` VARCHAR(10) NOT NULL,\n")
        f.write("  `traditional` VARCHAR(10),\n")
        f.write("  `pinyin` VARCHAR(50),\n")
        f.write("  `radical` VARCHAR(50),\n")
        f.write("  `stroke_count` INT,\n")
        f.write("  `frequency_rank` INT,\n")
        f.write("  `general_standard` VARCHAR(20),\n")
        f.write("  `level_id` INT,\n")
        f.write("  `meaning` TEXT,\n")
        f.write("  `meaning_vi` TEXT,\n")
        f.write("  `meaning_cn` TEXT,\n")
        f.write("  `meaning_en` TEXT,\n")
        f.write("  `meaning_jp` TEXT,\n")
        f.write("  `meaning_kr` TEXT,\n")
        f.write("  `meaning_th` TEXT,\n")
        f.write("  `meaning_de` TEXT,\n")
        f.write("  `meaning_fr` TEXT,\n")
        f.write("  `meaning_es` TEXT,\n")
        f.write("  `meaning_it` TEXT,\n")
        f.write("  `meaning_br` TEXT,\n")
        f.write("  `meaning_tr` TEXT,\n")
        f.write("  `is_favorite` TINYINT(1) DEFAULT 0,\n")
        f.write("  INDEX `idx_hanzi` (`hanzi`),\n")
        f.write("  INDEX `idx_level_id` (`level_id`),\n")
        f.write("  INDEX `idx_frequency_rank` (`frequency_rank`),\n")
        f.write("  FOREIGN KEY (`level_id`) REFERENCES `level`(`id`) ON DELETE SET NULL\n")
        f.write(") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n")
        
        f.write("-- Insert radical data\n")
        
        # Process each character entry
        for item in data:
            hanzi = escape_sql_string(item.get('hanzi', ''))
            traditional = escape_sql_string(item.get('traditional', ''))
            pinyin = escape_sql_string(item.get('pinyin', ''))
            radical = escape_sql_string(item.get('radical', ''))
            
            # Convert stroke_count to integer or NULL
            stroke_count_val = item.get('stroke_count', '')
            if stroke_count_val and stroke_count_val != '':
                try:
                    stroke_count = int(stroke_count_val)
                except:
                    stroke_count = 'NULL'
            else:
                stroke_count = 'NULL'
            
            # Convert frequency_rank to integer or NULL
            frequency_rank_val = item.get('frequency_rank', '')
            if frequency_rank_val and frequency_rank_val != '':
                try:
                    frequency_rank = int(frequency_rank_val)
                except:
                    frequency_rank = 'NULL'
            else:
                frequency_rank = 'NULL'
            
            general_standard = escape_sql_string(item.get('general_standard', ''))
            
            # Convert hsk_level to level_id
            # Map HSK level to level table
            hsk_level_val = item.get('hsk_level', '')
            if hsk_level_val and hsk_level_val != '':
                try:
                    hsk_num = int(hsk_level_val)
                    # HSK levels in level table: 1-6 are 1-6, 7-9 is level 7
                    if hsk_num >= 7:
                        level_id = 7  # HSK 7-9
                    else:
                        level_id = hsk_num  # HSK 1-6
                except:
                    level_id = 'NULL'
            else:
                level_id = 'NULL'
            
            meaning = escape_sql_string(item.get('meaning', ''))
            meaning_vi = escape_sql_string(item.get('meaning_vi', ''))
            meaning_cn = escape_sql_string(item.get('meaning_cn', ''))
            meaning_en = escape_sql_string(item.get('meaning', ''))  # Using 'meaning' as English
            meaning_jp = escape_sql_string(item.get('meaning_jp', ''))
            meaning_kr = escape_sql_string(item.get('meaning_kr', ''))
            meaning_th = escape_sql_string(item.get('meaning_th', ''))
            meaning_de = escape_sql_string(item.get('meaning_de', ''))
            meaning_fr = escape_sql_string(item.get('meaning_fr', ''))
            meaning_es = escape_sql_string(item.get('meaning_es', ''))
            meaning_it = escape_sql_string(item.get('meaning_it', ''))
            meaning_br = escape_sql_string(item.get('meaning_br', ''))
            meaning_tr = escape_sql_string(item.get('meaning_tr', ''))
            
            is_favorite = int(item.get('isFavorite', 0))
            
            sql = f"INSERT INTO `radical` (`hanzi`, `traditional`, `pinyin`, `radical`, `stroke_count`, `frequency_rank`, `general_standard`, `level_id`, `meaning`, `meaning_vi`, `meaning_cn`, `meaning_en`, `meaning_jp`, `meaning_kr`, `meaning_th`, `meaning_de`, `meaning_fr`, `meaning_es`, `meaning_it`, `meaning_br`, `meaning_tr`, `is_favorite`) VALUES ({hanzi}, {traditional}, {pinyin}, {radical}, {stroke_count}, {frequency_rank}, {general_standard}, {level_id}, {meaning}, {meaning_vi}, {meaning_cn}, {meaning_en}, {meaning_jp}, {meaning_kr}, {meaning_th}, {meaning_de}, {meaning_fr}, {meaning_es}, {meaning_it}, {meaning_br}, {meaning_tr}, {is_favorite});\n"
            
            f.write(sql)
        
        f.write("\n-- End of SQL file\n")
    
    print(f"\n✓ Processed {len(data)} characters from {input_file}")
    print(f"✓ SQL file created successfully: {output_file}")
    print(f"\nTo import into MySQL, use:")
    print(f"  mysql -u username -p database_name < {output_file}")

if __name__ == "__main__":
    print("=" * 60)
    print("HSK Radicals JSON to SQL Converter")
    print("=" * 60)
    print()
    
    parse_radicals_to_sql()
    
    print("\nDone!")
