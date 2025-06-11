<?php

namespace Database\Seeders;

use App\Models\Nssf;
use App\Models\Paye;
use App\Models\Shif;
use App\Models\Gender;
use App\Models\Relief;
use App\Models\Salutation;
use App\Models\HousingLevy;
use App\Models\Nationality;
use App\Models\PaymentMode;
use App\Models\DocumentType;
use App\Models\Relationship;
use App\Models\MaritalStatus;
use App\Models\EducationLevel;
use App\Models\EmploymentType;
use Illuminate\Database\Seeder;
use App\Models\EmploymentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HrConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gender
        $genders = [
            ['name' => 'Male'],
            ['name' => 'Female']
        ];

        foreach($genders as $gender) {
            Gender::firstOrCreate($gender);
        }

        // Salutations
        $salutations = [
            ['name' => 'Mr.'],
            ['name' => 'Mrs'],
            ['name' => 'Miss'],
            ['name' => 'Ms.']
        ];

        foreach($salutations as $salutation) {
            Salutation::firstOrCreate($salutation);
        }

        // Marital Statuses
        $maritalStatuses = [
            ['name' => 'Single'],
            ['name' => 'Married'],
            ['name' => 'Divorced'],
            ['name' => 'Separated'],
            ['name' => 'Widowed'],
            ['name' => 'Prefer Not Say'],
        ];

        foreach($maritalStatuses as $maritalStatus) {
            MaritalStatus::firstOrCreate($maritalStatus);
        }

        // Employment Types
        $employmentTypes = [
            ['name' => 'Casual'],
            ['name' => 'Contract'],
            ['name' => 'Consultant'],
            ['name' => 'Fixed Term'],
            ['name' => 'Internship'],
            ['name' => 'Permanent'],
            ['name' => 'Probationary'],
            ['name' => 'Regular'],
            ['name' => 'Trainee'],
            ['name' => 'Voluntary'],
        ];

        foreach($employmentTypes as $employmentType) {
            EmploymentType::firstOrCreate($employmentType);
        }

        // Employment Statuses
        $employmentStatuses = [
            ['name' => 'Active'],
            ['name' => 'Suspended'],
            ['name' => 'Terminated'],

        ];

        foreach($employmentStatuses as $employmentStatus) {
            EmploymentStatus::firstOrCreate($employmentStatus);
        }

        // Relationships
        $relationships = [
            ['name' => 'Father'],
            ['name' => 'Mother'],
            ['name' => 'Sister'],
            ['name' => 'Brother'],
            ['name' => 'Son'],
            ['name' => 'Daughter'],
            ['name' => 'Grandfather'],
            ['name' => 'Grandmother'],
            ['name' => 'Grandson'],
            ['name' => 'Granddaughter'],
            ['name' => 'Uncle'],
            ['name' => 'Aunt'],
            ['name' => 'Nephew'],
            ['name' => 'Niece'],
            ['name' => 'Cousin'],
            ['name' => 'Husband'],
            ['name' => 'Wife'],
            ['name' => 'Fiancé'],
            ['name' => 'Fiancée'],
            ['name' => 'Boyfriend'],
            ['name' => 'Girlfriend'],
            ['name' => 'Stepfather'],
            ['name' => 'Stepmother'],
            ['name' => 'Stepbrother'],
            ['name' => 'Stepsister'],
            ['name' => 'Stepson'],
            ['name' => 'Stepdaughter'],
            ['name' => 'Father-in-law'],
            ['name' => 'Mother-in-law'],
            ['name' => 'Brother-in-law'],
            ['name' => 'Sister-in-law'],
            ['name' => 'Son-in-law'],
            ['name' => 'Daughter-in-law'],
            ['name' => 'Godfather'],
            ['name' => 'Godmother'],
            ['name' => 'Godson'],
            ['name' => 'Goddaughter'],
            ['name' => 'Friend'],
            ['name' => 'Best Friend'],
            ['name' => 'Colleague'],
            ['name' => 'Boss'],
            ['name' => 'Mentor'],
            ['name' => 'Mentee'],
            ['name' => 'Neighbor'],
            ['name' => 'Acquaintance'],
            ['name' => 'Classmate'],
            ['name' => 'Roommate'],
            ['name' => 'Teammate'],
        ];        

        foreach($relationships as $relationship) {
            Relationship::firstOrCreate($relationship);
        }

        // Document Types
        $documentTypes = [
            ['name' => 'ID'],
            ['name' => 'KCPE Certificate'],
            ['name' => 'KCSE Certificate'],
            ['name' => 'Certificate'],
            ['name' => 'Diploma'],
            ['name' => 'Degree'],
            ['name' => 'PIN Certificate'],
            ['name' => 'NSSF Card'],
            ['name' => 'NHIF Card'],
            ['name' => 'NHIF Card'],
        ];

        foreach($documentTypes as $documentType) {
            DocumentType::firstOrCreate($documentType);
        }

        // Education Levels
        $educationLevels = [
            ['name' => 'KCPE'],
            ['name' => 'KCSE'],
            ['name' => 'Diploma'],
            ['name' => 'Degree'],
            ['name' => 'Doctorate'],
        ];

        foreach($educationLevels as $educationLevel) {
            EducationLevel::firstOrCreate($educationLevel);
        }

        // Nationality
        $nationalities = [
            ['name' => 'Kenyan'],
        ];

        foreach($nationalities as $nationality) {
            Nationality::firstOrCreate($nationality);
        }

        // Payment Modes
        $paymentModes = [
            ['name' => 'Cash'],
            ['name' => 'Cheque'],
            ['name' => 'Bank'],
            ['name' => 'Mpesa'],
        ];

        foreach($paymentModes as $paymentMode) {
            PaymentMode::firstOrCreate($paymentMode);
        }

        // PAYROLL CONFIGURATIONS

        // PAYE
        $payes = [
            [
                'from' => 1,
                'to' => 24000,
                'rate' => 10
            ],
            [
                'from' => 24001,
                'to' => 32333,
                'rate' => 25
            ],
            [
                'from' => 32334,
                'to' => 500000,
                'rate' => 30
            ],
            [
                'from' => 500001,
                'to' => 80000,
                'rate' => 32.5
            ],
            [
                'from' => 800001,
                'to' => 999999999,
                'rate' => 35
            ],
        ];

        foreach($payes as $paye) {
            Paye::firstOrCreate($paye);
        }

        // NSSF
        $nssfs = [
            [
                'from' => 1,
                'to' => 7000,
                'rate' => 6
            ],
            [
                'from' => 7001,
                'to' => 36000,
                'rate' => 6
            ],
        ];

        foreach($nssfs as $nssf) {
            Nssf::firstOrCreate($nssf);
        }

        // SHIF
        Shif::firstOrCreate([
            'name' => 'SHIF',
            'rate' => 2.75
        ]);

        // Housing Levy
        HousingLevy::firstOrCreate([
            'name' => 'Housing Levy',
            'rate' => 1.5
        ]);

        // Reliefs
        $reliefs = [
            [
                'name' => 'Tax Relief',
                'amount_type' => 'fixed_amount',
                'amount' => 2400,
                'system_reserved' => true
            ],
            [
                'name' => 'Housing Levy Relief',
                'amount_type' => 'percentage',
                'rate' => 15,
                'system_reserved' => true
            ],
        ];

        foreach($reliefs as $relief) {
            Relief::firstOrCreate($relief);
        }
    }
}
