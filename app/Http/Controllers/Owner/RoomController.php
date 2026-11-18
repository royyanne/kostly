<?php

namespace App\Http\Controllers\Owner;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class RoomController extends Controller
{
    /**
     * Tampilkan daftar kamar dalam bentuk visual grid dan filter interaktif.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()->get();
        $selectedPropertyId = $request->query('property_id');
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        // Query kamar terisolasi milik user
        $baseRoomsQuery = $user->rooms()->with(['property', 'currentTenant']);

        if ($selectedPropertyId) {
            $baseRoomsQuery->where('rooms.property_id', $selectedPropertyId);
        }

        // Hitung total status untuk badge filter
        $totalCount = (clone $baseRoomsQuery)->count();
        $availableCount = (clone $baseRoomsQuery)->where('status', RoomStatus::Available)->count();
        $occupiedCount = (clone $baseRoomsQuery)->where('status', RoomStatus::Occupied)->count();
        $maintenanceCount = (clone $baseRoomsQuery)->where('status', RoomStatus::Maintenance)->count();

        // Terapkan filter status & pencarian
        $roomsQuery = clone $baseRoomsQuery;

        if ($statusFilter && in_array($statusFilter, array_column(RoomStatus::cases(), 'value'), true)) {
            $roomsQuery->where('status', $statusFilter);
        }

        if ($search) {
            $roomsQuery->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $rooms = $roomsQuery->orderBy('room_number')->get();

        return view('owner.rooms.index', [
            'user' => $user,
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'rooms' => $rooms,
            'totalCount' => $totalCount,
            'availableCount' => $availableCount,
            'occupiedCount' => $occupiedCount,
            'maintenanceCount' => $maintenanceCount,
        ]);
    }

    /**
     * Simpan data kamar baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Pastikan properti yang dipilih adalah milik user
        $userPropertyIds = $user->properties()->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($userPropertyIds)],
            'room_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'room_number')->where(fn ($q) => $q->where('property_id', $request->input('property_id'))),
            ],
            'type' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', new Enum(RoomStatus::class)],
        ], [
            'property_id.required' => 'Pilih lokasi properti kost.',
            'property_id.in' => 'Properti tidak valid.',
            'room_number.required' => 'Nomor / nama kamar wajib diisi.',
            'room_number.unique' => 'Nomor kamar ini sudah digunakan pada properti yang sama.',
            'type.required' => 'Tipe kamar wajib diisi (misal: Deluxe AC, Standard Fan).',
            'price.required' => 'Harga sewa per bulan wajib diisi.',
            'price.numeric' => 'Harga sewa harus berupa angka valid.',
            'status.required' => 'Status awal kamar wajib dipilih.',
        ]);

        Room::create($validated);

        return redirect()->back()->with('success', "Kamar '{$validated['room_number']}' berhasil ditambahkan!");
    }

    /**
     * Perbarui data kamar.
     */
    public function update(Request $request, Room $room): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Data scoping
        if ($room->property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah kamar ini.');
        }

        $validated = $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'room_number')
                    ->where(fn ($q) => $q->where('property_id', $room->property_id))
                    ->ignore($room->id),
            ],
            'type' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', new Enum(RoomStatus::class)],
        ], [
            'room_number.required' => 'Nomor kamar wajib diisi.',
            'room_number.unique' => 'Nomor kamar sudah terdaftar di properti ini.',
            'type.required' => 'Tipe kamar wajib diisi.',
            'price.required' => 'Harga sewa bulanan wajib diisi.',
        ]);

        $room->update($validated);

        return redirect()->back()->with('success', "Data kamar '{$room->room_number}' berhasil diperbarui!");
    }

    /**
     * Pembaruan cepat status kamar dari visual grid.
     */
    public function updateStatus(Request $request, Room $room): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Data scoping
        if ($room->property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses terhadap kamar ini.');
        }

        $validated = $request->validate([
            'status' => ['required', new Enum(RoomStatus::class)],
        ]);

        $room->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', "Status kamar '{$room->room_number}' diubah menjadi '{$room->status->label()}'.");
    }

    /**
     * Hapus data kamar.
     */
    public function destroy(Request $request, Room $room): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Data scoping
        if ($room->property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses terhadap kamar ini.');
        }

        // Cek apakah ada penghuni aktif
        if ($room->currentTenant()->exists()) {
            return redirect()->back()->with('error', "Kamar '{$room->room_number}' tidak dapat dihapus karena masih ditempati oleh penghuni aktif.");
        }

        $roomNumber = $room->room_number;
        $room->delete();

        return redirect()->back()->with('success', "Kamar '{$roomNumber}' berhasil dihapus.");
    }
}
