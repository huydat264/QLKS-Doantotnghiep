<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm giá trị 'Tạm ứng đã chọn' vào enum loai_thanh_toan
        DB::statement("ALTER TABLE thanhtoan MODIFY COLUMN loai_thanh_toan ENUM('Đặt cọc 30%', 'Thanh toán phần còn lại', 'Tạm ứng đã chọn') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum value
        DB::statement("ALTER TABLE thanhtoan MODIFY COLUMN loai_thanh_toan ENUM('Đặt cọc 30%', 'Thanh toán phần còn lại') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
};
