<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('name')
            ->paginate(10);

        return view('users.index', compact('admins'));
    }

    public function create(): View
    {
        return view('users.create', ['user' => new User()]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        User::create($request->validated() + ['role' => User::ROLE_ADMIN]);

        return redirect()->route('users.index')->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $this->ensureAdmin($user);

        return view('users.edit', compact('user'));
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $this->ensureAdmin($user);
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function status(User $user): RedirectResponse
    {
        $this->ensureAdmin($user);
        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('users.index')->with('success', 'Status akun admin berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureAdmin($user);

        try {
            $user->delete();

            return redirect()->route('users.index')->with('success', 'Akun admin berhasil dihapus.');
        } catch (Throwable) {
            return redirect()->route('users.index')->with('error', 'Akun admin tidak dapat dihapus karena masih terhubung dengan transaksi peminjaman.');
        }
    }

    private function ensureAdmin(User $user): void
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404);
    }
}
