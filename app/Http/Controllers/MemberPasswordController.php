<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMemberPasswordRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class MemberPasswordController extends Controller
{
    public function update(UpdateMemberPasswordRequest $request): RedirectResponse
    {
        /** @var Student $member */
        $member = auth('member')->user();

        if (! Hash::check($request->validated('current_password'), $member->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $member->update(['password' => $request->validated('password')]);
        $request->session()->regenerate();

        return redirect()->route('members.profile')->with('success', 'Password berhasil diubah.');
    }
}
