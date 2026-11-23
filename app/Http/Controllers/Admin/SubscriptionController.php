<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionPlan;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Tampilkan daftar pengajuan dan riwayat langganan owner.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');

        $query = Subscription::with('user')->latest();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $subscriptions = $query->paginate(15)->withQueryString();

        $counts = [
            'total' => Subscription::count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'active' => Subscription::where('status', 'active')->count(),
            'rejected' => Subscription::where('status', 'rejected')->count(),
        ];

        return view('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'counts' => $counts,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Setujui pengajuan langganan Pro dan aktifkan akun owner.
     */
    public function approve(Subscription $subscription): RedirectResponse
    {
        $startsAt = now();
        $endsAt = $subscription->billing_cycle === 'yearly'
            ? $startsAt->copy()->addYear()
            : $startsAt->copy()->addMonth();

        $subscription->update([
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'rejected_reason' => null,
        ]);

        // Perbarui data user owner
        $user = $subscription->user;
        $user->update([
            'subscription_plan' => SubscriptionPlan::Pro,
            'plan_expires_at' => $endsAt,
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', "Langganan Pro untuk owner {$user->name} berhasil disetujui & diaktifkan hingga {$endsAt->translatedFormat('d F Y')}!");
    }

    /**
     * Tolak pengajuan langganan Pro dengan alasan penolakan.
     */
    public function reject(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'rejected_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejected_reason.required' => 'Alasan penolakan pembayaran wajib diisi.',
        ]);

        $subscription->update([
            'status' => 'rejected',
            'rejected_reason' => $validated['rejected_reason'],
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('info', "Pengajuan langganan #{$subscription->id} atas nama {$subscription->user->name} telah ditolak.");
    }
}
