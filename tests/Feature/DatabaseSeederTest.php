<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_can_run_twice_and_demo_member_can_log_in(): void
    {
        Student::create([
            'no' => 'IMPORTED-001',
            'member_type' => Student::TYPE_STUDENT,
            'member_code' => 'AGT-2026-001',
            'name' => 'Alya Putri',
            'gender' => 'Perempuan',
            'class' => 'VII-A',
            'phone' => null,
            'address' => null,
            'status' => 'aktif',
        ]);

        $this->seed(DatabaseSeeder::class);
        $this->assertSame(
            'IMPORTED-001',
            Student::query()->where('member_code', 'AGT-2026-001')->value('no')
        );
        $this->assertSame(
            'AGT-DEMO-2026-001',
            Student::query()->where('no', '252607001')->value('member_code')
        );

        $counts = [
            User::count(),
            Category::count(),
            Student::count(),
            Book::count(),
            Loan::count(),
            Visit::count(),
        ];

        $this->seed(DatabaseSeeder::class);

        $demoMemberIds = Student::query()
            ->whereIn('no', ['252607001', '252607002', '252607003'])
            ->pluck('id');

        foreach ($demoMemberIds as $memberId) {
            $this->assertSame(
                1,
                Visit::query()
                    ->where('student_id', $memberId)
                    ->whereDate('visit_date', '2026-10-14')
                    ->count()
            );
        }

        $this->assertSame($counts, [
            User::count(),
            Category::count(),
            Student::count(),
            Book::count(),
            Loan::count(),
            Visit::count(),
        ]);

        $this->post(route('members.login.store'), [
            'no' => '252607001',
            'password' => 'password',
        ])->assertRedirect(route('members.home'));
    }
}
