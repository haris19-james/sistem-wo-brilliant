<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    /**
     * Show settings page for Korlap
     */
    public function index()
    {
        $user = Auth::user();
        $activeMenu = 'pengaturan';
        
        return view('lapangan.modules.pengaturan.index', compact('user', 'activeMenu'));
    }

    /**
     * Update user profile with avatar upload support
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar_url')) {
            // Delete old avatar if exists
            if ($user->avatar_url && str_starts_with($user->avatar_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar_url')->store('avatars', 'public');
            $validated['avatar_url'] = '/storage/' . $path;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? $user->phone_number,
            'address' => $validated['address'] ?? $user->address,
            ...(isset($validated['avatar_url']) ? ['avatar_url' => $validated['avatar_url']] : []),
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan akun berhasil diperbarui',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url ?? '/images/default-avatar.png',
                    'phone_number' => $user->phone_number,
                    'address' => $user->address,
                    'role' => $user->role,
                ]
            ]);
        }

        return redirect()->route('lapangan.pengaturan')
            ->with('success', 'Pengaturan akun berhasil diperbarui');
    }

    /**
     * Get user profile as JSON (for real-time header sync)
     */
    public function apiProfile(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url ?? '/images/default-avatar.png',
            'role' => $user->role,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
        ]);
    }
}
