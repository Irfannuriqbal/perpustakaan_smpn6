<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitByMemberCodeRequest;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Student;
use App\Models\Visit;
use App\Services\VisitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class VisitController extends Controller
{
    public function __construct(private readonly VisitService $visitService)
    {
    }

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $class = $request->input('class');
        $visitDate = $request->input('visit_date');

        $visits = Visit::with('student')
            ->when($search, function ($query) use ($search): void {
                $query->whereHas('student', function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($class, function ($query) use ($class): void {
                $query->whereHas('student', fn ($query) => $query->where('class', $class));
            })
            ->when($visitDate, fn ($query) => $query->whereDate('visit_date', $visitDate))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $classes = Student::query()
            ->select('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        return view('visits.index', compact('visits', 'classes', 'search', 'class', 'visitDate'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('visits.index')->with('status', 'Scan kode anggota pada halaman kunjungan untuk mencatat kunjungan.');
    }

    public function store(StoreVisitByMemberCodeRequest $request): RedirectResponse
    {
        try {
            $visit = $this->visitService->recordByMemberCode($request->validated('member_code'));
            $student = $visit->student;
            $time = substr($visit->check_in_time, 0, 5);

            return redirect()
                ->route('visits.index')
                ->with('success', 'Kunjungan berhasil dicatat: '.$student->name.' (NIS '.$student->nis.', kelas '.$student->class.') pada '.$time.'.');
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors())->with('error', collect($exception->errors())->flatten()->first());
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Kunjungan gagal dicatat. Periksa kembali kode anggota.');
        }
    }

    public function show(Visit $visit): View
    {
        $visits = collect([$visit->load('student')]);
        $classes = collect();
        $search = null;
        $class = null;
        $visitDate = null;

        return view('visits.index', compact('visits', 'classes', 'search', 'class', 'visitDate'));
    }

    public function edit(Visit $visit): RedirectResponse
    {
        return redirect()->route('visits.index')->with('status', 'Form edit kunjungan akan dibuat pada tahap berikutnya.');
    }

    public function update(UpdateVisitRequest $request, Visit $visit): RedirectResponse
    {
        $visit->update($request->validated());

        return redirect()->route('visits.index')->with('status', 'Data kunjungan berhasil diperbarui.');
    }

    public function destroy(Visit $visit): RedirectResponse
    {
        $visit->delete();

        return redirect()->route('visits.index')->with('status', 'Data kunjungan berhasil dihapus.');
    }
}
