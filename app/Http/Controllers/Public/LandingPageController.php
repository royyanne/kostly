<?php

namespace App\Http\Controllers\Public;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Tampilkan landing page utama aplikasi Kostly beserta katalog pencarian kost publik.
     */
    public function __invoke(Request $request): View
    {
        $city = $request->query('city');
        $maxPrice = $request->query('max_price');
        $search = $request->query('search');

        $query = Property::query()
            ->with([
                'rooms' => fn ($q) => $q->where('status', RoomStatus::Available)->orderBy('price'),
            ])
            ->withCount([
                'rooms as available_rooms_count' => fn ($q) => $q->where('status', RoomStatus::Available),
            ]);

        if ($city) {
            $query->where(function ($q) use ($city) {
                $q->where('city', 'like', "%{$city}%")
                    ->orWhere('address', 'like', "%{$city}%");
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($maxPrice) {
            $query->whereHas('rooms', function ($q) use ($maxPrice) {
                $q->where('status', RoomStatus::Available)
                    ->where('price', '<=', (float) $maxPrice);
            });
        }

        $properties = $query->latest()->get();

        $availableCities = Property::query()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->toArray();

        if (empty($availableCities)) {
            $availableCities = ['Bandung', 'Jakarta', 'Yogyakarta', 'Surabaya', 'Malang'];
        }

        return view('public.landing', [
            'properties' => $properties,
            'availableCities' => $availableCities,
            'selectedCity' => $city,
            'maxPrice' => $maxPrice,
            'search' => $search,
        ]);
    }
}
