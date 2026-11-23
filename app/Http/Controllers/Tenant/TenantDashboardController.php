<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TenantDashboardController extends Controller
{
    /**
     * Tampilkan halaman utama portal anak kost / penyewa aktif.
     */
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        // Cari record penghuni aktif yang terhubung dengan akun ini
        $tenant = Tenant::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('phone', $user->phone);
            })
            ->where('is_active', true)
            ->with([
                'property.user',
                'room',
                'invoices' => fn ($q) => $q->latest('due_date'),
                'complaints' => fn ($q) => $q->latest(),
            ])
            ->first();

        // Jika tenant ditemukan tapi belum terisi user_id-nya, pasangkan sekarang
        if ($tenant && ! $tenant->user_id) {
            $tenant->update(['user_id' => $user->id]);
        }

        // Tagihan yang belum lunas
        $activeInvoice = $tenant?->invoices->first(fn ($inv) => $inv->status !== InvoiceStatus::Paid);

        // Riwayat pengajuan sewa (booking) user ini
        $recentBookings = Booking::where('user_id', $user->id)
            ->with(['property', 'room'])
            ->latest()
            ->get();

        return view('tenant.dashboard', [
            'user' => $user,
            'tenant' => $tenant,
            'activeInvoice' => $activeInvoice,
            'recentBookings' => $recentBookings,
        ]);
    }
}
