<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Tampilkan data pengeluaran operasional.
     */
    public function index(Request $request): View
    {
        return view('owner.expenses.index');
    }
}

