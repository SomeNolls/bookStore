<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public const TABS = ['orders', 'rentals', 'profile'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * The signed-in user's account page; the {user} in the URL is only kept for old links.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($user)
    {
        $user = auth()->user();

        return view('profile/profile', [
            'user' => $user,
            'orders' => $user->orders()->with('items')->latest()->get(),
            'rentals' => $user->rentals()->with('book')->latest()->get(),
            'tab' => in_array(request('tab'), self::TABS) ? request('tab') : 'orders',
        ]);
    }

    public function update()
    {
        $user = auth()->user();

        $data = request()->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        // The profile only changes the email; the name stays as it was given at registration.
        $user->update(['email' => $data['email']]);

        return redirect()->route('profile.show', ['user' => $user->id, 'tab' => 'profile'])
            ->with('status', 'Your profile is saved.');
    }

    public function updatePassword()
    {
        $data = request()->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()],
        ]);

        auth()->user()->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('profile.show', ['user' => auth()->id(), 'tab' => 'profile'])
            ->with('status', 'Your password is changed.');
    }

    public function returnBook($id)
    {
        // Rentals still waiting on their order can't be returned yet.
        $rental = auth()->user()->rentals()->whereNotNull('returnDate')->findOrFail($id);

        optional($rental->book)->increment('forRent');
        $rental->delete();

        return redirect()->route('profile.show', ['user' => auth()->id(), 'tab' => 'rentals']);
    }
}
