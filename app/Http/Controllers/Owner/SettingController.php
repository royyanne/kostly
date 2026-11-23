<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan profil dan rekening pembayaran owner.
     */
    public function index(Request $request): View
    {
        return view('owner.settings.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Simpan pembaruan informasi rekening bank dan foto QRIS pembayaran.
     */
    public function updatePaymentSettings(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'qris_image' => ['nullable', 'image', 'max:2048'],
        ], [
            'qris_image.image' => 'File QRIS harus berupa gambar (JPG, PNG, atau WEBP).',
            'qris_image.max' => 'Ukuran file QRIS maksimal 2MB.',
        ]);

        $updateData = [
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
        ];

        if ($request->hasFile('qris_image')) {
            if ($user->qris_image_path && Storage::disk('public')->exists($user->qris_image_path)) {
                Storage::disk('public')->delete($user->qris_image_path);
            }
            $updateData['qris_image_path'] = $request->file('qris_image')->store('owner-qris', 'public');
        }

        $user->update($updateData);

        return redirect()->route('owner.settings.index')
            ->with('success', 'Pengaturan rekening dan QRIS pembayaran berhasil disimpan!');
    }
}
