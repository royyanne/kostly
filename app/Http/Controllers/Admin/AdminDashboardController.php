<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan ringkasan metrik SaaS Super Admin.
     */
    public function index(): View
    {
        $totalOwners = User::where('role', UserRole::Owner)->count();
        $freeOwners = User::where('role', UserRole::Owner)->where('subscription_plan', SubscriptionPlan::Free)->count();
        $proOwners = User::where('role', UserRole::Owner)->where('subscription_plan', SubscriptionPlan::Pro)->count();
        $activeOwners = User::where('role', UserRole::Owner)->where('is_active', true)->count();
        $suspendedOwners = User::where('role', UserRole::Owner)->where('is_active', false)->count();

        $totalProperties = Property::count();
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', RoomStatus::Occupied)->count();
        $totalTenants = Tenant::where('is_active', true)->count();

        $recentOwners = User::where('role', UserRole::Owner)
            ->withCount(['properties', 'rooms'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalOwners',
            'freeOwners',
            'proOwners',
            'activeOwners',
            'suspendedOwners',
            'totalProperties',
            'totalRooms',
            'occupiedRooms',
            'totalTenants',
            'recentOwners'
        ));
    }
}
