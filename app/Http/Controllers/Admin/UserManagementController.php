<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()->latest()->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => User::roles(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(User::roles())],
        ]);

        $user->update([
            'role' => $data['role'],
        ]);

        return back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'role' => 'You cannot delete your own account while logged in.',
            ]);
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
