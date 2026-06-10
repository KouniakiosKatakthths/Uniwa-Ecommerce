<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', new Enum(UserRole::class)],
        ]);

        // Prevent admin from changing their own role
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('profile.index')
                ->with('error', 'You cannot change your own role.');
        }

        $user->role = UserRole::from($validated['role']);
        $user->save();

        return redirect()
            ->route('profile.index')
            ->with('success', 'User role updated successfully.');
    }

    public function index()
    {
        $users = User::withCount('tickets')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.list-users', compact('users'));
    }

    public function editUser(User $user): View
    {
        return view('dashboard.update-user', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', new Enum(UserRole::class)],
            'email_verified' => ['nullable', 'boolean'],
        ]);

        //Prevent admin from editing their own role
        if (auth()->id() === $user->id && $validated['role'] !== $user->role->value) {
            return redirect()
                ->route('profile.edit-user', $user)
                ->with('error', 'You cannot change your own role.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = UserRole::from($validated['role']);

        if ($request->boolean('email_verified')) {
            $user->email_verified_at = $user->email_verified_at ?? now();
        } else {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('profile.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
