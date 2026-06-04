<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show admin profile page
     */
    public function show()
    {
        return view('admin.profile');
    }

    /**
     * Update admin profile with photo upload
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,gif|max:2048'
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_url'] = $path;
        }

        // Update user
        $user->update($validated);

        // Return JSON response for AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'avatar_url' => $user->avatar_url ? asset('storage/' . $user->avatar_url) : null,
                'user' => $user
            ]);
        }

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profil berhasil disimpan!');
    }

    /**
     * Get current user profile (for real-time updates)
     */
    public function getCurrentProfile()
    {
        $user = auth()->user();
        
        // Generate avatar URL - use stored avatar or placeholder
        $avatarUrl = $user->avatar_url 
            ? asset('storage/' . $user->avatar_url)
            : $this->generatePlaceholderAvatar($user->name);
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $avatarUrl,
                'phone_number' => $user->phone_number,
                'address' => $user->address,
                'role' => $user->role
            ]
        ]);
    }

    /**
     * Generate placeholder avatar using UI Avatars service
     */
    private function generatePlaceholderAvatar($name)
    {
        $initials = collect(explode(' ', $name))
            ->map(fn($word) => substr($word, 0, 1))
            ->take(2)
            ->implode('')
            ->toUpper();

        return sprintf(
            'https://ui-avatars.com/api/?name=%s&background=00A32A&color=fff&size=128&bold=true&rounded=true',
            urlencode($initials)
        );
    }
}
