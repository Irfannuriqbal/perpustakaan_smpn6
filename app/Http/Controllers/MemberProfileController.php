<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Student;
use App\Services\CodeImageService;
use App\Services\LoanStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class MemberProfileController extends Controller
{
    public function __construct(private readonly LoanStatusService $loanStatusService)
    {
    }

    public function show(CodeImageService $codes): View
    {
        /** @var Student $member */
        $member = auth('member')->user();

        $this->loanStatusService->syncOverdueStatuses();

        $loans = Loan::query()
            ->with('items.book')
            ->where('student_id', $member->id)
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->orderBy('due_date')
            ->get();

        $activeItemsCount = $loans->sum(fn (Loan $loan): int => $loan->items
            ->whereNull('returned_at')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->count());

        $nearestDueDate = $loans->first()?->due_date;
        $qrCode         = $codes->qrSvg($member->member_code);

        // Riwayat peminjaman lengkap
        $loanHistory = Loan::query()
            ->with('items.book')
            ->where('student_id', $member->id)
            ->orderByDesc('loan_date')
            ->paginate(10);

        return view('members.profile', [
            'member'            => $member,
            'photoUrl'          => $member->photo_path ? asset('storage/' . $member->photo_path) : null,
            'loans'             => $loans,
            'activeItemsCount'  => $activeItemsCount,
            'nearestDueDate'    => $nearestDueDate,
            'qrCode'            => $qrCode,
            'loanHistory'       => $loanHistory,
            'loanStatusService' => $this->loanStatusService,
        ]);
    }

    public function edit(): View
    {
        /** @var Student $member */
        $member = auth('member')->user();

        return view('members.profile-edit', compact('member'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $member */
        $member = auth('member')->user();

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'photo'        => ['nullable', 'image', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        $previousPhotoPath = $member->photo_path;
        $newPhotoPath      = null;
        $data              = [
            'name'  => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ];

        try {
            if ($request->hasFile('photo')) {
                $newPhotoPath        = $request->file('photo')->store('member-photos', 'public');
                $data['photo_path']  = $newPhotoPath;
            } elseif ($request->boolean('remove_photo')) {
                $data['photo_path'] = null;
            }

            $member->update($data);

            if (array_key_exists('photo_path', $data) && $previousPhotoPath) {
                Storage::disk('public')->delete($previousPhotoPath);
            }

            return redirect()->route('members.profile')->with('success', 'Profil berhasil diperbarui.');
        } catch (Throwable) {
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            return back()->withInput()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }
}
