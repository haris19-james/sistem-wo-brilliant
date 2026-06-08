<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return $this->redirectAfterLogin(auth()->user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            $request->session()->put('user_role', $role);

            return $this->redirectAfterLogin($role);
        }

        return back()
            ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
            ->onlyInput('email');
    }

    public function showAdminLogin()
    {
        return redirect()->route('login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'Akun ini bukan admin.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Email atau kata sandi admin tidak sesuai.'])
            ->onlyInput('email');
    }

    public function showLapanganLogin()
    {
        return redirect()->route('login');
    }

    public function lapanganLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (Auth::user()->role !== 'lapangan') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'Akun ini bukan tim lapangan.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('lapangan.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        if (auth()->check()) {
            return $this->redirectAfterLogin(auth()->user()->role);
        }

        return view('auth.register');
    }

    public function register(Request $request): JsonResponse|RedirectResponse
    {
        if (auth()->check()) {
            return $this->redirectAfterLogin(auth()->user()->role);
        }

        $wantsJson = $this->registerWantsJson($request);

        try {
            $validated = $request->validate(
                [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                    'password' => ['required', 'string', 'min:8', 'confirmed'],
                ],
                [
                    'name.required' => 'Nama lengkap wajib diisi.',
                    'email.required' => 'Email wajib diisi.',
                    'email.email' => 'Format email tidak valid.',
                    'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau masuk.',
                    'password.required' => 'Kata sandi wajib diisi.',
                    'password.confirmed' => 'Password tidak cocok.',
                    'password.min' => 'Kata sandi minimal 8 karakter.',
                ]
            );

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'client',
            ]);

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $request->session()->put('user_role', $user->role);

            $successMessage = 'Selamat! Anda berhasil membuat akun dan sudah masuk.';

            $request->session()->flash('success', $successMessage);
            $request->session()->put('skip_expire_sync', true);

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'redirect' => route('client.dashboard'),
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ]);
            }

            return redirect()
                ->route('client.dashboard')
                ->with('success', $successMessage);
        } catch (ValidationException $e) {
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?? 'Data tidak valid.',
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        } catch (QueryException $e) {
            Log::warning('Register query error', ['message' => $e->getMessage()]);

            $message = 'Email sudah terdaftar. Silakan gunakan email lain atau masuk.';
            $lower = strtolower($e->getMessage());

            if (str_contains($lower, 'duplicate') && str_contains($lower, 'email')) {
                $message = 'Email sudah terdaftar. Silakan gunakan email lain atau masuk.';
            } elseif (str_contains($lower, "'role'") || str_contains($lower, 'column \'role\'')) {
                $message = 'Pendaftaran sementara tidak tersedia. Hubungi administrator.';
            } elseif (! str_contains($lower, 'duplicate')) {
                $message = 'Gagal membuat akun: '.(config('app.debug') ? $e->getMessage() : 'periksa log server.');
            }

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => ['email' => [$message]],
                ], 422);
            }

            return back()->withInput()->withErrors(['email' => $message]);
        } catch (\Throwable $e) {
            Log::error('Register failed', ['exception' => $e]);

            $message = 'Terjadi kesalahan server. Silakan coba lagi nanti.';

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return back()->withInput()->withErrors(['email' => $message]);
        }
    }

    protected function redirectAfterLogin(?string $role)
    {
        return match ($role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'lapangan' => redirect()->intended(route('lapangan.dashboard')),
            'client' => redirect()->intended(route('client.dashboard')),
            default => abort(403, 'Peran pengguna tidak diizinkan.'),
        };
    }

    protected function registerWantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->wantsJson()
            || $request->ajax();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
