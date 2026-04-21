<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\ClassEnrollment;
use App\Models\ClassRoom;
use App\Models\ClassSubject;
use App\Models\ClassSubjectTeacher;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentFeeAccount;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Academic Year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            [
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_current' => true,
            ]
        );

        // Create Term 1
        $term = Term::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'name' => 'Term 1'],
            [
                'start_date' => '2026-01-15',
                'end_date' => '2026-04-15',
                'is_current' => true,
            ]
        );

        // Create Subjects
        $subjectsData = [
            ['name' => 'English', 'code' => 'ENG', 'is_core' => true],
            ['name' => 'Chichewa', 'code' => 'CHI', 'is_core' => false],
            ['name' => 'Mathematics', 'code' => 'MAT', 'is_core' => true],
            ['name' => 'Social and Development Studies', 'code' => 'SDS', 'is_core' => false],
            ['name' => 'Agriculture', 'code' => 'AGR', 'is_core' => true],
            ['name' => 'Arts', 'code' => 'ART', 'is_core' => false],
            ['name' => 'Biology', 'code' => 'BIO', 'is_core' => false],
            ['name' => 'Physics', 'code' => 'PHY', 'is_core' => false],
            ['name' => 'Chemistry', 'code' => 'CHE', 'is_core' => false],
            ['name' => 'History', 'code' => 'HIS', 'is_core' => false],
            ['name' => 'Computer Studies', 'code' => 'COM', 'is_core' => false],
            ['name' => 'Life Skills', 'code' => 'LSK', 'is_core' => false],
        ];

        $subjects = [];
        foreach ($subjectsData as $subjectData) {
            $subjects[$subjectData['code']] = Subject::firstOrCreate(
                ['code' => $subjectData['code']],
                $subjectData
            );
        }

        // Create Teachers
        $teachers = [];
        $teacherNames = [
            ['first' => 'John', 'last' => 'Smith'],
            ['first' => 'Mary', 'last' => 'Johnson'],
            ['first' => 'David', 'last' => 'Williams'],
            ['first' => 'Sarah', 'last' => 'Brown'],
            ['first' => 'Michael', 'last' => 'Jones'],
            ['first' => 'Emma', 'last' => 'Garcia'],
            ['first' => 'Robert', 'last' => 'Miller'],
            ['first' => 'Jennifer', 'last' => 'Davis'],
            ['first' => 'William', 'last' => 'Rodriguez'],
            ['first' => 'Lisa', 'last' => 'Martinez'],
            ['first' => 'James', 'last' => 'Hernandez'],
            ['first' => 'Patricia', 'last' => 'Lopez'],
        ];

        foreach ($teacherNames as $index => $name) {
            $empNum = 'EMP-' . str_pad($index + 2, 4, '0', STR_PAD_LEFT); // Starting from EMP-0002
            $teachers[] = Staff::firstOrCreate(
                ['employee_number' => $empNum],
                [
                    'first_name' => $name['first'],
                    'last_name' => $name['last'],
                    'gender' => $index % 2 == 0 ? 'Male' : 'Female',
                    'email' => strtolower($name['first'] . '.' . $name['last']) . '@school.test',
                    'phone' => '+265' . rand(1000000000, 9999999999),
                    'job_title' => 'Teacher',
                    'hire_date' => '2025-01-15',
                    'status' => 'ACTIVE',
                ]
            );
        }

        // Create Classes and assign class teachers
        $classData = [
            ['name' => 'Form 1A', 'stream' => '1', 'section' => 'A'],
            ['name' => 'Form 1B', 'stream' => '1', 'section' => 'B'],
            ['name' => 'Form 2A', 'stream' => '2', 'section' => 'A'],
            ['name' => 'Form 2B', 'stream' => '2', 'section' => 'B'],
            ['name' => 'Form 3A', 'stream' => '3', 'section' => 'A'],
            ['name' => 'Form 3B', 'stream' => '3', 'section' => 'B'],
            ['name' => 'Form 4A', 'stream' => '4', 'section' => 'A'],
            ['name' => 'Form 4B', 'stream' => '4', 'section' => 'B'],
        ];

        $classRooms = [];
        foreach ($classData as $index => $data) {
            $classRoom = ClassRoom::firstOrCreate(
                ['name' => $data['name']],
                [
                    'stream' => $data['stream'],
                    'section' => $data['section'],
                    'class_teacher_id' => $teachers[$index % count($teachers)]->id,
                ]
            );
            $classRooms[] = $classRoom;
        }

        // Assign Subject Teachers to Classes
        $subjectCodesPerClass = [
            ['ENG', 'CHI', 'MAT', 'SDS', 'AGR', 'ART', 'BIO', 'PHY', 'CHE', 'HIS', 'COM', 'LSK'],
        ];

        foreach ($classRooms as $classIndex => $classRoom) {
            // Rotate teachers for each subject
            foreach ($subjects as $code => $subject) {
                $teacherIndex = (array_search($code, array_keys($subjects)) + $classIndex) % count($teachers);

                ClassSubject::firstOrCreate(
                    [
                        'class_room_id' => $classRoom->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'teacher_id' => $teachers[$teacherIndex]->id,
                    ]
                );

                $classSubject = ClassSubject::where('class_room_id', $classRoom->id)
                    ->where('subject_id', $subject->id)
                    ->first();

                ClassSubjectTeacher::updateOrCreate(
                    [
                        'class_subject_id' => $classSubject->id,
                        'teacher_id' => $teachers[$teacherIndex]->id,
                    ],
                    [
                        'is_core' => true,
                        'starts_on' => $term->start_date->toDateString(),
                    ]
                );
            }
        }

        // Create Students and Guardians
        $firstNames = ['Alice', 'Bob', 'Charlie', 'Diana', 'Evan', 'Fiona', 'George', 'Hannah', 'Isaac', 'Julia', 'Kevin', 'Laura'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez'];
        $relationships = ['Mother', 'Father', 'Guardian', 'Uncle', 'Aunt'];

        $students = [];
        $studentCount = 0;

        foreach ($classRooms as $classIndex => $classRoom) {
            for ($i = 0; $i < 12; $i++) {
                $studentCount++;
                $admissionNum = 'STU-' . str_pad($studentCount, 5, '0', STR_PAD_LEFT);

                $student = Student::firstOrCreate(
                    ['admission_number' => $admissionNum],
                    [
                        'first_name' => $firstNames[$i],
                        'last_name' => $lastNames[($classIndex + $i) % count($lastNames)],
                        'gender' => rand(0, 1) ? 'Male' : 'Female',
                        'date_of_birth' => $this->generateDateOfBirth(),
                        'enrollment_date' => $term->start_date,
                        'status' => 'ACTIVE',
                        'current_class_room_id' => $classRoom->id,
                    ]
                );

                $students[] = $student;

                // Create ClassEnrollment
                ClassEnrollment::firstOrCreate(
                    ['student_id' => $student->id, 'term_id' => $term->id],
                    [
                        'class_room_id' => $classRoom->id,
                        'enrollment_date' => $term->start_date,
                        'status' => 'ACTIVE',
                    ]
                );

                // Create Guardians for Students
                for ($g = 0; $g < rand(1, 2); $g++) {
                    $guardian = Guardian::create([
                        'first_name' => $firstNames[rand(0, count($firstNames) - 1)],
                        'last_name' => $student->last_name,
                        'phone' => '+265' . rand(1000000000, 9999999999),
                        'email' => strtolower($student->first_name . '.' . $student->last_name . '.guardian' . ($g + 1)) . '@email.test',
                        'address' => 'Plot ' . rand(100, 9999) . ', Box ' . rand(100, 9999) . ', City',
                        'relationship' => $relationships[rand(0, count($relationships) - 1)],
                    ]);

                    $student->guardians()->attach($guardian->id, [
                        'is_primary' => $g == 0,
                        'notes' => $g == 0 ? 'Primary guardian' : 'Secondary guardian',
                    ]);
                }
            }
        }

        // Create Guardians for Staff
        foreach ($teachers as $teacher) {
            $guardian = Guardian::create([
                'first_name' => $teacher->first_name,
                'last_name' => $teacher->last_name,
                'phone' => $teacher->phone,
                'email' => $teacher->email,
                'address' => 'Staff Residence ' . rand(1, 100),
                'relationship' => 'Self',
            ]);

            // Optionally create user accounts for some teachers
            if (rand(0, 1)) {
                $username = strtolower($teacher->first_name . '.' . $teacher->last_name);
                $user = User::updateOrCreate(
                    ['username' => $username],
                    [
                        'staff_id' => $teacher->id,
                        'password' => Hash::make('password'),
                        'is_active' => true,
                    ]
                );

                $user->syncRoles(['teacher']);
            }
        }

        // Create Fee Structures
        $feeStructures = [];
        foreach ($classRooms as $classRoom) {
            $feeStructure = FeeStructure::firstOrCreate(
                [
                    'class_room_id' => $classRoom->id,
                    'academic_year_id' => $academicYear->id,
                    'term_id' => $term->id,
                ],
                [
                    'total_amount' => 50000,
                ]
            );

            $feeStructures[] = $feeStructure;

            // Create Fee Items
            $feeItems = [
                ['name' => 'Tuition', 'amount' => 25000],
                ['name' => 'Development', 'amount' => 10000],
                ['name' => 'Activity', 'amount' => 8000],
                ['name' => 'Exams', 'amount' => 5000],
                ['name' => 'Sports', 'amount' => 2000],
            ];

            foreach ($feeItems as $item) {
                FeeItem::firstOrCreate(
                    [
                        'fee_structure_id' => $feeStructure->id,
                        'name' => $item['name'],
                    ],
                    [
                        'amount' => $item['amount'],
                    ]
                );
            }
        }

        // Create Student Fee Accounts and Payments
        $receiptCounter = 1000;
        foreach ($students as $student) {
            // Find the fee structure for student's class
            $feeStructure = FeeStructure::where('class_room_id', $student->current_class_room_id)
                ->where('academic_year_id', $academicYear->id)
                ->where('term_id', $term->id)
                ->first();

            if ($feeStructure) {
                $feeAccount = StudentFeeAccount::firstOrCreate(
                    ['student_id' => $student->id, 'fee_structure_id' => $feeStructure->id],
                    [
                        'balance' => $feeStructure->total_amount,
                    ]
                );

                // Create some payments for some students
                if (rand(0, 2) > 0) { // 66% chance of having at least one payment
                    $paymentCount = rand(1, 3);
                    for ($p = 0; $p < $paymentCount; $p++) {
                        $amountPaid = rand(5000, 20000);
                        Payment::create([
                            'student_fee_account_id' => $feeAccount->id,
                            'amount_paid' => $amountPaid,
                            'payment_date' => $term->start_date->addDays(rand(1, 30)),
                            'payment_method' => ['Cash', 'Bank Transfer', 'Mobile Money'][rand(0, 2)],
                            'receipt_number' => 'REC-' . str_pad($receiptCounter++, 5, '0', STR_PAD_LEFT),
                            'recorded_by' => $teachers[rand(0, count($teachers) - 1)]->id,
                        ]);

                        // Update balance
                        $feeAccount->decrement('balance', $amountPaid);
                    }
                }
            }
        }

        // Create Assessments
        $assessmentTypes = AssessmentType::all();

        foreach ($classRooms as $classRoom) {
            $classSubjects = ClassSubject::where('class_room_id', $classRoom->id)->get();

            foreach ($classSubjects as $classSubject) {
                foreach ($assessmentTypes as $assessmentType) {
                    Assessment::firstOrCreate(
                        [
                            'class_subject_id' => $classSubject->id,
                            'term_id' => $term->id,
                            'assessment_type_id' => $assessmentType->id,
                        ],
                        [
                            'title' => $classSubject->subject->name . ' - ' . $assessmentType->name,
                            'max_score' => 100,
                            'assessment_date' => $term->start_date->addDays(rand(5, 50)),
                        ]
                    );
                }
            }
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('- Created Academic Year: 2026/2027');
        $this->command->info('- Created Term: Term 1');
        $this->command->info('- Created Subjects: ' . count($subjects));
        $this->command->info('- Created Teachers: ' . count($teachers));
        $this->command->info('- Created Classes: ' . count($classRooms));
        $this->command->info('- Created Students: ' . count($students));
        $this->command->info('- Created Guardians: Multiple per student');
        $this->command->info('- Created Fee Structures: ' . count($feeStructures));
        $this->command->info('- Created Assessments per class subject and type');
    }

    private function generateDateOfBirth(): string
    {
        // Generate date of birth for students (assuming ages 13-19)
        $year = rand(2006, 2013);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

        return "{$year}-{$month}-{$day}";
    }
}
