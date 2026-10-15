<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Student;
use App\Models\Visit;
use App\Services\LoanStatusService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function index(): View
    {
        $this->loanStatusService->syncOverdueStatuses();

        $today = now()->toDateString();
        $totalBooks = Book::sum('stock');
        $totalBookTitles = Book::count();
        $borrowedBooks = LoanItem::whereNull('returned_at')->whereIn('status', ['dipinjam', 'terlambat'])->count();
        $lateStudents = Loan::whereNull('returned_at')
            ->whereDate('due_date', '<', $today)
            ->distinct()
            ->count('student_id');
        $todayVisits = Visit::whereDate('visit_date', $today)->count();
        $activeMembers = Student::where('status', 'aktif')->count();

        $summaryCards = [
            ['label' => 'Total Buku', 'value' => number_format($totalBooks, 0, ',', '.'), 'note' => 'Total eksemplar koleksi'],
            ['label' => 'Total Judul Buku', 'value' => number_format($totalBookTitles, 0, ',', '.'), 'note' => 'Jumlah judul/kode buku'],
            ['label' => 'Buku Sedang Dipinjam', 'value' => number_format($borrowedBooks, 0, ',', '.'), 'note' => 'Item belum kembali'],
            ['label' => 'Siswa Terlambat', 'value' => number_format($lateStudents, 0, ',', '.'), 'note' => 'Anggota dengan pinjaman lewat tempo'],
            ['label' => 'Kunjungan Hari Ini', 'value' => number_format($todayVisits, 0, ',', '.'), 'note' => 'Siswa tercatat hadir'],
            ['label' => 'Total Anggota Aktif', 'value' => number_format($activeMembers, 0, ',', '.'), 'note' => 'Siswa berstatus aktif'],
        ];

        $recentLoans = Loan::with(['student', 'items.book'])
            ->latest('loan_date')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Loan $loan): array => [
                'student' => $loan->student?->name ?? '-',
                'class' => $loan->student?->class ?? '-',
                'book' => $loan->items->pluck('book.title')->filter()->join(', ') ?: '-',
                'date' => $this->formatDateValue($loan->getAttribute('loan_date')),
                'status_label' => $this->loanStatusService->displayLabel($loan),
                'status_class' => $this->loanStatusService->badgeClass($loan),
                'late_days' => $this->loanStatusService->lateDays($loan),
            ]);

        $lateStudentsList = Loan::with(['student', 'items.book'])
            ->whereNull('returned_at')
            ->whereDate('due_date', '<', $today)
            ->oldest('due_date')
            ->oldest('id')
            ->limit(20)
            ->get()
            ->unique('student_id')
            ->take(5)
            ->map(fn (Loan $loan): array => [
                'student' => $loan->student?->name ?? '-',
                'class' => $loan->student?->class ?? '-',
                'book' => $loan->items->pluck('book.title')->filter()->join(', ') ?: '-',
                'due_date' => $this->formatDateValue($loan->getAttribute('due_date')),
                'late_days' => $this->loanStatusService->lateDays($loan),
            ]);

        $popularBooks = LoanItem::with('book')
            ->select('book_id')
            ->selectRaw('count(*) as loans_count')
            ->groupBy('book_id')
            ->orderByDesc('loans_count')
            ->limit(5)
            ->get()
            ->map(fn (LoanItem $item): array => [
                'book_code' => $item->book?->book_code ?? '-',
                'title' => $item->book?->title ?? '-',
                'loans_count' => (int) $item->getAttribute('loans_count'),
            ]);

        $visitStats = $this->visitStats();

        return view('dashboard.index', compact('summaryCards', 'recentLoans', 'lateStudentsList', 'popularBooks', 'visitStats'));
    }

    private function formatDateValue(mixed $value): string
    {
        if (! $value) {
            return '-';
        }

        return Carbon::parse($value)->format('d M Y');
    }

    private function visitStats(): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->startOfDay();
        $rows = Visit::query()
            ->select('visit_date', DB::raw('count(*) as total'))
            ->whereBetween('visit_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('visit_date')
            ->pluck('total', 'visit_date');

        $days = collect(range(0, 6))->map(function (int $offset) use ($startDate, $rows): array {
            $date = $startDate->copy()->addDays($offset);
            $key = $date->toDateString();
            $total = (int) ($rows[$key] ?? 0);

            return [
                'date' => $date->format('d M'),
                'day' => $date->translatedFormat('D'),
                'total' => $total,
            ];
        });

        $max = max($days->max('total'), 1);

        return $days
            ->map(fn (array $day): array => $day + ['percentage' => (int) round(($day['total'] / $max) * 100)])
            ->all();
    }
}
