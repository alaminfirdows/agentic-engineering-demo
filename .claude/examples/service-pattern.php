<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

/**
 * Example: Email Service
 *
 * Encapsulate external concerns (email provider, queuing, etc.)
 * Makes it easy to swap providers or change behavior globally.
 */
class EmailService
{
    /**
     * Send email immediately
     */
    public function send(string $to, string $subject, Mailable $mailable): void
    {
        Mail::to($to)->send($mailable);
    }

    /**
     * Queue email for async delivery
     */
    public function queue(string $to, string $subject, Mailable $mailable): void
    {
        Mail::to($to)->queue($mailable);
    }

    /**
     * Send to multiple recipients
     *
     * @param list<string> $recipients
     */
    public function sendBatch(array $recipients, string $subject, Mailable $mailable): void
    {
        foreach ($recipients as $email) {
            Mail::to($email)->queue($mailable);
        }
    }
}

/**
 * Example: Invoice Number Service
 *
 * Generate invoice numbers with business logic.
 * Centralized so all invoices follow same format.
 */
class InvoiceNumberService
{
    /**
     * Generate next invoice number
     * Example: INV-2026-000001
     */
    public function next(): string
    {
        $count = \App\Models\Invoice::whereYear('created_at', now()->year)->count();
        $sequence = str_pad((string) ($count + 1), 6, '0', STR_PAD_LEFT);

        return "INV-" . now()->year . "-{$sequence}";
    }

    /**
     * Format invoice number for display
     */
    public function format(string $number): string
    {
        // Could add formatting, removing dashes, etc.
        return $number;
    }
}

/**
 * Example: Payment Service
 *
 * Abstract payment provider (Stripe, Square, etc.)
 * Allows switching providers without changing action code.
 */
class PaymentService
{
    public function __construct(private string $apiKey = '') {}

    /**
     * Charge a customer
     *
     * @return array{id: string, status: string}
     */
    public function charge(string $customerId, float $amount, string $currency = 'usd'): array
    {
        // Call external API (Stripe, etc.)
        // For now, simulate
        return [
            'id' => 'txn_' . uniqid(),
            'status' => 'succeeded',
        ];
    }

    /**
     * Refund a transaction
     */
    public function refund(string $transactionId, ?float $amount = null): array
    {
        return [
            'id' => 'ref_' . uniqid(),
            'status' => 'succeeded',
        ];
    }

    /**
     * Get transaction status
     */
    public function getStatus(string $transactionId): string
    {
        return 'succeeded';
    }
}

/**
 * Usage in Action:
 *
 * class ChargeLicenseAction
 * {
 *     public function __construct(
 *         private PaymentService $payment,
 *         private EmailService $email,
 *     ) {}
 *
 *     public function execute(User $user, float $amount): void
 *     {
 *         $result = $this->payment->charge($user->stripe_id, $amount);
 *
 *         if ($result['status'] === 'succeeded') {
 *             $this->email->queue(
 *                 to: $user->email,
 *                 subject: 'Payment Confirmed',
 *                 mailable: new PaymentConfirmed($user, $amount),
 *             );
 *         }
 *     }
 * }
 */
