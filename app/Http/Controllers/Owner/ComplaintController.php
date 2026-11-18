<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Tampilkan laporan komplain penghuni.
     */
    public function index(Request $request): View
    {
        return view('owner.complaints.index');
    }
}
