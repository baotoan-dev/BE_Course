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
        Schema::create('admin_permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_permission_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_role_id')->constrained()->onDelete('cascade');
            $table->unique(['admin_permission_id', 'admin_role_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_permission_role');
    }
};
