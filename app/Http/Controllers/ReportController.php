<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Category;
use App\Models\Student;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(Request $request): View
    {
        $type = $this->reportService->normalizeType($request->query('type'));
        $types = $this->reportService->types();
        $meta = $this->reportService->type($type);
        $filters = $this->reportService->filters($request->query());
        $rows = $this->reportService->paginated($type, $filters);
        $total = $rows->total();
        $period = $this->reportService->periodLabel($filters, $meta['filters']['date']);
        $classes = Student::query()->select('class')->distinct()->orderBy('class')->pluck('class');
        $categories = Category::orderBy('name')->get();

        return view('reports.index', compact('types', 'type', 'meta', 'filters', 'rows', 'total', 'period', 'classes', 'categories'));
    }

    public function exportPdf(Request $request, string $type): Response|RedirectResponse
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()->route('reports.index', ['type' => $this->reportService->normalizeType($type)])
                ->with('error', 'Export PDF belum tersedia. Jalankan composer require barryvdh/laravel-dompdf terlebih dahulu.');
        }

        $type = $this->reportService->normalizeType($type);
        $meta = $this->reportService->type($type);
        $filters = $this->reportService->filters($request->query());
        $rows = $this->reportService->collection($type, $filters);
        $period = $this->reportService->periodLabel($filters, $meta['filters']['date']);
        $printedAt = now()->format('d M Y H:i');
        $filename = $this->reportService->filename($type, 'pdf');

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('meta', 'rows', 'period', 'printedAt'))
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function exportExcel(Request $request, string $type): BinaryFileResponse|RedirectResponse
    {
        if (! class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return redirect()->route('reports.index', ['type' => $this->reportService->normalizeType($type)])
                ->with('error', 'Export Excel belum tersedia. Jalankan composer require maatwebsite/excel terlebih dahulu.');
        }

        $type = $this->reportService->normalizeType($type);
        $meta = $this->reportService->type($type);
        $filters = $this->reportService->filters($request->query());
        $rows = $this->reportService->collection($type, $filters);
        $period = $this->reportService->periodLabel($filters, $meta['filters']['date']);
        $printedAt = now()->format('d M Y H:i');
        $filename = $this->reportService->filename($type, 'xlsx');

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($rows, $meta, $period, $printedAt, $rows->count()), $filename);
    }
}
