<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceDueReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invoice $invoice,
        public int $daysRemaining
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
        $url = route('tenant.invoices.index');

        return (new MailMessage)
            ->subject("Pengingat Tagihan Sewa Kost #{$this->invoice->invoice_code} (H-{$this->daysRemaining})")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Tagihan sewa kamar Anda untuk periode ini akan jatuh tempo dalam {$this->daysRemaining} hari lagi pada tanggal {$dueDateFormatted}.")
            ->line("Total Tagihan: {$amountFormatted}")
            ->action('Lihat & Bayar Tagihan', $url)
            ->line('Mohon lakukan pembayaran tepat waktu untuk kenyamanan sewa kamar Anda. Terima kasih!');
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

        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_code,
            'title' => "Pengingat Tagihan H-{$this->daysRemaining}",
            'message' => "Tagihan #{$this->invoice->invoice_code} sebesar {$amountFormatted} jatuh tempo pada {$dueDateFormatted}.",
            'action_url' => route('tenant.invoices.index'),
            'type' => 'reminder',
        ];
    }
}
