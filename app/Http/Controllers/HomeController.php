<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Vendor;
use App\Support\BlogPosts;

class HomeController extends Controller
{
    public function index()
    {
        $pakets = Paket::standar()->orderBy('harga')->take(3)->get();
        $paketKustom = Paket::kustom()->first();
        $vendors = Vendor::aktif()->take(4)->get();
        $blogPosts = array_slice(BlogPosts::all(), 0, 3);

        return view('pages.home', [
            'activeNav' => 'home',
            'pakets' => $pakets,
            'paketKustom' => $paketKustom,
            'vendors' => $vendors,
            'blogPosts' => $blogPosts,
        ]);
    }
}
