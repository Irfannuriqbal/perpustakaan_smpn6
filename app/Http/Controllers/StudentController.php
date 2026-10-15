<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Services\CodeImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $class = $request->input('class');
        $status = $request->input('status');

        $students = Student::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($class, fn ($query) => $query->where('class', $class))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $classes = Student::query()
            ->select('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        return view('students.index', compact('students', 'classes', 'search', 'class', 'status'));
    }

    public function create(): View
    {
        return view('students.create', ['student' => new Student()]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        try {
            Student::create($request->validated());

            return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Data siswa gagal ditambahkan. Periksa kembali data yang diisi.');
        }
    }

    public function show(Student $student): View
    {
        return view('students.show', compact('student'));
    }

    public function printCards(Request $request, CodeImageService $codes): View
    {
        $search = $request->input('search');
        $class = $request->input('class');
        $status = $request->input('status');

        $students = Student::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($class, fn ($query) => $query->where('class', $class))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        $qrCodes = $students
            ->mapWithKeys(fn (Student $student): array => [$student->id => $codes->qrSvg($student->member_code)]);

        return view('students.print-cards', compact('students', 'qrCodes'));
    }

    public function printCard(Student $student, CodeImageService $codes): View
    {
        $students = collect([$student]);
        $qrCodes = collect([$student->id => $codes->qrSvg($student->member_code)]);

        return view('students.print-cards', compact('students', 'qrCodes'));
    }

    public function edit(Student $student): View
    {
        return view('students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        try {
            $student->update($request->validated());

            return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Data siswa gagal diperbarui. Periksa kembali data yang diisi.');
        }
    }

    public function destroy(Student $student): RedirectResponse
    {
        try {
            $student->delete();

            return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
        } catch (Throwable) {
            return redirect()->route('students.index')->with('error', 'Data siswa gagal dihapus karena masih terhubung dengan data peminjaman atau kunjungan.');
        }
    }
}
