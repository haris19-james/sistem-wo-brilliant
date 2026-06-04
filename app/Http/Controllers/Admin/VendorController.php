<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\VendorDirectoryService;
use App\Support\ImageHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VendorController extends Controller
{
    public function __construct(
        protected VendorDirectoryService $directory
    ) {}

    public function index()
    {
        $filters = Cache::remember('admin.vendor.filters', now()->addHour(), function () {
            $vendors = Vendor::query()->select(['kategori', 'lokasi'])->get();

            return [
                'categories' => self::normalizeFilterList(
                    collect(config('brilliant.vendor_categories', []))
                        ->merge($vendors->pluck('kategori'))
                        ->all()
                ),
                'locations' => self::normalizeFilterList(
                    $this->directory->distinctLocations($vendors)->all()
                ),
                'total' => Vendor::count(),
            ];
        });

        return view('admin.modules.vendors.index', [
            'activeMenu' => 'vendor',
            'filterCategories' => self::normalizeFilterList($filters['categories'] ?? []),
            'filterLocations' => self::normalizeFilterList($filters['locations'] ?? []),
            'vendorTotal' => (int) ($filters['total'] ?? 0),
        ]);
    }

    /**
     * @param  array<int, mixed>  $items
     * @return list<string>
     */
    private static function normalizeFilterList(array $items): array
    {
        $flat = [];

        foreach ($items as $item) {
            if (is_string($item) && $item !== '') {
                $flat[] = $item;
            } elseif (is_numeric($item)) {
                $flat[] = (string) $item;
            } elseif (is_array($item)) {
                foreach ($item as $nested) {
                    if (is_string($nested) && $nested !== '') {
                        $flat[] = $nested;
                    } elseif (is_numeric($nested)) {
                        $flat[] = (string) $nested;
                    }
                }
            }
        }

        return collect($flat)->unique()->sort()->values()->all();
    }

    public function cards(Request $request): JsonResponse
    {
        $query = Vendor::query()
            ->select([
                'id', 'nama_vendor', 'kategori', 'lokasi', 'harga_info', 'kontak',
                'status', 'gambar', 'gambar_url', 'rating_avg', 'rating_count',
                'instagram', 'whatsapp', 'website',
            ])
            ->orderBy('nama_vendor');

        if ($request->filled('q')) {
            $q = $request->string('q')->trim();
            $query->where('nama_vendor', 'like', "%{$q}%");
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        $paginated = $query->paginate(24)->withQueryString();

        return response()->json([
            'data' => collect($paginated->items())->map(fn (Vendor $v) => $this->directory->cardPayload($v)),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
            ],
        ]);
    }

    public function detail(Vendor $vendor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'vendor' => $this->directory->detailPayload($vendor),
        ]);
    }

    public function create()
    {
        return view('admin.modules.vendors.form', [
            'activeMenu' => 'vendor',
            'vendor' => new Vendor(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['gambar'] = ImageHelper::storeUploaded($request->file('gambar'), 'vendors');
        if ($data['gambar']) {
            $data['gambar_url'] = null;
        }

        Vendor::create($data);
        Cache::forget('admin.vendor.filters');

        return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor)
    {
        return view('admin.modules.vendors.form', [
            'activeMenu' => 'vendor',
            'vendor' => $vendor,
        ]);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $data = $this->validated($request);
        $uploaded = ImageHelper::storeUploaded($request->file('gambar'), 'vendors', $vendor->gambar);
        if ($uploaded !== $vendor->gambar) {
            $data['gambar'] = $uploaded;
            if ($uploaded) {
                $data['gambar_url'] = null;
            }
        }

        $vendor->update($data);
        Cache::forget('admin.vendor.filters');

        return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        ImageHelper::delete($vendor->gambar);
        $vendor->delete();
        Cache::forget('admin.vendor.filters');

        return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'harga_info' => ['nullable', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'string', 'max:500'],
            'portfolio_urls' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
            'gambar' => ['nullable', 'image', 'max:5120'],
            'gambar_url' => ['nullable', 'url', 'max:500'],
        ]);

        $urls = collect(preg_split('/\r\n|\r|\n/', $data['portfolio_urls'] ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => filled($line) && filter_var($line, FILTER_VALIDATE_URL))
            ->take(6)
            ->values()
            ->all();

        unset($data['portfolio_urls']);
        $data['portfolio_images'] = $urls ?: null;

        return $data;
    }
}
