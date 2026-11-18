<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Tampilkan daftar dan status tagihan bulanan.
     */
    public function index(Request $request): View
    {
        return view('owner.invoices.index');
    }
}
