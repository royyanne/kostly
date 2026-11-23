<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invoice $invoice,
        public string $recipientType = 'tenant' // 'tenant' | 'owner'
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $amountFormatted = 'Rp '.number_format((float) $this->invoice->total_amount, 0, ',', '.');
        $dueDateFormatted = $this->invoice->due_date->translatedFormat('d F Y');

        if ($this->recipientType === 'owner') {
            $url = route('owner.invoices.index');
            $tenantName = $this->invoice->tenant?->name ?? 'Penghuni';
            $roomName = $this->invoice->tenant?->room?->room_number ? 'Kamar '.$this->invoice->tenant->room->room_number : '-';

            return (new MailMessage)
                ->subject("Pemberitahuan Tagihan Jatuh Tempo #{$this->invoice->invoice_code} - {$tenantName}")
                ->greeting("Halo Juragan {$notifiable->name},")
                ->line("Tagihan sewa untuk penghuni {$tenantName} ({$roomName}) telah melewati batas tanggal jatuh tempo ({$dueDateFormatted}).")
                ->line("Total Tagihan Menunggak: {$amountFormatted}")
                ->action('Kelola Tagihan di Dashboard', $url)
                ->line('Anda dapat mengirimkan pesan pengingat WhatsApp langsung melalui panel Kostly.');
        }

        $url = route('tenant.invoices.index');

        return (new MailMessage)
            ->subject("Tagihan Sewa Kost Lewat Jatuh Tempo #{$this->invoice->invoice_code}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Tagihan sewa kamar Anda sebesar {$amountFormatted} telah melewati batas jatuh tempo ({$dueDateFormatted}).")
            ->line('Status tagihan kini telah menjadi Jatuh Tempo (Overdue).')
            ->action('Bayar Tagihan Sekarang', $url)
            ->line('Mohon segera menyelesaikan pembayaran atau mengunggah bukti transfer agar terhindar dari denda atau kendala sewa.');
    }

    /**
     * Get the array representation of the notification for database channel.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $amountFormatted = 'Rp '.number_format((float) $this->invoice->total_amount, 0, ',', '.');
        $dueDateFormatted = $this->invoice->due_date->translatedFormat('d F Y');

        if ($this->recipientType === 'owner') {
            $tenantName = $this->invoice->tenant?->name ?? 'Penghuni';

            return [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_code,
                'title' => "Tagihan Jatuh Tempo - {$tenantName}",
                'message' => "Tagihan #{$this->invoice->invoice_code} ({$tenantName}) sebesar {$amountFormatted} telah lewat jatuh tempo ({$dueDateFormatted}).",
                'action_url' => route('owner.invoices.index'),
                'type' => 'overdue',
            ];
        }

        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_code,
            'title' => 'Tagihan Lewat Jatuh Tempo!',
            'message' => "Tagihan #{$this->invoice->invoice_code} sebesar {$amountFormatted} telah melewati batas tanggal jatuh tempo ({$dueDateFormatted}).",
            'action_url' => route('tenant.invoices.index'),
            'type' => 'overdue',
        ];
    }
}
