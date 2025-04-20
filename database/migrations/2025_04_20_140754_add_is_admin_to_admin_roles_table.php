<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAdminToAdminRolesTable extends Migration
{
    public function up()
    {
        Schema::table('admin_roles', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false); // Thêm cột is_admin
        });
    }

    public function down()
    {
        Schema::table('admin_roles', function (Blueprint $table) {
            $table->dropColumn('is_admin'); // Xóa cột is_admin nếu cần rollback
        });
    }
}
