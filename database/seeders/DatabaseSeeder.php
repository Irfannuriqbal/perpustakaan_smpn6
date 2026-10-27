<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(['email' => 'superadmin@smpn6bandung.sch.id'], [
            'name' => 'Kepala Perpustakaan',
            'email' => 'superadmin@smpn6bandung.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $admin = User::firstOrCreate(['email' => 'admin@smpn6bandung.sch.id'], [
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@smpn6bandung.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $pelajaran = Category::firstOrCreate(['name' => 'Pelajaran'], [
            'name' => 'Pelajaran',
            'description' => 'Buku penunjang mata pelajaran sekolah.',
        ]);

        $referensi = Category::firstOrCreate(['name' => 'Referensi'], [
            'name' => 'Referensi',
            'description' => 'Ensiklopedia, kamus, atlas, dan bahan rujukan.',
        ]);

        $umum = Category::firstOrCreate(['name' => 'Umum'], [
            'name' => 'Umum',
            'description' => 'Bacaan umum dan pengembangan literasi siswa.',
        ]);

        $students = collect([
            $this->firstOrCreateMember('252607001', [
                'no' => '252607001',
                'member_type' => Student::TYPE_STUDENT,
                'member_code' => 'AGT-DEMO-2026-001',
                'name' => 'Alya Putri',
                'gender' => 'Perempuan',
                'class' => 'VII-A',
                'phone' => '081234560001',
                'address' => 'Bandung',
                'status' => 'aktif',
                'password' => Hash::make('password'),
            ]),
            $this->firstOrCreateMember('252607002', [
                'no' => '252607002',
                'member_type' => Student::TYPE_STUDENT,
                'member_code' => 'AGT-DEMO-2026-002',
                'name' => 'Rafi Maulana',
                'gender' => 'Laki-laki',
                'class' => 'VIII-B',
                'phone' => '081234560002',
                'address' => 'Bandung',
                'status' => 'aktif',
                'password' => Hash::make('password'),
            ]),
            $this->firstOrCreateMember('252607003', [
                'no' => '252607003',
                'member_type' => Student::TYPE_STUDENT,
                'member_code' => 'AGT-DEMO-2026-003',
                'name' => 'Nadia Rahma',
                'gender' => 'Perempuan',
                'class' => 'IX-C',
                'phone' => null,
                'address' => 'Bandung',
                'status' => 'aktif',
                'password' => Hash::make('password'),
            ]),
            $this->firstOrCreateMember('252607004', [
                'no' => '252607004',
                'member_type' => Student::TYPE_STUDENT,
                'member_code' => 'AGT-DEMO-2026-004',
                'name' => 'Dimas Pratama',
                'gender' => 'Laki-laki',
                'class' => 'VII-D',
                'phone' => null,
                'address' => null,
                'status' => 'aktif',
                'password' => Hash::make('password'),
            ]),
            // Contoh anggota Guru/Staf
            $this->firstOrCreateMember('6010701', [
                'no' => '6010701',
                'member_type' => Student::TYPE_STAFF,
                'member_code' => 'AGT-DEMO-GURU-001',
                'name' => 'Hj. NOVITA MIRA DEWI, S.Pd.,M.M.',
                'gender' => 'Perempuan',
                'class' => null,
                'phone' => null,
                'address' => null,
                'status' => 'aktif',
                'password' => Hash::make('password'),
            ]),
        ]);

        $books = collect([
            Book::firstOrCreate(['book_code' => 'BK-001'], [
                'book_code' => 'BK-001',
                'isbn' => '9786020000011',
                'title' => 'Bahasa Indonesia Kelas VII',
                'author' => 'Tim Penyusun Kemendikbud',
                'publisher' => 'Kemendikbud',
                'publication_year' => 2022,
                'category_id' => $pelajaran->id,
                'stock' => 32,
                'available_stock' => 30,
                'shelf_location' => 'A-01',
                'status' => 'aktif',
            ]),
            Book::firstOrCreate(['book_code' => 'BK-002'], [
                'book_code' => 'BK-002',
                'isbn' => '9786020000028',
                'title' => 'Ensiklopedia Sains Dasar',
                'author' => 'R. Wibowo',
                'publisher' => 'Pustaka Edukasi',
                'publication_year' => 2021,
                'category_id' => $referensi->id,
                'stock' => 18,
                'available_stock' => 17,
                'shelf_location' => 'B-02',
                'status' => 'aktif',
            ]),
            Book::firstOrCreate(['book_code' => 'BK-003'], [
                'book_code' => 'BK-003',
                'isbn' => null,
                'title' => 'Sejarah Indonesia Ringkas',
                'author' => 'S. Kartika',
                'publisher' => 'Cahaya Ilmu',
                'publication_year' => 2020,
                'category_id' => $umum->id,
                'stock' => 21,
                'available_stock' => 21,
                'shelf_location' => 'C-04',
                'status' => 'aktif',
            ]),
            Book::firstOrCreate(['book_code' => 'BK-004'], [
                'book_code' => 'BK-004',
                'isbn' => '9786020000042',
                'title' => 'Matematika SMP Kelas IX',
                'author' => 'Tim MGMP Matematika',
                'publisher' => 'Penerbit Sekolah',
                'publication_year' => 2023,
                'category_id' => $pelajaran->id,
                'stock' => 28,
                'available_stock' => 27,
                'shelf_location' => 'A-03',
                'status' => 'aktif',
            ]),
        ]);

        $loanOne = Loan::firstOrCreate(['loan_code' => 'PMJ-1026-001'], [
            'loan_code' => 'PMJ-1026-001',
            'student_id' => $students[0]->id,
            'user_id' => $admin->id,
            'loan_date' => '2026-10-14',
            'due_date' => '2026-10-21',
            'returned_at' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
        ]);

        $loanOne->items()->firstOrCreate(['book_id' => $books[0]->id], [
            'book_id' => $books[0]->id,
            'returned_at' => null,
            'status' => 'dipinjam',
        ]);

        $loanTwo = Loan::firstOrCreate(['loan_code' => 'PMJ-1026-002'], [
            'loan_code' => 'PMJ-1026-002',
            'student_id' => $students[2]->id,
            'user_id' => $superAdmin->id,
            'loan_date' => '2026-10-07',
            'due_date' => '2026-10-14',
            'returned_at' => null,
            'status' => 'terlambat',
            'extension_count' => 1,
        ]);

        $loanTwo->items()->firstOrCreate(['book_id' => $books[3]->id], [
            'book_id' => $books[3]->id,
            'returned_at' => null,
            'status' => 'terlambat',
        ]);

        $this->seedVisit($students[0], '08:15');
        $this->seedVisit($students[1], '09:05');
        $this->seedVisit($students[2], '10:20');
    }

    private function firstOrCreateMember(string $number, array $attributes): Student
    {
        $memberByNumber = Student::query()->where('no', $number)->first();
        $memberByCode = Student::query()->where('member_code', $attributes['member_code'])->first();

        if ($memberByNumber && $memberByCode && $memberByNumber->isNot($memberByCode)) {
            throw new RuntimeException(
                "Data anggota demo {$number} bertabrakan: nomor dan kode anggota sudah digunakan oleh dua data berbeda."
            );
        }

        $member = $memberByNumber ?? $memberByCode;

        if (! $member) {
            $member = Student::create($attributes);
        } elseif ($member->no !== $number) {
            $member->update(['no' => $number]);
        }

        if (! $member->password) {
            $member->update(['password' => Hash::make('password')]);
        }

        return $member;
    }

    private function seedVisit(Student $student, string $checkInTime): void
    {
        $alreadySeeded = Visit::query()
            ->where('student_id', $student->id)
            ->whereDate('visit_date', '2026-10-14')
            ->exists();

        if (! $alreadySeeded) {
            Visit::create([
                'student_id' => $student->id,
                'visit_date' => '2026-10-14',
                'check_in_time' => $checkInTime,
            ]);
        }
    }
}
