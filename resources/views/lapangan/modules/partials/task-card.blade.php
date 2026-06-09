@php
    $status = $task->status;
    $borderClass = match ($status) {
        'pending' => 'border-green-100',
        'in_progress', 'awaiting_verification' => 'border-green-200',
        'completed' => 'border-green-300',
        default => 'border-gray-100',
    };
    $badgeClass = match ($status) {
        'pending' => 'bg-gray-100 text-gray-700',
        'in_progress' => 'bg-green-50 text-green-700',
        'awaiting_verification' => 'bg-amber-50 text-amber-800 border border-amber-200',
        'completed' => 'bg-green-100 text-green-800',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="task-card bg-white border {{ $borderClass }} rounded-2xl p-5 shadow-sm hover:shadow-md transition"
     data-task-id="{{ $task->id }}"
     data-task-name="{{ $task->nama_tugas }}"
     data-pesanan-id="{{ $task->pesanan_id }}"
     data-vendor-id="{{ $task->vendor_id }}"
     data-status="{{ $status }}"
     data-prioritas="{{ $task->prioritas }}">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <h3 class="text-base font-bold text-gray-900 truncate">{{ $task->nama_tugas }}</h3>
                @if($task->is_auto_generated)
                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-green-50 text-green-600 border border-green-100">Rutin</span>
                @else
                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100">Ad-hoc</span>
                @endif
            </div>
            <p class="text-xs text-gray-500">{{ $task->pesanan?->nama_pasangan ?? '—' }} · {{ $task->pesanan?->nomor_pesanan }}</p>
            @if($task->vendor)
            <p class="text-xs text-green-600 font-medium mt-0.5">{{ $task->vendor->nama_vendor }} ({{ $task->vendor->kategori }})</p>
            @endif
        </div>
        <span data-status-badge class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $badgeClass }}">
            {{ $task->status_label }}
        </span>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-600">
        <div>
            <p class="font-semibold text-gray-800">Deadline</p>
            <p>{{ optional($task->deadline)->format('d M Y H:i') ?? '—' }}</p>
        </div>
        <div>
            <p class="font-semibold text-gray-800">PIC</p>
            <p>{{ $task->pic?->name ?? '—' }}</p>
        </div>
        <div class="col-span-2">
            <p class="font-semibold text-gray-800">Progress</p>
            <p class="text-green-600 font-semibold">{{ $task->progress }}%</p>
        </div>
    </div>

    @if($status === 'in_progress' && !empty($task->alasan_penolakan))
    <div class="mt-3 p-3 bg-red-50 border border-red-100 rounded-lg text-xs">
        <p class="font-bold text-red-800 mb-1">Ditolak oleh Admin:</p>
        <p class="text-red-700">{{ $task->alasan_penolakan }}</p>
    </div>
    @endif

    @if($status !== 'completed')
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <select class="task-status-select text-xs px-2 py-1.5 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none"
            data-task-id="{{ $task->id }}" data-prev-status="{{ $status }}">
            <option value="pending" @selected($status === 'pending')>Belum Dikerjakan</option>
            <option value="in_progress" @selected($status === 'in_progress')>Sedang Dikerjakan</option>
        </select>

        @if($status === 'in_progress')
        <button type="button" class="px-3 py-1.5 bg-bottle hover:bg-bottleHover text-white text-xs font-semibold rounded-lg transition"
            onclick="openUploadModal('{{ $task->id }}')">
            Kirim Laporan
        </button>
        @endif
    </div>
    @else
    <p class="mt-2 text-[10px] text-green-600">✓ Selesai</p>
    @endif

    <div class="mt-3 flex items-center justify-between gap-2 border-t border-gray-50 pt-2">
        <p class="text-xs text-gray-500 flex-1">{{ Str::limit($task->catatan ?? '—', 60) }}</p>
        <a href="{{ route('lapangan.tugas.edit', $task) }}" class="text-xs font-semibold text-green-600 hover:text-green-700">Detail</a>
    </div>
</div>
