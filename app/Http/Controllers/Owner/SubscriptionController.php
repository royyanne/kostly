<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Tampilkan halaman perbandingan paket, status paket saat ini, dan riwayat langganan.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $subscriptions = $user->subscriptions()->latest()->get();
        $pendingSubscription = $subscriptions->firstWhere('status', 'pending');
        $activeSubscription = $subscriptions->firstWhere('status', 'active');

        // Harga paket Pro
        $pricing = [
            'monthly' => [
                'amount' => 99000,
                'label' => 'Paket Pro Bulanan',
                'period' => 'bulan',
            ],
            'yearly' => [
                'amount' => 999000,
                'label' => 'Paket Pro Tahunan (Hemat 2 Bulan)',
                'period' => 'tahun',
            ],
        ];

        return view('owner.subscription.index', [
            'user' => $user,
            'subscriptions' => $subscriptions,
            'pendingSubscription' => $pendingSubscription,
            'activeSubscription' => $activeSubscription,
            'pricing' => $pricing,
        ]);
    }

    /**
     * Ajukan upgrade paket langganan ke Pro dengan mengunggah bukti pembayaran.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Cek jika sudah ada pengajuan yang berstatus pending
        $hasPending = $user->subscriptions()->where('status', 'pending')->exists();
        if ($hasPending) {
            return back()->with('error', 'Anda masih memiliki pengajuan langganan yang sedang menunggu verifikasi admin.');
        }

        $validated = $request->validate([
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'payment_proof' => ['required', 'image', 'max:2048'],
        ], [
            'billing_cycle.required' => 'Pilih siklus penagihan langganan (Bulanan atau Tahunan).',
            'billing_cycle.in' => 'Siklus penagihan tidak valid.',
            'payment_proof.required' => 'Foto bukti pembayaran wajib diunggah.',
            'payment_proof.image' => 'File bukti pembayaran harus berupa gambar (JPG, PNG, atau WEBP).',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 2MB.',
        ]);

        $amount = $validated['billing_cycle'] === 'yearly' ? 999000 : 99000;
        $path = $request->file('payment_proof')->store('subscription-proofs', 'public');

        Subscription::create([
            'user_id' => $user->id,
            'plan' => 'pro',
            'billing_cycle' => $validated['billing_cycle'],
            'amount' => $amount,
            'status' => 'pending',
            'payment_proof' => $path,
        ]);

        return redirect()->route('owner.subscription.index')
            ->with('success', 'Pengajuan upgrade ke Paket Pro berhasil dikirim! Mohon tunggu verifikasi oleh tim admin kami.');
    }
}
