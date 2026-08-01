<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\InvoiceData;
use App\Models\Invoice;
use App\Models\User;
use App\Services\EmailService;
use App\Services\InvoiceNumberService;

/**
 * Example: Create Invoice Action
 * Single responsibility: orchestrate invoice creation
 */
class CreateInvoiceAction
{
    public function __construct(
        private InvoiceNumberService $invoiceNumbers,
        private EmailService $email,
    ) {}

    /**
     * @param array{
     *     user_id: int,
     *     amount: float,
     *     due_date: string,
     *     items: array
     * } $data
     */
    public function execute(array $data): Invoice
    {
        $user = User::findOrFail($data['user_id']);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => $this->invoiceNumbers->next(),
            'amount' => $data['amount'],
            'due_date' => $data['due_date'],
        ]);

        // Attach line items
        $invoice->items()->createMany($data['items']);

        // Send confirmation email asynchronously
        $this->email->queue(
            to: $user->email,
            subject: "Invoice {$invoice->invoice_number}",
            body: "Your invoice is ready.",
        );

        return $invoice;
    }
}

/**
 * Usage in Controller:
 *
 * class InvoiceController
 * {
 *     public function store(
 *         CreateInvoiceRequest $request,
 *         CreateInvoiceAction $action,
 *     ) {
 *         $invoice = $action->execute($request->validated());
 *         return redirect()->route('invoices.show', $invoice);
 *     }
 * }
 */
