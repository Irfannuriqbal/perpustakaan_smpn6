<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Services\CodeImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search      = $request->input('search');
        $memberType  = $request->input('member_type');
        $class       = $request->input('class');
        $status      = $request->input('status');

        $members = Student::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('no', 'like', "%{$search}%")
                      ->orWhere('member_code', 'like', "%{$search}%");
                });
            })
            ->when($memberType, fn ($q) => $q->where('member_type', $memberType))
            ->when($class,      fn ($q) => $q->where('class', $class))
            ->when($status,     fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $classes = Student::query()
            ->whereNotNull('class')
            ->select('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        return view('anggota.index', compact('members', 'classes', 'search', 'memberType', 'class', 'status'));
    }

    public function create(): View
    {
        return view('anggota.create', ['member' => new Student()]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated     = $request->safe()->except('photo');
        $newPhotoPath  = null;

        // Auto-generate member_code from 'no' if not provided
        if (empty($validated['member_code'])) {
            $validated['member_code'] = 'AGT-' . strtoupper(Str::random(6));
        }

        try {
            if ($request->hasFile('photo')) {
                $newPhotoPath             = $request->file('photo')->store('member-photos', 'public');
                $validated['photo_path']  = $newPhotoPath;
            }

            Student::create($validated);

            return redirect()->route('members.index')->with('success', 'Data anggota berhasil ditambahkan.');
        } catch (Throwable) {
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            return back()->withInput()->with('error', 'Data anggota gagal ditambahkan. Periksa kembali data yang diisi.');
        }
    }

    public function show(Student $member): View
    {
        return view('anggota.show', compact('member'));
    }

    public function printCards(Request $request, CodeImageService $codes): View
    {
        $search     = $request->input('search');
        $memberType = $request->input('member_type');
        $class      = $request->input('class');
        $status     = $request->input('status');

        $members = Student::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('no', 'like', "%{$search}%");
                });
            })
            ->when($memberType, fn ($q) => $q->where('member_type', $memberType))
            ->when($class,      fn ($q) => $q->where('class', $class))
            ->when($status,     fn ($q) => $q->where('status', $status))
            ->orderBy('name')
            ->get();

        $qrCodes = $members->mapWithKeys(
            fn (Student $m): array => [$m->id => $codes->qrSvg($m->member_code)]
        );

        return view('anggota.print-cards', compact('members', 'qrCodes'));
    }

    public function printCard(Student $member, CodeImageService $codes): View
    {
        $members = collect([$member]);
        $qrCodes = collect([$member->id => $codes->qrSvg($member->member_code)]);

        return view('anggota.print-cards', compact('members', 'qrCodes'));
    }

    public function edit(Student $member): View
    {
        return view('anggota.edit', compact('member'));
    }

    public function update(UpdateStudentRequest $request, Student $member): RedirectResponse
    {
        $validated         = $request->safe()->except(['photo', 'remove_photo']);
        $previousPhotoPath = $member->photo_path;
        $newPhotoPath      = null;

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        try {
            if ($request->hasFile('photo')) {
                $newPhotoPath             = $request->file('photo')->store('member-photos', 'public');
                $validated['photo_path']  = $newPhotoPath;
            } elseif ($request->boolean('remove_photo')) {
                $validated['photo_path'] = null;
            }

            $member->update($validated);

            if (array_key_exists('photo_path', $validated) && $previousPhotoPath) {
                Storage::disk('public')->delete($previousPhotoPath);
            }

            return redirect()->route('members.index')->with('success', 'Data anggota berhasil diperbarui.');
        } catch (Throwable) {
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            return back()->withInput()->with('error', 'Data anggota gagal diperbarui. Periksa kembali data yang diisi.');
        }
    }

    public function destroy(Student $member): RedirectResponse
    {
        $photoPath = $member->photo_path;

        try {
            $member->delete();

            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return redirect()->route('members.index')->with('success', 'Data anggota berhasil dihapus.');
        } catch (Throwable) {
            return redirect()->route('members.index')
                ->with('error', 'Data anggota gagal dihapus karena masih terhubung dengan data peminjaman atau kunjungan.');
        }
    }
}
