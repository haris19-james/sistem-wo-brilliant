<div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
    <div class="flex items-center gap-2 mb-4">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-green-50 text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </span>
        <div>
            <h2 class="text-lg font-bold text-gray-900">Notifikasi</h2>
            <p class="text-sm text-gray-500">Atur jenis notifikasi yang ingin Anda terima.</p>
        </div>
    </div>
    <div class="space-y-3">
        @php
            $notifItems = [
                ['label' => 'Notifikasi Chat', 'roles' => ['client', 'korlap', 'admin']],
                ['label' => 'Notifikasi Pembayaran', 'roles' => ['client', 'admin']],
                ['label' => 'Notifikasi Tugas', 'roles' => ['korlap', 'admin']],
                ['label' => 'Notifikasi Acara', 'roles' => ['client', 'korlap', 'admin']],
                ['label' => 'Notifikasi Kendala', 'roles' => ['korlap', 'admin']],
            ];
            $role = $settingsRoleKey ?? 'client';
        @endphp
        @foreach($notifItems as $notif)
            @if(in_array($role, $notif['roles'], true))
            <label class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                <span>{{ $notif['label'] }}</span>
                <input type="checkbox" checked class="h-5 w-5 rounded border-gray-300 text-green-600 focus:ring-green-500" />
            </label>
            @endif
        @endforeach
    </div>
    <p class="mt-4 text-xs text-gray-400">Preferensi disimpan di perangkat ini. Notifikasi in-app tetap tampil di ikon bell header.</p>
</div>
