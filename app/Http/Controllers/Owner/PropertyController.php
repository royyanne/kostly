<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Tampilkan daftar properti milik pemilik kost.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $properties = $user->properties()
            ->withCount(['rooms', 'tenants'])
            ->with(['rooms' => fn ($q) => $q->select('id', 'property_id', 'status', 'price')])
            ->latest()
            ->get();

        return view('owner.properties.index', [
            'user' => $user,
            'properties' => $properties,
        ]);
    }

    /**
     * Simpan data properti baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama properti kost wajib diisi.',
            'address.required' => 'Alamat lengkap lokasi kost wajib diisi.',
        ]);

        $user->properties()->create($validated);

        return redirect()->route('owner.properties.index')->with('success', 'Properti kost baru berhasil ditambahkan!');
    }

    /**
     * Perbarui data properti yang sudah ada.
     */
    public function update(Request $request, Property $property): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Data scoping
        if ($property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses terhadap properti ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama properti kost wajib diisi.',
            'address.required' => 'Alamat lengkap lokasi kost wajib diisi.',
        ]);

        $property->update($validated);

        return redirect()->route('owner.properties.index')->with('success', 'Informasi properti berhasil diperbarui!');
    }

    /**
     * Hapus properti beserta relasinya.
     */
    public function destroy(Request $request, Property $property): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Data scoping
        if ($property->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses terhadap properti ini.');
        }

        $propertyName = $property->name;
        $property->delete();

        return redirect()->route('owner.properties.index')->with('success', "Properti '{$propertyName}' beserta seluruh datanya telah berhasil dihapus.");
    }
}
