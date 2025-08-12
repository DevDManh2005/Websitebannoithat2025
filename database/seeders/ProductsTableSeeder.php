<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('products')->delete();
        
        \DB::table('products')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Giường Ngủ Gỗ Tràm BRICK ETERNAHOME 601 Nhiều Kích Thước',
                'slug' => 'giuong-ngu-go-tram-brick-eternahome-601-nhieu-kich-thuoc',
                'brand_id' => 1,
                'supplier_id' => 1,
                'description' => 'Đặc điểm nổi bật
Kiểu dáng hiện đại: Phong cách V‑LINE đậm nét Việt Nam pha chất hiện đại năng động, thiết kế tối giản nhưng tinh tế 
Đầu giường thiết kế cong êm ái: Hai bản gỗ lớn, uốn cong ôm sát tạo cảm giác dễ chịu khi dựa lưng thư giãn 
Khung vạt giường vững chắc: Hệ khung kim loại khít nhau, kết hợp với phản nguyên tấm tăng khả năng chịu lực, sử dụng với nệm dày đến 30 cm 
Tránh mạt nệm trượt: Kích thước được tính toán kỹ, ôm sát nệm để giữ cố định, sạch sẽ dễ sử dụng 
Chiều cao hợp lý: Phù hợp robot hút bụi và việc vệ sinh dưới gầm dễ dàng 
Ưu điểm nổi bật tổng thể
Thiết kế hiện đại, sang trọng và tối giản

Chất liệu gỗ thật (tràm + veneer sồi, cao su, plywood), đảm bảo bền và an toàn

Khung đỡ chắc chắn, thích hợp nệm dày, chịu lực tốt

Dễ dàng lau dọn, không sinh mối mọt, an toàn sức khỏe

Kích thước linh hoạt phù hợp nhiều nhu cầu sử dụng',
                'is_active' => true,
                'is_featured' => true,
                'label' => 'Sale , Hot',
                'created_at' => '2025-08-06 12:55:07',
                'updated_at' => '2025-08-06 12:55:07',
                'total_purchased' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Bàn Ăn Gỗ Cao Su Tự Nhiên Home Garden 601 90cm',
                'slug' => 'ban-an-go-cao-su-tu-nhien-home-garden-601-90cm',
                'brand_id' => 6,
                'supplier_id' => 6,
                'description' => 'Bàn Ăn Home Garden 601 90cm làm từ gỗ cao su tự nhiên, chắc chắn, chống cong vênh, mối mọt. Kích thước nhỏ gọn, phù hợp 2-4 người, lý tưởng cho căn hộ nhỏ. Thiết kế hiện đại với chân chữ X, cạnh bo tròn an toàn, màu vàng nhạt ấm cúng. Dễ lau chùi, có logo MOHO chống hàng giả, miễn phí giao hàng.',
                'is_active' => true,
                'is_featured' => false,
                'label' => 'Giảm Giá',
                'created_at' => '2025-08-06 21:39:04',
                'updated_at' => '2025-08-06 21:39:04',
                'total_purchased' => 0,
            ),
            2 => 
            array (
                'id' => 4,
                'name' => 'Bàn Làm Việc Gỗ ARCHITECTURE 601 Màu Nâu',
                'slug' => 'ban-lam-viec-go-architecture-601-mau-nau',
                'brand_id' => 4,
                'supplier_id' => 4,
                'description' => 'Bàn làm việc gọn gàng, kích thước vừa phải, phù hợp không gian nhỏ. Gỗ cao su tự nhiên, màu nâu trầm ấm, chống cong vênh, mối mọt. Thiết kế tối giản, hiện đại, thanh ngang tăng độ chắc chắn. Chiều cao chuẩn người Việt, hỗ trợ tư thế ngồi thoải mái, giảm mỏi lưng. Dễ lau chùi, miễn phí giao hàng.',
                'is_active' => true,
                'is_featured' => true,
                'label' => 'Giảm Giá',
                'created_at' => '2025-08-06 22:41:15',
                'updated_at' => '2025-08-06 22:41:15',
                'total_purchased' => 0,
            ),
            3 => 
            array (
                'id' => 3,
                'name' => 'Ghế Sofa 1m6 Home Build Ver.2',
                'slug' => 'ghe-sofa-1m6-home-build-ver2',
                'brand_id' => 2,
                'supplier_id' => 2,
                'description' => 'Phong cách Bắc Âu tối giản, lý tưởng cho phòng khách nhỏ. Gỗ cao su tự nhiên, màu Dark Grey Barn Wood hoặc Light Cabin Wood, bọc vải melamine xám Greige chống trầy, chống nước. Thiết kế không tay vịn tiết kiệm không gian, bền bỉ, dễ vệ sinh. Miễn phí giao hàng, bảo hành 2 năm.',
                'is_active' => true,
                'is_featured' => true,
                'label' => 'hot',
                'created_at' => '2025-08-06 22:22:12',
                'updated_at' => '2025-08-09 21:55:02',
                'total_purchased' => 0,
            ),
        ));
        
        
    }
}