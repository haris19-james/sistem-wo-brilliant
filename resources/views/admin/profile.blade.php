@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-grayBg py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-grayText hover:text-bottle">
                ← Kembali ke Dashboard
            </a>
        </div>

        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-leafSoft to-leafSoft px-8 py-8 border-b border-gray-100">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Profil Akun Admin</h1>
                <p class="text-gray-600">Kelola informasi profil dan foto akun Anda</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <form id="profileForm" 
                      action="{{ route('admin.profile.update') }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      @submit.prevent="handleSubmit($event)"
                      x-data="profileForm()">
                    @csrf
                    @method('PATCH')

                    <!-- Photo Upload Section -->
                    <div class="mb-10">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-bottle" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            Foto Profil
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Photo Preview -->
                            <div class="flex flex-col items-center">
                                <div class="relative mb-4">
                                            <!-- Circle Frame -->
                                    <div class="w-32 h-32 rounded-full border-4 border-gray-200 bg-gray-50 overflow-hidden shadow-lg">
                                        <img id="photoPreview" 
                                             :src="photoUrl || '{{ auth()->user()->getAvatarUrlAttribute() }}'"
                                             alt="Foto Profil"
                                             class="w-full h-full object-cover">
                                    </div>
                                    <!-- Badge status -->
                                    <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-bottle rounded-full flex items-center justify-center border-2 border-white shadow">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 text-center">
                                    {{ auth()->user()->name }}
                                </p>
                            </div>

                            <!-- Upload Area -->
                            <div class="md:col-span-2">
                                <!-- File Input -->
                                <div class="mb-4">
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-bottle hover:bg-leafSoft transition cursor-pointer group"
                                         @click="$refs.photoInput.click()">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-bottle mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-900 mb-1">
                                            Klik atau drag gambar ke sini
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Format: JPG, PNG, GIF (Max 2MB)
                                        </p>
                                    </div>
                                    <input type="file" 
                                           ref="photoInput"
                                           name="avatar" 
                                           accept="image/jpeg,image/png,image/gif"
                                           @change="handlePhotoChange($event)"
                                           class="hidden">
                                </div>

                                <!-- Info Box -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <p class="text-sm text-blue-700">
                                            <strong>Tips:</strong> Gunakan foto berkualitas tinggi dengan rasio 1:1 (square) untuk hasil terbaik.
                                        </p>
                                    </div>
                                </div>

                                <!-- File Info -->
                                <div x-show="fileName" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm text-green-700 font-medium">
                                        <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span x-text="fileName"></span> siap diupload
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-10"></div>

                    <!-- Info Section -->
                    <div class="mb-10">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-bottle" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                            </svg>
                            Informasi Akun
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap
                                </label>
                                <input type="text" 
                                       name="name" 
                                       value="{{ auth()->user()->name }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bottle focus:border-transparent"
                                       required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ auth()->user()->email }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bottle focus:border-transparent"
                                       required>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="tel" 
                                       name="phone_number" 
                                       value="{{ auth()->user()->phone_number ?? '' }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bottle focus:border-transparent">
                            </div>

                            <!-- Role (Read Only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Role
                                </label>
                                <div class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 font-medium">
                                    Admin
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat
                            </label>
                            <textarea name="address" 
                                      rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bottle focus:border-transparent">{{ auth()->user()->address ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-10"></div>

                    <!-- Account Info -->
                    <div class="mb-10 bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h3 class="font-medium text-gray-900 mb-4">Informasi Akun</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Akun dibuat:</span>
                                <span class="font-medium text-gray-900">{{ auth()->user()->created_at->locale('id')->translatedFormat('d F Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir update:</span>
                                <span class="font-medium text-gray-900">{{ auth()->user()->updated_at->locale('id')->translatedFormat('d F Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-4">
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="px-8 py-3 bg-bottle hover:bg-bottleHover text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg x-show="isSubmitting" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>

                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-8 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-lg transition">
                            Batal
                        </a>
                    </div>
                </form>

                <!-- Success Alert -->
                <div x-show="showSuccess" 
                     @click.away="showSuccess = false"
                     class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-medium text-green-800">Profil berhasil disimpan!</p>
                        <p class="text-sm text-green-700">Foto Anda akan diperbarui di header dalam beberapa saat.</p>
                    </div>
                </div>

                <!-- Error Alert -->
                <div x-show="errorMessage" 
                     @click.away="errorMessage = ''"
                     class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-medium text-red-800" x-text="errorMessage"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function profileForm() {
    return {
        fileName: '',
        photoUrl: '',
        isSubmitting: false,
        showSuccess: false,
        errorMessage: '',

        handlePhotoChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                this.errorMessage = 'Ukuran file terlalu besar. Maksimal 2MB.';
                setTimeout(() => this.errorMessage = '', 3000);
                return;
            }

            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                this.errorMessage = 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF.';
                setTimeout(() => this.errorMessage = '', 3000);
                return;
            }

            this.fileName = file.name;

            // Preview image
            const reader = new FileReader();
            reader.onload = (e) => {
                this.photoUrl = e.target.result;
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        async handleSubmit(event) {
            this.isSubmitting = true;
            this.errorMessage = '';

            try {
                const formData = new FormData(event.target);
                const response = await fetch(event.target.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal menyimpan profil');
                }

                const result = await response.json();

                // Show success message
                this.showSuccess = true;
                setTimeout(() => this.showSuccess = false, 5000);

                // Update user profile in header
                if (this.photoUrl) {
                    // Broadcast event to update header
                    window.dispatchEvent(new CustomEvent('profile-updated', {
                        detail: { avatar_url: result.avatar_url }
                    }));
                }

                // Reset file input
                this.$refs.photoInput.value = '';
                this.fileName = '';

            } catch (error) {
                console.error('Error:', error);
                this.errorMessage = error.message || 'Terjadi kesalahan saat menyimpan profil.';
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endsection
