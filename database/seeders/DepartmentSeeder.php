<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'SS Department',
                'email' => 'ss.department@port.com',
                'head_name' => 'UCUA Officer',
                'head_email' => 'ucua.officer@port.com',
                'head_phone' => '0123456783',
                'is_active' => true
            ],
            [
                'name' => 'Operations',
                'email' => 'operations@port.com',
                'head_name' => 'John Doe',
                'head_email' => 'john.doe@port.com',
                'head_phone' => '0123456789',
                'is_active' => true
            ],
            [
                'name' => 'Maintenance',
                'email' => 'maintenance@port.com',
                'head_name' => 'Jane Smith',
                'head_email' => 'jane.smith@port.com',
                'head_phone' => '0123456788',
                'is_active' => true
            ],
            [
                'name' => 'Security',
                'email' => 'security@port.com',
                'head_name' => 'Mike Johnson',
                'head_email' => 'mike.johnson@port.com',
                'head_phone' => '0123456787',
                'is_active' => true
            ],
            [
                'name' => 'Safety',
                'email' => 'safety@port.com',
                'head_name' => 'Sarah Williams',
                'head_email' => 'sarah.williams@port.com',
                'head_phone' => '0123456786',
                'is_active' => true
            ],
            [
                'name' => 'Human Resources',
                'email' => 'hr@port.com',
                'head_name' => 'David Brown',
                'head_email' => 'david.brown@port.com',
                'head_phone' => '0123456785',
                'is_active' => true
            ],
            [
                'name' => 'Information Technology',
                'email' => 'it@port.com',
                'head_name' => 'Lisa Davis',
                'head_email' => 'lisa.davis@port.com',
                'head_phone' => '0123456784',
                'is_active' => true
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
} 