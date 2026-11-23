<?php

namespace App\Http\Controllers\Owner;

use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Tampilkan daftar pengajuan sewa kamar dari calon penyewa.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $statusFilter = $request->query('status');

        $baseQuery = Booking::forOwner($user)->with(['property', 'room', 'user']);

        $pendingCount = (clone $baseQuery)->where('status', BookingStatus::Pending)->count();
        $approvedCount = (clone $baseQuery)->where('status', BookingStatus::Approved)->count();
        $rejectedCount = (clone $baseQuery)->where('status', BookingStatus::Rejected)->count();

        if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
            $baseQuery->where('status', $statusFilter);
        }

        $bookings = $baseQuery->latest()->get();

        return view('owner.bookings.index', [
            'user' => $user,
            'bookings' => $bookings,
            'statusFilter' => $statusFilter,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }

    /**
     * Setujui (Approve) pengajuan sewa kamar.
     */
    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($booking->property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses atas properti kost ini.');
        }

        $room = $booking->room;

        if ($room->status !== RoomStatus::Available) {
            return back()->with('error', "Kamar {$room->room_number} saat ini sudah berstatus terisi atau dalam perbaikan.");
        }

        // 1. Ubah status pengajuan sewa jadi Approved
        $booking->update([
            'status' => BookingStatus::Approved,
        ]);

        // 2. Ubah status kamar otomatis menjadi Occupied
        $room->update([
            'status' => RoomStatus::Occupied,
        ]);

        // 3. Daftarkan calon penyewa ke tabel tenants sebagai penghuni aktif
        $tenant = Tenant::updateOrCreate(
            [
                'property_id' => $booking->property_id,
                'room_id' => $booking->room_id,
                'phone' => $booking->phone,
            ],
            [
                'user_id' => $booking->user_id,
                'name' => $booking->name,
                'start_date' => $booking->start_date,
                'due_day' => Carbon::parse($booking->start_date)->day,
                'is_active' => true,
            ]
        );

        // 4. Terbitkan tagihan sewa awal (Invoice)
        $invoiceCode = 'INV-'.now()->format('Ym').'-'.strtoupper(Str::random(5));
        Invoice::create([
            'property_id' => $booking->property_id,
            'tenant_id' => $tenant->id,
            'invoice_code' => $invoiceCode,
            'amount' => $booking->total_price,
            'additional_fee' => 0,
            'total_amount' => $booking->total_price,
            'due_date' => $booking->start_date,
            'status' => InvoiceStatus::Unpaid,
            'payment_url' => url('/pay/'.$invoiceCode),
        ]);

        return redirect()->route('owner.bookings.index')
            ->with('success', "Pengajuan sewa dari {$booking->name} disetujui! Kamar {$room->room_number} kini berstatus terisi.");
    }

    /**
     * Tolak (Reject) pengajuan sewa kamar.
     */
    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($booking->property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses atas properti kost ini.');
        }

        $reason = $request->input('rejection_reason', 'Kamar tidak tersedia atau jadwal bentrok.');

        $booking->update([
            'status' => BookingStatus::Rejected,
            'rejection_reason' => $reason,
        ]);

        return redirect()->route('owner.bookings.index')
            ->with('info', "Pengajuan sewa dari {$booking->name} telah ditolak.");
    }
}
