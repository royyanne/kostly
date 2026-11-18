<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard pemilik kost dengan ringkasan statistik.
     */
    public function index(Request $request): View
    {
        return view('owner.dashboard');
    }
}
