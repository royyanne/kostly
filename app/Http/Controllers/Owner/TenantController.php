<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Tampilkan data penghuni kost.
     */
    public function index(Request $request): View
    {
        return view('owner.tenants.index');
    }
}
