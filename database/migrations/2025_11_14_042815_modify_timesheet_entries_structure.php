<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timesheet_entries', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'project_number',
                'project_name', 
                'task_number',
                'task_description'
            ]);
            
            // Add new columns
            $table->string('discipline')->after('timesheet_id');
            $table->string('level_grade')->after('discipline');
            $table->string('project_code')->after('level_grade');
            $table->string('cost_code')->after('project_code');
            $table->string('task')->after('cost_code');
        });
    }

    public function down(): void
    {
        Schema::table('timesheet_entries', function (Blueprint $table) {
            // Restore old columns
            $table->dropColumn([
                'discipline',
                'level_grade',
                'project_code',
                'cost_code',
                'task'
            ]);
            
            $table->string('project_number');
            $table->string('project_name');
            $table->string('task_number');
            $table->text('task_description');
        });
    }
};