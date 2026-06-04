<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\SettingsMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('customer.modules.profile.show', [
            'activeMenu' => 'profile',
            'user' => Auth::user(),
        ]);
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        $section = SettingsMenu::resolveSection($user, $request->query('tab'));
        $roleKey = SettingsMenu::roleKey($user);

        return view('customer.modules.profile.edit', [
            'activeMenu' => 'settings',
            'user' => $user,
            'settingsSection' => $section,
            'settingsMenuItems' => SettingsMenu::forUser($user, route('client.profile.edit')),
            'settingsSubtitle' => SettingsMenu::subtitle($roleKey),
            'settingsRoleKey' => $roleKey,
            'settingsShowsAdminPanels' => SettingsMenu::showsAdminPanels($roleKey),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->address = $validated['address'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('client.profile.edit', ['tab' => 'pengaturan_akun'])
            ->with('success', 'Pengaturan akun berhasil disimpan.');
    }
}
