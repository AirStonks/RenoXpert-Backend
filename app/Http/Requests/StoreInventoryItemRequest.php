<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implement proper authorization policy
        // Example: return $this->user()->can('create', InventoryItem::class);
        // For now, allow authenticated users (middleware handles authentication)
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|regex:/^[A-Z0-9-]+$/|unique:inventory_items,sku',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'zone' => 'nullable|string|size:1|regex:/^[A-Z]$/',
            'product_id' => 'nullable|string|max:255',
        ];
    }
}

