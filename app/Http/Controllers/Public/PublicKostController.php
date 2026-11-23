<?php

namespace App\Http\Controllers\Public;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PublicKostController extends Controller
{
    /**
     * Tampilkan detail properti kost beserta kamar-kamar yang tersedia.
     */
    public function show(Property $property): View
    {
        $property->load([
            'user',
            'rooms' => fn ($q) => $q->orderBy('room_number'),
        ]);

        $availableRooms = $property->rooms->where('status', RoomStatus::Available);

        return view('public.kost.show', [
            'property' => $property,
            'availableRooms' => $availableRooms,
        ]);
    }

    /**
     * Proses pengajuan sewa kamar kost oleh calon penyewa / tenant.
     */
    public function storeBooking(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')->where('property_id', $property->id),
            ],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_type' => ['required', 'in:monthly,yearly'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:24'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'room_id.required' => 'Pilih kamar yang ingin Anda sewa.',
            'room_id.exists' => 'Kamar yang dipilih tidak valid untuk properti kost ini.',
            'start_date.required' => 'Tanggal mulai masuk kost wajib ditentukan.',
            'start_date.after_or_equal' => 'Tanggal mulai masuk tidak boleh tanggal di masa lalu.',
            'duration_months.required' => 'Durasi sewa wajib dipilih.',
            'name.required' => 'Nama lengkap pemohon wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
        ]);

        // Pastikan kamar masih berstatus Available
        $room = Room::findOrFail($validated['room_id']);
        if ($room->status !== RoomStatus::Available) {
            return back()->withInput()->withErrors([
                'room_id' => 'Maaf, kamar '.$room->room_number.' saat ini sudah terisi atau sedang tidak tersedia.',
            ]);
        }

        // Tentukan user yang mengajukan (auth user atau buat akun tenant baru)
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('email', $validated['email'])->first();

            if (! $user) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'role' => UserRole::Tenant,
                    'is_active' => true,
                    'password' => Hash::make($validated['password'] ?? 'password'),
                ]);
            }

            Auth::login($user);
            $request->session()->regenerate();
        }

        // Hitung total harga sewa
        $months = (int) $validated['duration_months'];
        $totalPrice = (float) ($room->price * $months);

        $booking = Booking::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'room_id' => $room->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'start_date' => $validated['start_date'],
            'duration_type' => $validated['duration_type'],
            'duration_months' => $months,
            'total_price' => $totalPrice,
            'notes' => $validated['notes'] ?? null,
            'status' => BookingStatus::Pending,
        ]);

        if ($user->isTenant()) {
            return redirect()->route('tenant.dashboard')->with('success', 'Pengajuan sewa kamar '.$room->room_number.' berhasil dikirim! Pemilik kost akan segera meninjau dan mengonfirmasi pengajuan Anda.');
        }

        return redirect()->route('public.kost.show', $property->slug ?? $property->id)->with('success', 'Pengajuan sewa kamar '.$room->room_number.' berhasil dikirim! Kami akan menghubungi Anda melalui WhatsApp.');
    }
}
