<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom manager_id menyimpan user_id milik atasan
            // nullable: karena User paling atas (CEO) tidak punya bos
            $table->string('manager_id')->nullable()->after('user_id');

            // Foreign key ke tabel users itu sendiri (Self Reference)
            // onUpdate cascade: Jika ID atasan berubah, data bawahan ikut berubah
            // onDelete set null: Jika atasan dihapus, bawahan jadi tanpa bos (bukan ikut terhapus)
            $table->foreign('manager_id')
                  ->references('user_id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};