<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        $titles = [
            'Cách sử dụng Elasticsearch trong Laravel',
            'Hướng dẫn tạo API tìm kiếm nâng cao',
            'Tối ưu hóa hiệu suất ứng dụng PHP',
            'Giới thiệu Laravel 11 và các tính năng mới',
            'Xây dựng hệ thống blog thông minh với AI',
        ];

        $paragraphs = [
            'Elasticsearch là công cụ tìm kiếm mạnh mẽ được sử dụng trong nhiều hệ thống lớn. Bài viết này sẽ hướng dẫn bạn cách tích hợp nó với Laravel.',
            'Laravel là framework PHP phổ biến, giúp việc phát triển ứng dụng web trở nên dễ dàng và linh hoạt hơn.',
            'Khi kết hợp Elasticsearch và Laravel, bạn có thể tạo ra một hệ thống tìm kiếm nhanh và chính xác.',
            'Dữ liệu được index trong Elasticsearch có thể được tìm kiếm theo nhiều tiêu chí khác nhau, mang lại trải nghiệm người dùng tốt hơn.',
        ];
        for ($i = 0; $i < 50; $i++) {
            Product::create([
                'name' => $faker->randomElement($titles),
                'brand' => $faker->randomElement($titles),
                'color' => $faker->colorName(),
                'price' => $faker->numberBetween(10000000, 100000000),
                'description' => $faker->randomElement($paragraphs),
            ]);
        }
        Product::create([
            'name' => 'Thể thao',
            'brand' => 'Ronaldo',
            'color' => 'trắng',
            'price' => 9999,
            'description' => 'Trong phần cuối cuộc trò chuyện với MC Piers Morgan, Cristiano Ronaldo nói về lý do không dự đám tang của Diogo Jota, bất bình vì Quả Bóng Vàng không ghi nhận giải Saudi Pro League, quan điểm về cầu thủ hay nhất lịch sử và so sánh vẻ đẹp trai với David Beckham.',
        ]);

        echo "✅ Đã tạo 50 bài viết tiếng Việt và index vào Database!\n";
    }
}
