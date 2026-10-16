<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberAuthController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('members.card');
        }

        return view('members.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nis' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('member')->attempt($credentials + ['status' => 'aktif'])) {
            return back()
                ->withInput($request->only('nis'))
                ->withErrors(['nis' => 'NIS atau password tidak sesuai.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('members.card'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();

        $request->session()->regenerateToken();

        return redirect()->route('members.login')->with('status', 'Anda berhasil keluar.');
    }
}
