<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\ComplaintStatus;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantComplaintController extends Controller
{
    /**
     * Tampilkan riwayat komplain fasilitas dan form komplain baru.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $tenant = Tenant::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('phone', $user->phone);
            })
            ->where('is_active', true)
            ->with(['property', 'room', 'complaints' => fn ($q) => $q->latest()])
            ->first();

        $complaints = $tenant?->complaints ?? collect();

        return view('tenant.complaints', [
            'user' => $user,
            'tenant' => $tenant,
            'complaints' => $complaints,
        ]);
    }

    /**
     * Simpan laporan komplain fasilitas dari penyewa.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $tenant = Tenant::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('phone', $user->phone);
            })
            ->where('is_active', true)
            ->first();

        if (! $tenant) {
            return back()->with('error', 'Anda belum terdaftar sebagai penghuni aktif kamar kost.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ], [
            'title.required' => 'Judul kendala atau kerusakan wajib diisi.',
            'description.required' => 'Jelaskan rincian kerusakan fasilitas.',
        ]);

        Complaint::create([
            'property_id' => $tenant->property_id,
            'room_id' => $tenant->room_id,
            'tenant_id' => $tenant->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => ComplaintStatus::Pending,
        ]);

        return redirect()->route('tenant.complaints.index')
            ->with('success', 'Laporan komplain Anda berhasil dikirim ke pemilik kost!');
    }
}
