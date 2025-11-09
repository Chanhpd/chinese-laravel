<?php

namespace Database\Seeders;

use App\Models\Topic;
use App\Models\TopicTranslation;
use Illuminate\Database\Seeder;

class TopicTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $translations = [
            // Topic 1: Greetings
            1 => [
                'vi' => ['name' => 'Chào hỏi cơ bản', 'description' => 'Các cách chào hỏi và giới thiệu bản thân trong tiếng Trung'],
                'zh' => ['name' => '问候语', 'description' => '中文基本问候语和自我介绍'],
                'ja' => ['name' => '基本的な挨拶', 'description' => '中国語での基本的な挨拶と自己紹介'],
                'ko' => ['name' => '기본 인사', 'description' => '중국어 기본 인사 및 자기소개'],
                'es' => ['name' => 'Saludos básicos', 'description' => 'Saludos básicos y presentaciones en chino'],
                'fr' => ['name' => 'Salutations de base', 'description' => 'Salutations de base et présentations en chinois'],
            ],
            // Topic 2: Family
            2 => [
                'vi' => ['name' => 'Gia đình', 'description' => 'Từ vựng về thành viên gia đình và quan hệ họ hàng'],
                'zh' => ['name' => '家庭', 'description' => '家庭成员和亲属关系词汇'],
                'ja' => ['name' => '家族', 'description' => '家族と親戚関係の語彙'],
                'ko' => ['name' => '가족', 'description' => '가족 구성원 및 친척 관계 어휘'],
                'es' => ['name' => 'Familia', 'description' => 'Vocabulario sobre miembros de la familia y parientes'],
                'fr' => ['name' => 'Famille', 'description' => 'Vocabulaire sur les membres de la famille et les parents'],
            ],
            // Topic 3: Numbers
            3 => [
                'vi' => ['name' => 'Số đếm', 'description' => 'Các số đếm từ 0 đến 100 và cách sử dụng'],
                'zh' => ['name' => '数字', 'description' => '从0到100的数字及其用法'],
                'ja' => ['name' => '数字', 'description' => '0から100までの数字とその使い方'],
                'ko' => ['name' => '숫자', 'description' => '0부터 100까지의 숫자와 사용법'],
                'es' => ['name' => 'Números', 'description' => 'Números del 0 al 100 y su uso'],
                'fr' => ['name' => 'Chiffres', 'description' => 'Chiffres de 0 à 100 et leur utilisation'],
            ],
            // Topic 4: Colors
            4 => [
                'vi' => ['name' => 'Màu sắc', 'description' => 'Từ vựng về màu sắc và cách mô tả'],
                'zh' => ['name' => '颜色', 'description' => '颜色词汇和描述方法'],
                'ja' => ['name' => '色', 'description' => '色の語彙と説明方法'],
                'ko' => ['name' => '색상', 'description' => '색상 어휘 및 설명 방법'],
                'es' => ['name' => 'Colores', 'description' => 'Vocabulario de colores y descripciones'],
                'fr' => ['name' => 'Couleurs', 'description' => 'Vocabulaire des couleurs et descriptions'],
            ],
            // Topic 5: Food & Drinks
            5 => [
                'vi' => ['name' => 'Đồ ăn & Thức uống', 'description' => 'Từ vựng về đồ ăn, thức uống và nhà hàng'],
                'zh' => ['name' => '食物和饮料', 'description' => '食物、饮料和餐厅词汇'],
                'ja' => ['name' => '食べ物と飲み物', 'description' => '食べ物、飲み物、レストランの語彙'],
                'ko' => ['name' => '음식과 음료', 'description' => '음식, 음료 및 레스토랑 어휘'],
                'es' => ['name' => 'Comida y bebidas', 'description' => 'Vocabulario de comida, bebidas y restaurantes'],
                'fr' => ['name' => 'Nourriture et boissons', 'description' => 'Vocabulaire de nourriture, boissons et restaurants'],
            ],
            // Topic 6: Shopping
            6 => [
                'vi' => ['name' => 'Mua sắm', 'description' => 'Từ vựng liên quan đến mua sắm và giá cả'],
                'zh' => ['name' => '购物', 'description' => '购物和价格相关词汇'],
                'ja' => ['name' => '買い物', 'description' => 'ショッピングと価格に関する語彙'],
                'ko' => ['name' => '쇼핑', 'description' => '쇼핑 및 가격 관련 어휘'],
                'es' => ['name' => 'Compras', 'description' => 'Vocabulario relacionado con compras y precios'],
                'fr' => ['name' => 'Shopping', 'description' => 'Vocabulaire lié au shopping et aux prix'],
            ],
            // Topic 7: Travel
            7 => [
                'vi' => ['name' => 'Du lịch', 'description' => 'Từ vựng hữu ích cho du lịch và di chuyển'],
                'zh' => ['name' => '旅游', 'description' => '旅行和交通有用词汇'],
                'ja' => ['name' => '旅行', 'description' => '旅行と移動に役立つ語彙'],
                'ko' => ['name' => '여행', 'description' => '여행 및 이동에 유용한 어휘'],
                'es' => ['name' => 'Viajes', 'description' => 'Vocabulario útil para viajes y transporte'],
                'fr' => ['name' => 'Voyages', 'description' => 'Vocabulaire utile pour les voyages et les déplacements'],
            ],
            // Topic 8: Time
            8 => [
                'vi' => ['name' => 'Thời gian', 'description' => 'Từ vựng về thời gian, ngày tháng, giờ phút'],
                'zh' => ['name' => '时间', 'description' => '时间、日期、小时和分钟词汇'],
                'ja' => ['name' => '時間', 'description' => '時間、日付、時刻に関する語彙'],
                'ko' => ['name' => '시간', 'description' => '시간, 날짜, 시각 관련 어휘'],
                'es' => ['name' => 'Tiempo', 'description' => 'Vocabulario sobre tiempo, fechas y horas'],
                'fr' => ['name' => 'Temps', 'description' => 'Vocabulaire sur le temps, les dates et les heures'],
            ],
        ];

        foreach ($translations as $topicId => $langs) {
            foreach ($langs as $langCode => $data) {
                TopicTranslation::create([
                    'topic_id' => $topicId,
                    'language_code' => $langCode,
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);
            }
        }
    }
}
