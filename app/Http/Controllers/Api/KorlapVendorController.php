<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\KorlapVendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KorlapVendorController extends Controller
{
    public function __construct(
        protected KorlapVendorService $korlapVendorService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $korlapId = (int) auth()->id();

        $vendors = $this->korlapVendorService
            ->vendorsQuery($request, $korlapId)
            ->get();

        return response()->json([
            'vendors' => $vendors->map(
                fn (Vendor $v) => $this->korlapVendorService->serializeListItem($v)
            )->values(),
        ]);
    }

    public function show(Vendor $vendor): JsonResponse
    {
        return response()->json(
            $this->korlapVendorService->serializeDetail($vendor, (int) auth()->id())
        );
    }
}
