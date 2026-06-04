<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Vendor;
use App\Support\BlogPosts;
use App\Support\Branding;
use App\Support\PaketBudgetMatcher;
use App\Support\VendorCategories;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function paket()
    {
        $paketKustom = Paket::kustom()->first();
        $pakets = Paket::standar()->orderBy('harga')->get();

        return view('pages.paket', [
            'activeNav' => 'paket',
            'pakets' => $pakets,
            'paketKustom' => $paketKustom,
            'paketStandarJson' => PaketBudgetMatcher::standarForJs(),
            'minBudget' => config('brilliant.paket_kustom_min_budget', 10_000_000),
        ]);
    }

    public function vendor(Request $request)
    {
        $activeSlug = $request->query('kategori', 'semua');
        $kategori = VendorCategories::resolve($activeSlug);

        $vendors = Vendor::aktif()
            ->kategori($kategori)
            ->orderBy('nama_vendor')
            ->get();

        $totalAktif = Vendor::aktif()->count();

        return view('pages.vendor', [
            'activeNav' => 'vendor',
            'vendors' => $vendors,
            'categories' => VendorCategories::forFilter(),
            'activeCategory' => $activeSlug,
            'activeCategoryLabel' => $kategori,
            'totalAktif' => $totalAktif,
        ]);
    }

    public function vendorDetail(Vendor $vendor)
    {
        if ($vendor->status !== 'Aktif') {
            abort(404);
        }

        $related = Vendor::where('status', 'Aktif')
            ->where('kategori', $vendor->kategori)
            ->where('id', '!=', $vendor->id)
            ->take(4)
            ->get();

        return view('pages.vendor-detail', [
            'activeNav' => 'vendor',
            'vendor' => $vendor,
            'related' => $related,
        ]);
    }

    public function about()
    {
        return view('pages.about', ['activeNav' => 'about']);
    }

    public function blog(Request $request)
    {
        $category = $request->query('kategori', 'semua');
        $categories = config('brilliant.blog_categories', []);
        $posts = BlogPosts::filterByCategory($category === 'semua' ? null : $category);

        return view('pages.blog', [
            'activeNav' => 'blog',
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $category,
        ]);
    }

    public function blogShow(string $slug)
    {
        $post = BlogPosts::find($slug);
        if (! $post) {
            abort(404);
        }

        $related = array_values(array_filter(
            BlogPosts::all(),
            fn ($p) => $p['slug'] !== $slug && $p['category'] === $post['category']
        ));
        $related = array_slice($related, 0, 3);

        return view('pages.blog-show', [
            'activeNav' => 'blog',
            'post' => $post,
            'related' => $related,
        ]);
    }

    public function contact()
    {
        return view('pages.contact', ['activeNav' => 'contact']);
    }

    public function contactStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'subjek' => ['required', 'string', 'max:150'],
            'pesan' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $waMessage = "Halo Brilliant WO,\n\n".
            "Nama: {$validated['nama']}\n".
            "Email: {$validated['email']}\n".
            (! empty($validated['telepon']) ? "Telepon: {$validated['telepon']}\n" : '').
            "Subjek: {$validated['subjek']}\n\n".
            $validated['pesan'];

        return redirect()->away(Branding::whatsappUrl($waMessage));
    }
}
