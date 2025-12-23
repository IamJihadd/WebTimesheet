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
            // 1. Ganti nama kolom 'divisi' menjadi 'department'
            // Pastikan Anda sudah punya kolom 'divisi' sebelumnya
            if (Schema::hasColumn('users', 'divisi')) {
                $table->renameColumn('divisi', 'department');
            }

            // 2. Tambahkan kolom 'level_grade' baru
            // Kita taruh setelah kolom 'role' agar rapi
            if (!Schema::hasColumn('users', 'level_grade')) {
                $table->string('level_grade')->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan seperti semula jika rollback
            if (Schema::hasColumn('users', 'department')) {
                $table->renameColumn('department', 'divisi');
            }

            if (Schema::hasColumn('users', 'level_grade')) {
                $table->dropColumn('level_grade');
            }
        });
    }
};