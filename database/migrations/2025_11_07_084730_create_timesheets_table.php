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
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();

            // --- PERBAIKAN 'user_id' ---
            // Ubah dari foreignId() ke string()
            $table->string('user_id'); 
            $table->foreign('user_id')
                  ->references('user_id') // Merujuk ke users.user_id (string)
                  ->on('users')
                  ->onDelete('cascade');
            // ---------------------------

            $table->date('week_start'); // Senin
            $table->date('week_end');   // Minggu
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();

            // --- 'approved_by' ---
            $table->string('approved_by')->nullable(); // Tipe string
            $table->foreign('approved_by')
                  ->references('user_id') // Merujuk ke users.user_id (string)
                  ->on('users')
                  ->onDelete('set null'); // (atau 'restrict')
            // ---------------------------

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'week_start']); // Satu user satu timesheet per minggu
        });

        Schema::create('timesheet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timesheet_id')->constrained()->onDelete('cascade');
            $table->string('project_number');
            $table->string('project_name');
            $table->string('task_number');
            $table->text('task_description');

            // Jam per hari (Senin - Minggu)
            $table->decimal('monday_regular', 4, 2)->default(0);
            $table->decimal('monday_overtime', 4, 2)->default(0);
            $table->decimal('tuesday_regular', 4, 2)->default(0);
            $table->decimal('tuesday_overtime', 4, 2)->default(0);
            $table->decimal('wednesday_regular', 4, 2)->default(0);
            $table->decimal('wednesday_overtime', 4, 2)->default(0);
            $table->decimal('thursday_regular', 4, 2)->default(0);
            $table->decimal('thursday_overtime', 4, 2)->default(0);
            $table->decimal('friday_regular', 4, 2)->default(0);
            $table->decimal('friday_overtime', 4, 2)->default(0);
            $table->decimal('saturday_regular', 4, 2)->default(0);
            $table->decimal('saturday_overtime', 4, 2)->default(0);
            $table->decimal('sunday_regular', 4, 2)->default(0);
            $table->decimal('sunday_overtime', 4, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheet_entries');
        Schema::dropIfExists('timesheets');
    }
};