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
        // === PERBAIKAN TABEL USERS ===
        Schema::create('users', function (Blueprint $table) {
            // HAPUS $table->id();
            
            // JADIKAN 'user_id' (string) sebagai Kunci Utama (Primary Key)
            $table->string('user_id')->primary(); 
            
            $table->string('name');
            $table->enum('role', ['manager', 'karyawan','admin']);
            $table->string('divisi')->nullable();
            $table->string('lokasi_kerja')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->string('password');

            // Saya tambahkan 'email' di sini. 
            // Ini PENTING untuk fitur "Lupa Password" (password resets)
            // Dan sepertinya file User.php Anda juga sudah memilikinya.
            $table->string('email')->unique()->nullable(); 

            $table->rememberToken();
            $table->timestamps();
        });

        // === PERBAIKAN TABEL PASSWORD_RESET_TOKENS ===
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Tabel ini menggunakan 'email' sebagai acuan
            $table->string('email')->primary(); 
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            // Tambahkan relasi ke 'email' di tabel users
            $table->foreign('email')
                  ->references('email')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // === PERBAIKAN TABEL SESSIONS ===
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            
            // Ubah 'foreignId' (angka) menjadi 'string'
            $table->string('user_id')->nullable()->index(); 
            
            // Buat relasi ke 'users.user_id' (string)
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};