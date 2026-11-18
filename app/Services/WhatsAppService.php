<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format nomor HP Indonesia (e.g. 08123xxx -> 628123xxx).
     */
    public function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '62'.substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        }

        return '62'.$cleaned;
    }

    /**
     * Kirim pesan pengingat tagihan kepada penghuni kost.
     */
    public function sendInvoiceReminder(Invoice $invoice, string $type = 'upcoming'): bool
    {
        $tenant = $invoice->tenant;
        $property = $invoice->property;

        if (! $tenant || empty($tenant->phone)) {
            return false;
        }

        $formattedPhone = $this->formatPhoneNumber($tenant->phone);
        $message = $this->buildReminderMessage($invoice, $type);

        return $this->sendMessage($formattedPhone, $message);
    }

    /**
     * Susun template pesan pengingat yang ramah, sopan, dan jelas.
     */
    public function buildReminderMessage(Invoice $invoice, string $type = 'upcoming'): string
    {
        $tenantName = $invoice->tenant->name;
        $propertyName = $invoice->property->name;
        $roomNumber = $invoice->tenant->room?->room_number ?? '-';
        $amount = 'Rp '.number_format((float) $invoice->total_amount, 0, ',', '.');
        $dueDate = $invoice->due_date->translatedFormat('d F Y');
        $payUrl = url('/pay/'.$invoice->invoice_code);
        $portalUrl = url('/portal/'.$invoice->tenant->access_token);

        if ($type === 'due_today') {
            return "Halo {$tenantName}, tagihan sewa kost di *{$propertyName} (Kamar {$roomNumber})* jatuh tempo *HARI INI* ({$dueDate}).\n\n"
                ."Jumlah Tagihan: *{$amount}*\n"
                ."Kode Tagihan: #{$invoice->invoice_code}\n\n"
                ."Silakan selesaikan pembayaran dan unggah bukti bayar melalui link berikut:\n{$payUrl}\n\n"
                ."Portal Penghuni Anda: {$portalUrl}\n\n"
                .'Terima kasih atas kerja samanya! 🙏';
        }

        if ($type === 'overdue') {
            return "Pemberitahuan: Halo {$tenantName}, tagihan sewa kost di *{$propertyName} (Kamar {$roomNumber})* sebesar *{$amount}* telah melewati batas jatuh tempo ({$dueDate}).\n\n"
                ."Mohon segera melakukan pembayaran melalui link berikut:\n{$payUrl}\n\n"
                ."Jika sudah membayar, abaikan pesan ini atau konfirmasi melalui portal: {$portalUrl}\n\n"
                .'Terima kasih. 🙏';
        }

        return "Halo {$tenantName}, pengingat ramah untuk tagihan sewa kost di *{$propertyName} (Kamar {$roomNumber})* akan jatuh tempo pada *{$dueDate}*.\n\n"
            ."Jumlah Tagihan: *{$amount}*\n"
            ."Kode Tagihan: #{$invoice->invoice_code}\n\n"
            ."Detail & Pembayaran: {$payUrl}\n"
            ."Portal Penghuni: {$portalUrl}\n\n"
            .'Terima kasih! 🙏';
    }

    /**
     * Kirim pesan melalui provider WhatsApp API (misal: Fonnte, Waba, Twilio).
     */
    public function sendMessage(string $phone, string $message): bool
    {
        // Placeholder integrasi WhatsApp API (dapat dikonfigurasi via config/services.php)
        Log::info("WhatsApp Notification sent to {$phone}: {$message}");

        return true;
    }
}
