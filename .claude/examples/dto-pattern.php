<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Example: Invoice Data Transfer Object
 *
 * Use for:
 * - Passing computed data between layers
 * - Type-safe parameter passing
 * - Immutable data carriers
 *
 * Never use for database persistence—use Models for that.
 */
class InvoiceData implements Arrayable
{
    public readonly int $userId;

    public readonly string $invoiceNumber;

    public readonly float $subtotal;

    public readonly float $taxAmount;

    public readonly float $total;

    public readonly Carbon $dueDate;

    /** @var array<InvoiceItemData> */
    public readonly array $items;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->userId = $data['user_id'];
        $this->invoiceNumber = $data['invoice_number'];
        $this->subtotal = $data['subtotal'];
        $this->taxAmount = $data['tax_amount'];
        $this->total = $data['total'];
        $this->dueDate = $data['due_date'];
        $this->items = $data['items'] ?? [];
    }

    public static function fromModel($invoice): self
    {
        return new self([
            'user_id' => $invoice->user_id,
            'invoice_number' => $invoice->invoice_number,
            'subtotal' => $invoice->subtotal,
            'tax_amount' => $invoice->tax_amount,
            'total' => $invoice->total,
            'due_date' => $invoice->due_date,
            'items' => $invoice->items->map(fn ($item) => InvoiceItemData::fromModel($item))->toArray(),
        ]);
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'invoice_number' => $this->invoiceNumber,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'total' => $this->total,
            'due_date' => $this->dueDate->toIso8601String(),
            'items' => $this->items,
        ];
    }
}

class InvoiceItemData implements Arrayable
{
    public readonly string $description;

    public readonly int $quantity;

    public readonly float $unitPrice;

    public readonly float $total;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->description = $data['description'];
        $this->quantity = $data['quantity'];
        $this->unitPrice = $data['unit_price'];
        $this->total = $this->quantity * $this->unitPrice;
    }

    public static function fromModel($item): self
    {
        return new self([
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ]);
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'total' => $this->total,
        ];
    }
}
