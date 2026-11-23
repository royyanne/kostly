<?php

namespace App\Http\Controllers\Owner;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Tampilkan data seluruh penghuni kost.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()->get();
        $selectedPropertyId = $request->query('property_id');
        $statusFilter = $request->query('status', 'active'); // default: active
        $search = $request->query('search');

        $tenantsQuery = $user->tenants()->with(['property', 'room']);

        if ($selectedPropertyId) {
            $tenantsQuery->where('tenants.property_id', $selectedPropertyId);
        }

        if ($statusFilter === 'active') {
            $tenantsQuery->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $tenantsQuery->where('is_active', false);
        }

        if ($search) {
            $tenantsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('room', fn ($rq) => $rq->where('room_number', 'like', "%{$search}%"));
            });
        }

        $tenants = $tenantsQuery->latest()->get();

        // Kamar yang tersedia untuk penghuni baru
        $availableRooms = $user->rooms()
            ->where('status', RoomStatus::Available)
            ->with('property')
            ->orderBy('room_number')
            ->get();

        // Hitung counter
        $activeCount = (clone $user->tenants())->where('is_active', true)->count();
        $inactiveCount = (clone $user->tenants())->where('is_active', false)->count();

        return view('owner.tenants.index', [
            'user' => $user,
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'tenants' => $tenants,
            'availableRooms' => $availableRooms,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
        ]);
    }

    /**
     * Simpan data penghuni kost baru dan ubah status kamar menjadi terisi.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $userPropertyIds = $user->properties()->pluck('id')->toArray();
        $userRoomIds = $user->rooms()->pluck('rooms.id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($userPropertyIds)],
            'room_id' => ['required', Rule::in($userRoomIds)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'due_day' => ['required', 'integer', 'between:1,28'],
        ], [
            'property_id.required' => 'Pilih lokasi properti kost.',
            'room_id.required' => 'Pilih kamar yang akan dihuni.',
            'name.required' => 'Nama lengkap penghuni wajib diisi.',
            'phone.required' => 'Nomor WhatsApp penghuni wajib diisi.',
            'start_date.required' => 'Tanggal mulai sewa wajib diisi.',
            'due_day.required' => 'Tanggal jatuh tempo bulanan wajib diisi (1-28).',
        ]);

        $tenant = Tenant::create(array_merge($validated, ['is_active' => true]));

        // Otomatis ubah status kamar menjadi Occupied
        Room::where('id', $validated['room_id'])->update([
            'status' => RoomStatus::Occupied,
        ]);

        return redirect()->route('owner.tenants.index')->with('success', "Penghuni '{$tenant->name}' berhasil didaftarkan ke Kamar {$tenant->room->room_number}!");
    }

    /**
     * Perbarui data penghuni.
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Data scoping
        if ($tenant->property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah data penghuni ini.');
        }

        $userRoomIds = $user->rooms()->pluck('rooms.id')->toArray();

        $validated = $request->validate([
            'room_id' => ['required', Rule::in($userRoomIds)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'due_day' => ['required', 'integer', 'between:1,28'],
            'is_active' => ['required', 'boolean'],
        ]);

        $oldRoomId = $tenant->room_id;
        $newRoomId = (int) $validated['room_id'];

        $tenant->update($validated);

        // Jika kamar berpindah
        if ($oldRoomId !== $newRoomId) {
            if ($oldRoomId) {
                Room::where('id', $oldRoomId)->update(['status' => RoomStatus::Available]);
            }
            Room::where('id', $newRoomId)->update(['status' => RoomStatus::Occupied]);
        }

        // Jika status aktif dimatikan
        if (! $validated['is_active'] && $newRoomId) {
            Room::where('id', $newRoomId)->update(['status' => RoomStatus::Available]);
        }

        return redirect()->route('owner.tenants.index')->with('success', "Data penghuni '{$tenant->name}' berhasil diperbarui.");
    }

    /**
     * Proses checkout penghuni dan kosongkan kembali kamar.
     */
    public function checkout(Request $request, Tenant $tenant): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($tenant->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $roomId = $tenant->room_id;

        $tenant->update([
            'is_active' => false,
            'check_out_date' => now()->toDateString(),
        ]);

        if ($roomId) {
            Room::where('id', $roomId)->update(['status' => RoomStatus::Available]);
        }

        return redirect()->route('owner.tenants.index')->with('success', "Penghuni '{$tenant->name}' telah berhasil di-checkout. Kamar kini kembali berstatus Tersedia.");
    }

    /**
     * Hapus data penghuni.
     */
    public function destroy(Request $request, Tenant $tenant): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($tenant->property->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $roomId = $tenant->room_id;
        $name = $tenant->name;

        if ($tenant->is_active && $roomId) {
            Room::where('id', $roomId)->update(['status' => RoomStatus::Available]);
        }

        $tenant->delete();

        return redirect()->route('owner.tenants.index')->with('success', "Data penghuni '{$name}' telah dihapus.");
    }
}
