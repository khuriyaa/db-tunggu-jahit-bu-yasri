<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(): View
    {
        return $this->listing('all');
    }

    public function editIndex(): View
    {
        return $this->listing('edit');
    }

    public function deleteIndex(): View
    {
        return $this->listing('delete');
    }

    private function listing(string $actionPage): View
    {
        $users = User::with('role')->get();
        $adminCount = User::query()->whereHas('role', fn ($query) => $query->where('role_name', 'Admin'))->count();
        $roles = Role::query()
            ->whereIn('role_name', ['Admin', 'Owner', 'Customer'])
            ->orderBy('role_name')
            ->get();

        return view('users', compact('users', 'adminCount', 'roles', 'actionPage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(
                    fn ($query) => $query->whereIn('role_name', ['Admin', 'Owner', 'Customer'])
                ),
            ],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        DB::transaction(function () use ($validated): void {
            $role = Role::query()->findOrFail($validated['role_id']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'] ?? null,
                'password' => $validated['password'],
            ]);

            if ($role->role_name === 'Customer') {
                $user->customer()->create();
            }
        });

        return redirect()->route('users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $errorBag = 'updateUser'.$user->id;
        $validated = $request->validateWithBag($errorBag, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(
                    fn ($query) => $query
                        ->whereIn('role_name', ['Admin', 'Owner', 'Customer'])
                        ->orWhere('id', $user->role_id)
                ),
            ],
        ]);

        $result = DB::transaction(function () use ($validated, $user): ?string {
            $adminUsers = User::query()
                ->whereHas('role', fn ($query) => $query->where('role_name', 'Admin'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $lockedUser = $adminUsers->first(fn (User $admin): bool => $admin->is($user))
                ?? User::query()->lockForUpdate()->findOrFail($user->id);
            $role = Role::query()->findOrFail($validated['role_id']);

            if ($lockedUser->is(auth()->user()) && (int) $lockedUser->role_id !== (int) $role->id) {
                return 'Role akun sendiri tidak dapat diubah.';
            }

            if (
                $adminUsers->contains(fn (User $admin): bool => $admin->is($lockedUser))
                && $role->role_name !== 'Admin'
                && $adminUsers->count() <= 1
            ) {
                return 'Role Admin terakhir tidak dapat diubah. Jadikan pengguna lain sebagai Admin terlebih dahulu.';
            }

            $lockedUser->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role_id' => $role->id,
            ])->save();

            if ($role->role_name === 'Customer' && ! $lockedUser->customer()->exists()) {
                $lockedUser->customer()->create();
            }

            return null;
        });

        if ($result !== null) {
            return redirect()->route('users.manage.edit')
                ->withErrors(['role_id' => $result], $errorBag)
                ->withInput();
        }

        return redirect()->route('users.manage.edit')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 403, 'Admin tidak dapat menghapus akunnya sendiri.');

        $wasLastAdmin = DB::transaction(function () use ($user): bool {
            $adminUsers = User::query()
                ->whereHas('role', fn ($query) => $query->where('role_name', 'Admin'))
                ->lockForUpdate()
                ->get();

            if ($user->role?->role_name === 'Admin' && $adminUsers->count() <= 1) {
                return true;
            }

            $user->delete();

            return false;
        });

        if ($wasLastAdmin) {
            return redirect()->route('users.manage.delete')
                ->with('error', 'Akun Admin terakhir tidak dapat dihapus. Buat Admin lain terlebih dahulu.');
        }

        return redirect()->route('users.manage.delete')
            ->with('success', 'Pengguna beserta data terkait berhasil dihapus.');
    }
}
