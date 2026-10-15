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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator Perpustakaan',
            'email' => 'admin@smpn6bandung.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $petugas = User::create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'petugas@smpn6bandung.sch.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        $pelajaran = Category::create([
            'name' => 'Pelajaran',
            'description' => 'Buku penunjang mata pelajaran sekolah.',
        ]);

        $referensi = Category::create([
            'name' => 'Referensi',
            'description' => 'Ensiklopedia, kamus, atlas, dan bahan rujukan.',
        ]);

        $umum = Category::create([
            'name' => 'Umum',
            'description' => 'Bacaan umum dan pengembangan literasi siswa.',
        ]);

        $students = collect([
            Student::create([
                'nis' => '2425001',
                'member_code' => 'AGT-2026-001',
                'name' => 'Alya Putri',
                'gender' => 'Perempuan',
                'class' => 'VII-A',
                'phone' => '081234560001',
                'address' => 'Bandung',
                'status' => 'aktif',
            ]),
            Student::create([
                'nis' => '2425002',
                'member_code' => 'AGT-2026-002',
                'name' => 'Rafi Maulana',
                'gender' => 'Laki-laki',
                'class' => 'VIII-B',
                'phone' => '081234560002',
                'address' => 'Bandung',
                'status' => 'aktif',
            ]),
            Student::create([
                'nis' => '2425003',
                'member_code' => 'AGT-2026-003',
                'name' => 'Nadia Rahma',
                'gender' => 'Perempuan',
                'class' => 'IX-C',
                'phone' => null,
                'address' => 'Bandung',
                'status' => 'aktif',
            ]),
            Student::create([
                'nis' => '2425004',
                'member_code' => 'AGT-2026-004',
                'name' => 'Dimas Pratama',
                'gender' => 'Laki-laki',
                'class' => 'VII-D',
                'phone' => null,
                'address' => null,
                'status' => 'aktif',
            ]),
        ]);

        $books = collect([
            Book::create([
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
            Book::create([
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
            Book::create([
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
            Book::create([
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

        $loanOne = Loan::create([
            'loan_code' => 'PMJ-1026-001',
            'student_id' => $students[0]->id,
            'user_id' => $petugas->id,
            'loan_date' => '2026-10-14',
            'due_date' => '2026-10-21',
            'returned_at' => null,
            'status' => 'dipinjam',
            'extension_count' => 0,
        ]);

        $loanOne->items()->create([
            'book_id' => $books[0]->id,
            'returned_at' => null,
            'status' => 'dipinjam',
        ]);

        $loanTwo = Loan::create([
            'loan_code' => 'PMJ-1026-002',
            'student_id' => $students[2]->id,
            'user_id' => $admin->id,
            'loan_date' => '2026-10-07',
            'due_date' => '2026-10-14',
            'returned_at' => null,
            'status' => 'terlambat',
            'extension_count' => 1,
        ]);

        $loanTwo->items()->create([
            'book_id' => $books[3]->id,
            'returned_at' => null,
            'status' => 'terlambat',
        ]);

        Visit::create([
            'student_id' => $students[0]->id,
            'visit_date' => '2026-10-14',
            'check_in_time' => '08:15',
        ]);

        Visit::create([
            'student_id' => $students[1]->id,
            'visit_date' => '2026-10-14',
            'check_in_time' => '09:05',
        ]);

        Visit::create([
            'student_id' => $students[2]->id,
            'visit_date' => '2026-10-14',
            'check_in_time' => '10:20',
        ]);
    }
}
