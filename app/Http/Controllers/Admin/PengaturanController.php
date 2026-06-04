<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SettingsMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $section = SettingsMenu::resolveSection($user, $request->query('tab'));
        $roleKey = SettingsMenu::roleKey($user);

        return view('admin.modules.pengaturan.index', [
            'user' => $user,
            'activeMenu' => 'pengaturan',
            'settingsSection' => $section,
            'settingsMenuItems' => SettingsMenu::forUser($user, route('admin.pengaturan')),
            'settingsSubtitle' => SettingsMenu::subtitle($roleKey),
            'settingsRoleKey' => $roleKey,
            'settingsShowsAdminPanels' => SettingsMenu::showsAdminPanels($roleKey),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nomor_telepon' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'phone_number' => $validated['nomor_telepon'] ?? $user->phone_number,
        ]);

        return redirect()
            ->route('admin.pengaturan', ['tab' => 'pengaturan_akun'])
            ->with('success', 'Pengaturan akun berhasil diperbarui.');
    }
}
