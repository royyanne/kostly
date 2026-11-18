<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Tampilkan daftar properti milik pemilik kost.
     */
    public function index(Request $request): View
    {
        return view('owner.properties.index');
    }
}
