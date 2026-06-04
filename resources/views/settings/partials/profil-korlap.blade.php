<div class="bg-white rounded-lg shadow-sm p-8 border border-gray-100">
    <h2 class="text-xl font-bold text-gray-900 mb-2">Profil Korlap</h2>
    <p class="text-sm text-gray-500 mb-6">Informasi koordinator lapangan untuk operasional acara.</p>

    <form action="{{ $updateRoute }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Tampilan Korlap</label>
                <input type="text" name="nama_lengkap" value="{{ $user->name }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Operasional</label>
                <input type="email" name="email" value="{{ $user->email }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp / Telepon</label>
                <input type="tel" name="nomor_telepon" value="{{ $user->phone ?? $user->phone_number ?? '' }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none"
                    placeholder="0812-3456-7890" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                <input type="text" value="Koordinator Lapangan (Korlap)" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500" />
            </div>
        </div>
        <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-green-700 transition">
            Simpan Profil Korlap
        </button>
    </form>
</div>
