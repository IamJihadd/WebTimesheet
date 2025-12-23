<?php

namespace Database\Seeders;

use App\Models\Discipline;
use App\Models\LevelGrade;
use App\Models\ProjectCode;
use App\Models\CostCode;
use App\Models\Task;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $isSqlite = config('database.default') === 'sqlite';
        if ($isSqlite) DB::statement('PRAGMA foreign_keys=OFF;');
        else DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        //untuk menghapus data biar ga cape2 truncate di php artisan tinker
        Discipline::truncate();
        LevelGrade::truncate();
        ProjectCode::truncate();
        CostCode::truncate();
        Task::truncate();

        if ($isSqlite) DB::statement('PRAGMA foreign_keys=ON;');
        else DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //DATA MASTER UMUM
        $levelGrades = ['On Job Training', 'Staff', 'Drafter','Admin', 'Designer', 'Sr. Designer', 'Engineer', 'Sr. Engineer', 'Lead Engineer', 'Manager', 'Project Manager', 'IT Engineer'];
        foreach ($levelGrades as $grade) LevelGrade::create(['name' => $grade]);

        // Data Department
        $departments = ['Construction','Engineering','Finance','Human Resource & Development','Management','Information & Technology','Head Office (HO)'];
        foreach ($departments as $department) Department::create(['name' => $department]);

        $projectCodes = [
            ['code' => 'DEC23001-LPG TUBAN PROJECT', 'name' => 'LPG TUBAN PROJECT'],
            ['code' => 'DEC25003-COM COMPRESSOR SKG CILAMAYA PROJECT', 'name' => 'COMPRESSOR SKG CILAMAYA PROJECT'],
            ['code' => 'DEC23003-FREEPORT MANYAR REFINERY PROJECT', 'name' => 'FREEPORT MANYAR REFINERY PROJECT'],
            ['code' => 'DEC23002-SPBU APUNG PROJECT', 'name' => 'SPBU APUNG PROJECT'],
            ['code' => 'DEC22001-SPBU GREEN ENERGY STATION (GES) PROJECT', 'name' => 'SPBU GREEN ENERGY STATION (GES) PROJECT'],
            // harus bikin fitur tambah project
        ];
        foreach ($projectCodes as $project) ProjectCode::create($project);

        $commonTasks = [
            '023' => 'Public Holiday',     // Task umum
            '024' => 'Annual Leave',
            '025' => 'Sick Leave',
            '026' => 'Maternity Leave',
            '027' => 'Emergency Leave',
        ];

        $masterData = [
            'System3D' => [
                'prefix' => 'S3D',
                'tasks' => [
                    '001' => 'Piping Routing',  // Task Khusus
                    '002' => 'Isometric Drawing',
                    '003' => 'Piping Studies',
                    '004' => 'Information Drawing',
                    '005' => '3D Model Review',
                    '006' => 'Pipe Support Design',
                    '007' => 'Bill of Materials',
                    // Mungkin bisa nambah data
                ]
            ],
            'Piping' => [
                'prefix' => 'PIP',
                'tasks' => [
                    '001' => 'Plot Plan',       //Tasks Khusus
                    '002' => 'Unit Plot Plan',
                    '003' => 'isomatric Drawing Check',
                    '004' => 'Piping General Arrangement',
                    '005' => 'Material Take-Off',
                    '006' => 'Piping Material Specifications (PMS)',
                    '007' => 'Valve Material Specification',
                    '008' => 'Pipe Support Standard',
                    '009' => 'Critical Lines',
                    '010' => 'Procedure',
                    '011' => 'Pipe Stress Analysis',
                    '012' => 'Wall Thickness Calculations',
                    '013' => 'Special Support Drawing',
                    '014' => 'Piping Material Requisition',
                    '015' => 'Bill Of Material (BOM)',
                    '016' => 'Site Survey',
                    '017' => 'As-Built',
                    // Mungkin bisa nambah
                ]
            ],
            'Process' => [
                'prefix' => 'PRO',
                'tasks' => [
                    '001' => 'Piping & Intrument Diagram (P&ID)',   // Tasks Khusus
                    '002' => 'Process Flow Diagram (PFD)',
                    '003' => 'Heat & Mass Balance',
                    '004' => 'Line Sizing',
                    '005' => 'Equipment Sizing',
                    // mungkin bisa nambah
                ]
            ],
            'Mechanical' => [
                'prefix' => 'MCH',
                'tasks' => [
                    '001' => 'Mechanical Strengh Calculation',  //Tasks Khusus
                    '002' => 'Mechanical GA Drawing',
                    //mungkin bisa data
                ]
            ],
            'Electrical' => [
                'prefix' => 'ELC',
                'tasks' => [
                    '001' => 'Instrument',
                ]
            ],
            'Civil' => [
                'prefix' => 'CVL',
                'tasks' => [
                    '001' => 'Civil',
                ]
            ],
            'Management' => [
                'prefix' => 'MGM',
                'tasks' => [
                    '001' => 'Management',
                ]
            ],
            'Finance' => [
                'prefix' => 'FNC',
                'tasks' => [
                    '001' => 'Financial Reporting',
                    '002' => 'Accounts Payable (AP)',
                    '003' => 'Accounts Receivable (AR)',
                    '004' => 'Payroll',
                    '005' => 'Budgeting & Forecasting',
                ]
            ],
            'HRD' => [
                'prefix' => 'HRD',
                'tasks' => [
                    '001' => 'Recruitment & Talent Acq',
                    '002' => 'Training & Development',
                    '003' => 'Compensation & Benefits',
                ]
            ],
            'IT' => [
                'prefix' => 'ITE',
                'tasks' => [
                    '001' => 'Help Desk Support',
                    '002' => 'System Administration',
                    '003' => 'Software Development',
                    '004' => 'Cybersecurity',
                    '005' => 'IT Projects',
                ]
            ],
        ];


        //EKSEKUSI SEEDING OTOMATIS
        foreach ($masterData as $disciplineName => $data) {
            // A. Buat Discipline
            Discipline::create(['name' => $disciplineName]);

            $prefix = $data['prefix'];
            $taskList = $data['tasks'] + $commonTasks;

            foreach ($taskList as $number => $description) {
                // Generate Kode Lengkap: S3D001
                $costCode = $prefix . $number;

                // Generate Nama Task Lengkap: S3D001-Piping Routing
                $taskFullName = $costCode . '-' . $description;

                // Buat Cost Code
                CostCode::create([
                    'code' => $costCode,
                    'description' => $description
                ]);

                // Buat Task (DENGAN LABEL DISCIPLINE)
                Task::create([
                    'name' => $taskFullName,
                    'description' => $description,
                    'discipline' => $disciplineName // <--- INI KUNCINYA
                ]);
            }
        }
    }
}
