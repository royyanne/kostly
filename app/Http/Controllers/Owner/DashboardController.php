<?php

namespace App\Http\Controllers\Owner;

use App\Enums\ComplaintStatus;
use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard pemilik kost dengan ringkasan statistik.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        // Data properti pemilik
        $properties = $user->properties()->withCount(['rooms', 'tenants'])->get();
        $selectedPropertyId = $request->query('property_id');

        $roomsQuery = $user->rooms()->with(['property', 'currentTenant']);
        $invoicesQuery = $user->invoices();
        $expensesQuery = $user->expenses();
        $tenantsQuery = $user->tenants()->where('is_active', true);
        $complaintsQuery = $user->complaints()->with(['tenant', 'room']);

        if ($selectedPropertyId) {
            $roomsQuery->where('rooms.property_id', $selectedPropertyId);
            $invoicesQuery->where('invoices.property_id', $selectedPropertyId);
            $expensesQuery->where('expenses.property_id', $selectedPropertyId);
            $tenantsQuery->where('tenants.property_id', $selectedPropertyId);
            $complaintsQuery->where('complaints.property_id', $selectedPropertyId);
        }

        // Statistik Kamar
        $totalRooms = (clone $roomsQuery)->count();
        $occupiedRooms = (clone $roomsQuery)->where('status', RoomStatus::Occupied)->count();
        $availableRooms = (clone $roomsQuery)->where('status', RoomStatus::Available)->count();
        $maintenanceRooms = (clone $roomsQuery)->where('status', RoomStatus::Maintenance)->count();
        $occupancyRate = $totalRooms > 0 ? (int) round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Statistik Keuangan Bulan Ini
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyIncome = (clone $invoicesQuery)
            ->where('status', InvoiceStatus::Paid)
            ->whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->sum('total_amount');

        $pendingIncome = (clone $invoicesQuery)
            ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Overdue])
            ->sum('total_amount');

        $monthlyExpenses = (clone $expensesQuery)
            ->whereYear('expense_date', $currentYear)
            ->whereMonth('expense_date', $currentMonth)
            ->sum('amount');

        $netProfit = $monthlyIncome - $monthlyExpenses;

        // Kamar untuk visual grid preview
        $previewRooms = (clone $roomsQuery)->orderBy('room_number')->get();

        // Tagihan mendesak (Unpaid & Overdue terdekat)
        $urgentInvoices = (clone $invoicesQuery)
            ->with(['tenant.room', 'property'])
            ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Overdue])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Komplain aktif
        $activeComplaints = (clone $complaintsQuery)
            ->whereIn('status', [ComplaintStatus::Pending, ComplaintStatus::InProgress])
            ->latest()
            ->take(4)
            ->get();

        return view('owner.dashboard', [
            'user' => $user,
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'availableRooms' => $availableRooms,
            'maintenanceRooms' => $maintenanceRooms,
            'occupancyRate' => $occupancyRate,
            'activeTenantsCount' => $tenantsQuery->count(),
            'monthlyIncome' => $monthlyIncome,
            'pendingIncome' => $pendingIncome,
            'monthlyExpenses' => $monthlyExpenses,
            'netProfit' => $netProfit,
            'previewRooms' => $previewRooms,
            'urgentInvoices' => $urgentInvoices,
            'activeComplaints' => $activeComplaints,
        ]);
    }
}
