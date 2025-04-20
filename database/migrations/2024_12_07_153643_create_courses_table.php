<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); // Khóa chính tự động (ID)
            $table->string('title'); // Tên khóa học
            $table->text('description')->nullable(); // Mô tả khóa học
            $table->string('thumbnail')->nullable(); // Ảnh đại diện
            $table->string('author')->nullable(); // Tác giả
            $table->tinyInteger('status')->default(1); // Trạng thái khóa học
            $table->softDeletes(); // Thêm cột deleted_at
            $table->timestamps(); // Thêm created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses'); // Xóa bảng courses
    }
};
