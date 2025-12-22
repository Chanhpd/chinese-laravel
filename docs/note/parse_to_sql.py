import json
import os
from pathlib import Path

def escape_sql_string(value):
    """Escape special characters in SQL strings"""
    if value is None:
        return 'NULL'
    value = str(value)
    # Escape single quotes and backslashes
    value = value.replace('\\', '\\\\').replace("'", "\\'")
    return f"'{value}'"

def get_level_info():
    """Define level mappings for HSK and TOCFL"""
    levels = {
        'hsk': {
            'hsk_1': {'level': 1, 'name': 'HSK 1'},
            'hsk_2': {'level': 2, 'name': 'HSK 2'},
            'hsk_3': {'level': 3, 'name': 'HSK 3'},
            'hsk_4': {'level': 4, 'name': 'HSK 4'},
            'hsk_5': {'level': 5, 'name': 'HSK 5'},
            'hsk_6': {'level': 6, 'name': 'HSK 6'},
            'hsk_7-9': {'level': 7, 'name': 'HSK 7-9'}
        },
        'tocfl': {
            'tocfl_1': {'level': 1, 'name': 'TOCFL 1'},
            'tocfl_2': {'level': 2, 'name': 'TOCFL 2'},
            'tocfl_3': {'level': 3, 'name': 'TOCFL 3'},
            'tocfl_4': {'level': 4, 'name': 'TOCFL 4'},
            'tocfl_5-6': {'level': 5, 'name': 'TOCFL 5-6'}
        }
    }
    return levels

def parse_json_to_sql(output_file='vocabulary_data.sql'):
    """Parse all JSON files and generate SQL INSERT statements"""
    
    levels = get_level_info()
    
    with open(output_file, 'w', encoding='utf-8') as f:
        # Write level table creation SQL
        f.write("-- Create level table\n")
        f.write("DROP TABLE IF EXISTS `level`;\n\n")
        f.write("CREATE TABLE `level` (\n")
        f.write("  `id` INT AUTO_INCREMENT PRIMARY KEY,\n")
        f.write("  `test_type` VARCHAR(20) NOT NULL,\n")
        f.write("  `level_number` INT NOT NULL,\n")
        f.write("  `level_name` VARCHAR(50) NOT NULL,\n")
        f.write("  UNIQUE KEY `unique_test_level` (`test_type`, `level_number`),\n")
        f.write("  INDEX `idx_test_type` (`test_type`)\n")
        f.write(") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n")
        
        # Insert level data
        f.write("-- Insert level data\n")
        level_id_map = {}
        level_id = 1
        for test_type in ['hsk', 'tocfl']:
            for level_key, level_info in levels[test_type].items():
                test_type_upper = test_type.upper()
                level_num = level_info['level']
                level_name = level_info['name']
                
                f.write(f"INSERT INTO `level` (`id`, `test_type`, `level_number`, `level_name`) VALUES ({level_id}, '{test_type_upper}', {level_num}, '{level_name}');\n")
                
                # Store mapping for later use
                level_id_map[f"{test_type}_{level_num}"] = level_id
                level_id += 1
        
        f.write("\n")
        
        # Write word table creation SQL
        f.write("-- Create word table\n")
        f.write("DROP TABLE IF EXISTS `word`;\n\n")
        f.write("CREATE TABLE `word` (\n")
        f.write("  `id` INT AUTO_INCREMENT PRIMARY KEY,\n")
        f.write("  `word` VARCHAR(50) NOT NULL,\n")
        f.write("  `pinyin` VARCHAR(100),\n")
        f.write("  `meaning_vi` TEXT,\n")
        f.write("  `meaning_en` TEXT,\n")
        f.write("  `meaning_ru` TEXT,\n")
        f.write("  `meaning_th` TEXT,\n")
        f.write("  `meaning_ms` TEXT,\n")
        f.write("  `meaning_ko` TEXT,\n")
        f.write("  `meaning_ja` TEXT,\n")
        f.write("  `meaning_id` TEXT,\n")
        f.write("  `level_id` INT NOT NULL,\n")
        f.write("  INDEX `idx_level_id` (`level_id`),\n")
        f.write("  INDEX `idx_word` (`word`),\n")
        f.write("  FOREIGN KEY (`level_id`) REFERENCES `level`(`id`) ON DELETE CASCADE\n")
        f.write(") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n")
        
        f.write("-- Insert word data\n")
        
        # Process each test type
        for test_type in ['hsk', 'tocfl']:
            for level_key, level_info in levels[test_type].items():
                json_file = f"{level_key}.json"
                
                if not os.path.exists(json_file):
                    print(f"Warning: File {json_file} not found, skipping...")
                    continue
                
                print(f"Processing {json_file}...")
                
                try:
                    with open(json_file, 'r', encoding='utf-8') as jf:
                        data = json.load(jf)
                    
                    for item in data:
                        word = escape_sql_string(item.get('w', ''))
                        pinyin = escape_sql_string(item.get('p', ''))
                        meaning_vi = escape_sql_string(item.get('m', ''))
                        meaning_en = escape_sql_string(item.get('m_en', ''))
                        meaning_ru = escape_sql_string(item.get('m_ru', ''))
                        meaning_th = escape_sql_string(item.get('m_th', ''))
                        meaning_ms = escape_sql_string(item.get('m_ms', ''))
                        meaning_ko = escape_sql_string(item.get('m_ko', ''))
                        meaning_ja = escape_sql_string(item.get('m_ja', ''))
                        meaning_id = escape_sql_string(item.get('m_id', ''))
                        
                        level_num = level_info['level']
                        level_id_key = f"{test_type}_{level_num}"
                        level_id_val = level_id_map[level_id_key]
                        
                        sql = f"INSERT INTO `word` (`word`, `pinyin`, `meaning_vi`, `meaning_en`, `meaning_ru`, `meaning_th`, `meaning_ms`, `meaning_ko`, `meaning_ja`, `meaning_id`, `level_id`) VALUES ({word}, {pinyin}, {meaning_vi}, {meaning_en}, {meaning_ru}, {meaning_th}, {meaning_ms}, {meaning_ko}, {meaning_ja}, {meaning_id}, {level_id_val});\n"
                        
                        f.write(sql)
                    
                    print(f"  ✓ Processed {len(data)} words from {json_file}")
                    
                except Exception as e:
                    print(f"Error processing {json_file}: {e}")
        
        f.write("\n-- End of SQL file\n")
    
    print(f"\n✓ SQL file created successfully: {output_file}")
    print(f"\nTo import into MySQL, use:")
    print(f"  mysql -u username -p database_name < {output_file}")

if __name__ == "__main__":
    print("=" * 60)
    print("Chinese Vocabulary JSON to SQL Converter")
    print("=" * 60)
    print()
    
    parse_json_to_sql()
    
    print("\nDone!")
