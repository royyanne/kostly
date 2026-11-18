<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class LandingPageController extends Controller
{
    /**
     * Tampilkan landing page utama aplikasi Kostly.
     */
    public function __invoke(): View
    {
        return view('public.landing');
    }
}

