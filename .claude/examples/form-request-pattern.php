<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Example: Create Invoice Request
 *
 * Responsibilities:
 * - Validate input (rules)
 * - Check authorization (authorize)
 * - Transform data (prepareForValidation)
 *
 * Controller receives validated data via $request->validated()
 */
class CreateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is allowed to create invoices
        return $this->user()->can('create-invoice');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'due_date' => ['required', 'date', 'after:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'due_date.after' => 'Due date must be in the future.',
            'items.min' => 'Invoice must have at least one item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Called before validation rules are applied.
     * Use for data transformation, cleanup, etc.
     */
    protected function prepareForValidation(): void
    {
        // Example: normalize input
        if ($this->has('items')) {
            $this->merge([
                'items' => array_map(fn ($item) => [
                    'description' => trim($item['description']),
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $item['unit_price'],
                ], $this->input('items', [])),
            ]);
        }
    }

    /**
     * Get the validated input as a new instance.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        // Add user context
        return [
            ...$data,
            'user_id' => $this->user()->id,
            'created_by' => $this->user()->id,
        ];
    }
}

/**
 * Example: Update Invoice Request
 *
 * Can differ from Create request (different rules)
 */
class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('invoice'));
    }

    public function rules(): array
    {
        return [
            'due_date' => ['nullable', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            // Status only: draft, sent, paid, cancelled
            'status' => ['nullable', 'in:draft,sent,paid,cancelled'],
        ];
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
 *
 *     public function update(
 *         UpdateInvoiceRequest $request,
 *         Invoice $invoice,
 *         UpdateInvoiceAction $action,
 *     ) {
 *         $invoice = $action->execute($invoice, $request->validated());
 *         return redirect()->route('invoices.show', $invoice);
 *     }
 * }
 */
