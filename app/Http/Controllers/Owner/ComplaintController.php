<?php

namespace App\Http\Controllers\Owner;

use App\Enums\ComplaintStatus;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ComplaintController extends Controller
{
    /**
     * Tampilkan seluruh tiket laporan komplain fasilitas dari penghuni.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()->get();
        $selectedPropertyId = $request->query('property_id');
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $baseComplaintsQuery = $user->complaints()->with(['tenant', 'room', 'property']);

        if ($selectedPropertyId) {
            $baseComplaintsQuery->where('complaints.property_id', $selectedPropertyId);
        }

        // Hitung total status
        $totalCount = (clone $baseComplaintsQuery)->count();
        $pendingCount = (clone $baseComplaintsQuery)->where('status', ComplaintStatus::Pending)->count();
        $inProgressCount = (clone $baseComplaintsQuery)->where('status', ComplaintStatus::InProgress)->count();
        $resolvedCount = (clone $baseComplaintsQuery)->where('status', ComplaintStatus::Resolved)->count();

        $complaintsQuery = clone $baseComplaintsQuery;

        if ($statusFilter && in_array($statusFilter, array_column(ComplaintStatus::cases(), 'value'), true)) {
            $complaintsQuery->where('status', $statusFilter);
        }

        if ($search) {
            $complaintsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('tenant', fn ($tq) => $tq->where('name', 'like', "%{$search}%"));
            });
        }

        $complaints = $complaintsQuery->latest()->get();

        // Penghuni aktif untuk form catat komplain manual
        $activeTenants = $user->tenants()
            ->with(['room', 'property'])
            ->where('is_active', true)
            ->get();

        return view('owner.complaints.index', [
            'user' => $user,
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'complaints' => $complaints,
            'activeTenants' => $activeTenants,
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'resolvedCount' => $resolvedCount,
        ]);
    }

    /**
     * Catat laporan komplain secara manual oleh pemilik kost.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $userTenantIds = $user->tenants()->pluck('tenants.id')->toArray();

        $validated = $request->validate([
            'tenant_id' => ['required', Rule::in($userTenantIds)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', new Enum(ComplaintStatus::class)],
        ], [
            'tenant_id.required' => 'Pilih penghuni pelapor komplain.',
            'title.required' => 'Judul komplain / kerusakan wajib diisi.',
            'description.required' => 'Jelaskan rincian kendala fasilitas.',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);

        Complaint::create([
            'property_id' => $tenant->property_id,
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('owner.complaints.index')->with('success', 'Laporan komplain berhasil dicatat dan masuk ke sistem.');
    }

    /**
     * Perbarui status penanganan komplain (Menunggu -> Diproses -> Selesai).
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($complaint->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'status' => ['required', new Enum(ComplaintStatus::class)],
        ]);

        $complaint->update(['status' => $validated['status']]);

        return redirect()->route('owner.complaints.index')->with('success', "Status komplain '{$complaint->title}' diubah menjadi '{$complaint->status->label()}'.");
    }

    /**
     * Hapus tiket komplain.
     */
    public function destroy(Request $request, Complaint $complaint): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($complaint->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $title = $complaint->title;
        $complaint->delete();

        return redirect()->route('owner.complaints.index')->with('success', "Tiket komplain '{$title}' telah dihapus.");
    }
}
