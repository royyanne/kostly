<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionPlan;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pemilik kost (owner) di platform.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', UserRole::Owner)
            ->withCount(['properties', 'rooms', 'tenants']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        $owners = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('owners'));
    }

    /**
     * Ubah paket langganan pemilik kost (Free <-> Pro).
     */
    public function updatePlan(Request $request, User $user): RedirectResponse
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak dapat mengubah paket langganan Super Admin.');
        }

        if ($request->has('subscription_plan')) {
            $validated = $request->validate([
                'subscription_plan' => ['required', Rule::enum(SubscriptionPlan::class)],
            ]);
            $newPlan = SubscriptionPlan::from($validated['subscription_plan']);
        } else {
            $newPlan = $user->subscription_plan === SubscriptionPlan::Pro
                ? SubscriptionPlan::Free
                : SubscriptionPlan::Pro;
        }

        $user->update([
            'subscription_plan' => $newPlan,
        ]);

        return back()->with('success', "Paket langganan akun {$user->name} berhasil diperbarui menjadi {$newPlan->label()}.");
    }

    /**
     * Aktifkan atau tangguhkan akun pemilik kost.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun Super Admin.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan kembali' : 'ditangguhkan';

        return back()->with('success', "Status akun {$user->name} berhasil {$statusText}.");
    }
}
