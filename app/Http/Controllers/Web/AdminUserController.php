<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderBy(User::NAME)
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function new(): View
    {
        $user = new User;

        return $this->editOrNew($user);
    }

    public function edit(User $user): View
    {
        return $this->editOrNew($user);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = $request->input('id')
            ? User::findOrFail($request->input('id'))
            : new User;

        $user->fill($validated);
        $user->save();

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index');
    }

    private function editOrNew(User $user): View
    {
        return view('admin.users.form', [
            'user' => $user,
        ]);
    }
}
