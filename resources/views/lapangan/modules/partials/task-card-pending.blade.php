<div class="bg-white border border-red-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition" data-task-id="{{ $task->id }}" data-task-name="{{ $task->nama_tugas }}">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $task->nama_tugas }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $task->pesanan?->nama_pasangan ?? 'Acara tidak diketahui' }}</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <span data-status-badge class="inline-flex items-center px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold">Belum Dikerjakan</span>
            <select class="task-status-select text-sm px-2 py-1 border border-gray-200 rounded" data-task-id="{{ $task->id }}" data-prev-status="pending">
                <option value="pending" selected>Belum Dikerjakan</option>
                <option value="in_progress">Sedang Dikerjakan</option>
                <option value="completed">Selesai</option>
            </select>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-gray-600">
        <div class="space-y-1">
            <p class="font-semibold text-gray-900">Deadline</p>
            <p>{{ optional($task->deadline)->format('d M Y H:i') ?? '-' }}</p>
        </div>
        <div class="space-y-1">
            <p class="font-semibold text-gray-900">Prioritas</p>
            <p class="capitalize">{{ $task->prioritas ?? '-' }}</p>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between gap-3">
        <div class="text-sm text-gray-500">{{ Str::limit($task->catatan ?? 'Tidak ada catatan tambahan.', 80) }}</div>
        <a href="{{ route('lapangan.tugas.edit', $task) }}" class="text-field hover:text-field/80 font-semibold">Detail</a>
    </div>
</div>
