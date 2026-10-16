<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Student;
use App\Models\Visit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ReportService
{
    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function types(): array
    {
        return [
            'loans' => [
                'title' => 'Laporan Peminjaman',
                'filename' => 'laporan-peminjaman',
                'filters' => ['date' => true, 'class' => true, 'status' => true, 'category' => false],
                'statuses' => ['dipinjam' => 'Dipinjam', 'terlambat' => 'Terlambat', 'dikembalikan' => 'Dikembalikan'],
                'columns' => [
                    'loan_code' => 'Kode Pinjam',
                    'loan_date' => 'Tanggal Pinjam',
                    'due_date' => 'Jatuh Tempo',
                    'student_name' => 'Siswa',
                    'nis' => 'NIS',
                    'class' => 'Kelas',
                    'books' => 'Buku',
                    'officer' => 'Admin',
                    'status' => 'Status',
                ],
            ],
            'returns' => [
                'title' => 'Laporan Pengembalian',
                'filename' => 'laporan-pengembalian',
                'filters' => ['date' => true, 'class' => true, 'status' => false, 'category' => false],
                'statuses' => [],
                'columns' => [
                    'returned_at' => 'Tanggal Kembali',
                    'loan_code' => 'Kode Pinjam',
                    'book_code' => 'Kode Buku',
                    'book_title' => 'Judul Buku',
                    'student_name' => 'Siswa',
                    'nis' => 'NIS',
                    'class' => 'Kelas',
                    'status' => 'Status',
                ],
            ],
            'overdue' => [
                'title' => 'Laporan Keterlambatan',
                'filename' => 'laporan-keterlambatan',
                'filters' => ['date' => true, 'class' => true, 'status' => false, 'category' => false],
                'statuses' => [],
                'columns' => [
                    'loan_code' => 'Kode Pinjam',
                    'due_date' => 'Jatuh Tempo',
                    'late_days' => 'Hari Terlambat',
                    'student_name' => 'Siswa',
                    'nis' => 'NIS',
                    'class' => 'Kelas',
                    'books' => 'Buku',
                    'status' => 'Status',
                ],
            ],
            'visits' => [
                'title' => 'Laporan Kunjungan',
                'filename' => 'laporan-kunjungan',
                'filters' => ['date' => true, 'class' => true, 'status' => false, 'category' => false],
                'statuses' => [],
                'columns' => [
                    'visit_date' => 'Tanggal',
                    'check_in_time' => 'Jam Masuk',
                    'student_name' => 'Siswa',
                    'nis' => 'NIS',
                    'class' => 'Kelas',
                ],
            ],
            'books' => [
                'title' => 'Laporan Data Buku',
                'filename' => 'laporan-data-buku',
                'filters' => ['date' => false, 'class' => false, 'status' => true, 'category' => true],
                'statuses' => ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'],
                'columns' => [
                    'book_code' => 'Kode Buku',
                    'title' => 'Judul',
                    'author' => 'Penulis',
                    'category' => 'Kategori',
                    'stock' => 'Stok',
                    'available_stock' => 'Tersedia',
                    'shelf_location' => 'Rak',
                    'status' => 'Status',
                ],
            ],
            'students' => [
                'title' => 'Laporan Data Siswa',
                'filename' => 'laporan-data-siswa',
                'filters' => ['date' => false, 'class' => true, 'status' => true, 'category' => false],
                'statuses' => ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'],
                'columns' => [
                    'nis' => 'NIS',
                    'member_code' => 'Kode Anggota',
                    'name' => 'Nama',
                    'class' => 'Kelas',
                    'phone' => 'Nomor HP',
                    'status' => 'Status',
                ],
            ],
        ];
    }

    public function type(string $type): array
    {
        $types = $this->types();

        if (! isset($types[$type])) {
            throw new InvalidArgumentException('Jenis laporan tidak tersedia.');
        }

        return $types[$type];
    }

    public function normalizeType(mixed $type): string
    {
        return \is_string($type) && \array_key_exists($type, $this->types()) ? $type : 'loans';
    }

    public function filters(array $input): array
    {
        return [
            'search' => $this->nullableString($input['search'] ?? null),
            'start_date' => $this->dateString($input['start_date'] ?? null),
            'end_date' => $this->dateString($input['end_date'] ?? null),
            'class' => $this->nullableString($input['class'] ?? null),
            'status' => $this->nullableString($input['status'] ?? null),
            'category_id' => $this->nullableString($input['category_id'] ?? null),
        ];
    }

    public function paginated(string $type, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $rows = $this->query($type, $filters)->paginate($perPage)->withQueryString();
        $rows->setCollection($rows->getCollection()->map(fn ($row): array => $this->mapRow($type, $row)));

        return $rows;
    }

    public function collection(string $type, array $filters): Collection
    {
        return $this->query($type, $filters)
            ->get()
            ->map(fn ($row): array => $this->mapRow($type, $row));
    }

    public function periodLabel(array $filters, bool $hasDateFilter): string
    {
        if (! $hasDateFilter) {
            return 'Semua periode';
        }

        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        if ($startDate && $endDate) {
            return $this->formatDate($startDate).' - '.$this->formatDate($endDate);
        }

        if ($startDate) {
            return 'Mulai '.$this->formatDate($startDate);
        }

        if ($endDate) {
            return 'Sampai '.$this->formatDate($endDate);
        }

        return 'Semua periode';
    }

    public function filename(string $type, string $extension): string
    {
        $meta = $this->type($type);

        return $meta['filename'].'-'.now()->format('Ymd-His').'.'.$extension;
    }

    private function query(string $type, array $filters)
    {
        return match ($type) {
            'loans' => $this->loansQuery($filters),
            'returns' => $this->returnsQuery($filters),
            'overdue' => $this->overdueQuery($filters),
            'visits' => $this->visitsQuery($filters),
            'books' => $this->booksQuery($filters),
            'students' => $this->studentsQuery($filters),
            default => throw new InvalidArgumentException('Jenis laporan tidak tersedia.'),
        };
    }

    private function loansQuery(array $filters)
    {
        return Loan::with(['student', 'user', 'items.book'])
            ->when($filters['start_date'], fn ($query, $date) => $query->whereDate('loan_date', '>=', $date))
            ->when($filters['end_date'], fn ($query, $date) => $query->whereDate('loan_date', '<=', $date))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['class'], fn ($query, $class) => $query->whereHas('student', fn ($query) => $query->where('class', $class)))
            ->when($filters['search'], function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('loan_code', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('nis', 'like', "%{$search}%");
                        })
                        ->orWhereHas('items.book', function ($query) use ($search): void {
                            $query->where('title', 'like', "%{$search}%")
                                ->orWhere('book_code', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('loan_date')
            ->latest('id');
    }

    private function returnsQuery(array $filters)
    {
        return LoanItem::with(['book', 'loan.student'])
            ->where('status', 'dikembalikan')
            ->whereNotNull('returned_at')
            ->when($filters['start_date'], fn ($query, $date) => $query->whereDate('returned_at', '>=', $date))
            ->when($filters['end_date'], fn ($query, $date) => $query->whereDate('returned_at', '<=', $date))
            ->when($filters['class'], fn ($query, $class) => $query->whereHas('loan.student', fn ($query) => $query->where('class', $class)))
            ->when($filters['search'], function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->whereHas('book', function ($query) use ($search): void {
                        $query->where('book_code', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%");
                    })->orWhereHas('loan', function ($query) use ($search): void {
                        $query->where('loan_code', 'like', "%{$search}%")
                            ->orWhereHas('student', function ($query) use ($search): void {
                                $query->where('name', 'like', "%{$search}%")
                                    ->orWhere('nis', 'like', "%{$search}%");
                            });
                    });
                });
            })
            ->latest('returned_at')
            ->latest('id');
    }

    private function overdueQuery(array $filters)
    {
        return Loan::with(['student', 'items.book'])
            ->whereNull('returned_at')
            ->whereDate('due_date', '<', now()->toDateString())
            ->when($filters['start_date'], fn ($query, $date) => $query->whereDate('due_date', '>=', $date))
            ->when($filters['end_date'], fn ($query, $date) => $query->whereDate('due_date', '<=', $date))
            ->when($filters['class'], fn ($query, $class) => $query->whereHas('student', fn ($query) => $query->where('class', $class)))
            ->when($filters['search'], function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('loan_code', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('nis', 'like', "%{$search}%");
                        })
                        ->orWhereHas('items.book', function ($query) use ($search): void {
                            $query->where('title', 'like', "%{$search}%")
                                ->orWhere('book_code', 'like', "%{$search}%");
                        });
                });
            })
            ->oldest('due_date')
            ->oldest('id');
    }

    private function visitsQuery(array $filters)
    {
        return Visit::with('student')
            ->when($filters['start_date'], fn ($query, $date) => $query->whereDate('visit_date', '>=', $date))
            ->when($filters['end_date'], fn ($query, $date) => $query->whereDate('visit_date', '<=', $date))
            ->when($filters['class'], fn ($query, $class) => $query->whereHas('student', fn ($query) => $query->where('class', $class)))
            ->when($filters['search'], function ($query, $search): void {
                $query->whereHas('student', function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->latest('visit_date')
            ->latest('check_in_time')
            ->latest('id');
    }

    private function booksQuery(array $filters)
    {
        return Book::with('category')
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['category_id'], fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['search'], function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('book_code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->orderBy('title')
            ->orderBy('book_code');
    }

    private function studentsQuery(array $filters)
    {
        return Student::query()
            ->when($filters['class'], fn ($query, $class) => $query->where('class', $class))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'], function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('class')
            ->orderBy('name');
    }

    private function mapRow(string $type, mixed $row): array
    {
        return match ($type) {
            'loans' => $this->mapLoan($row),
            'returns' => $this->mapReturn($row),
            'overdue' => $this->mapOverdue($row),
            'visits' => $this->mapVisit($row),
            'books' => $this->mapBook($row),
            'students' => $this->mapStudent($row),
            default => [],
        };
    }

    private function mapLoan(Loan $loan): array
    {
        return [
            'loan_code' => (string) $loan->getAttribute('loan_code'),
            'loan_date' => $this->formatModelDate($loan->getAttribute('loan_date')),
            'due_date' => $this->formatModelDate($loan->getAttribute('due_date')),
            'student_name' => $loan->student?->name ?? '-',
            'nis' => $loan->student?->nis ?? '-',
            'class' => $loan->student?->class ?? '-',
            'books' => $loan->items->pluck('book.title')->filter()->join(', ') ?: '-',
            'officer' => $loan->user?->name ?? '-',
            'status' => $this->loanStatusService->displayLabel($loan),
        ];
    }

    private function mapReturn(LoanItem $item): array
    {
        return [
            'returned_at' => $item->returned_at?->format('d M Y H:i') ?? '-',
            'loan_code' => $item->loan?->loan_code ?? '-',
            'book_code' => $item->book?->book_code ?? '-',
            'book_title' => $item->book?->title ?? '-',
            'student_name' => $item->loan?->student?->name ?? '-',
            'nis' => $item->loan?->student?->nis ?? '-',
            'class' => $item->loan?->student?->class ?? '-',
            'status' => 'Dikembalikan',
        ];
    }

    private function mapOverdue(Loan $loan): array
    {
        return [
            'loan_code' => (string) $loan->getAttribute('loan_code'),
            'due_date' => $this->formatModelDate($loan->getAttribute('due_date')),
            'late_days' => $this->loanStatusService->lateDays($loan).' hari',
            'student_name' => $loan->student?->name ?? '-',
            'nis' => $loan->student?->nis ?? '-',
            'class' => $loan->student?->class ?? '-',
            'books' => $loan->items->pluck('book.title')->filter()->join(', ') ?: '-',
            'status' => 'Terlambat',
        ];
    }

    private function mapVisit(Visit $visit): array
    {
        return [
            'visit_date' => $this->formatModelDate($visit->getAttribute('visit_date')),
            'check_in_time' => substr((string) $visit->getAttribute('check_in_time'), 0, 5),
            'student_name' => $visit->student?->name ?? '-',
            'nis' => $visit->student?->nis ?? '-',
            'class' => $visit->student?->class ?? '-',
        ];
    }

    private function mapBook(Book $book): array
    {
        return [
            'book_code' => (string) $book->getAttribute('book_code'),
            'title' => (string) $book->getAttribute('title'),
            'author' => (string) $book->getAttribute('author'),
            'category' => $book->category?->name ?? '-',
            'stock' => (string) $book->getAttribute('stock'),
            'available_stock' => (string) $book->getAttribute('available_stock'),
            'shelf_location' => (string) ($book->getAttribute('shelf_location') ?? '-'),
            'status' => ucfirst((string) $book->getAttribute('status')),
        ];
    }

    private function mapStudent(Student $student): array
    {
        return [
            'nis' => (string) $student->getAttribute('nis'),
            'member_code' => (string) $student->getAttribute('member_code'),
            'name' => (string) $student->getAttribute('name'),
            'class' => (string) $student->getAttribute('class'),
            'phone' => (string) ($student->getAttribute('phone') ?? '-'),
            'status' => ucfirst((string) $student->getAttribute('status')),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function dateString(mixed $value): ?string
    {
        $value = $this->nullableString($value);

        if (! $value || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        return $value;
    }

    private function formatModelDate(mixed $value, string $format = 'd M Y'): string
    {
        if (! $value) {
            return '-';
        }

        return Carbon::parse($value)->format($format);
    }

    private function formatDate(string $date): string
    {
        return Carbon::parse($date)->format('d M Y');
    }
}
