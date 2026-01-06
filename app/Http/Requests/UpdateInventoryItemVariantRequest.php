<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\InventoryItem;

class UpdateInventoryItemVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implement proper authorization policy
        // Example: return $this->user()->can('update', $this->route('inventory_variant'));
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
        // Get the variant ID from route model binding
        // Laravel's route model binding will inject the model instance
        $variant = $this->route('inventory_variant');
        $variantId = $variant instanceof \App\Models\InventoryItemVariant ? $variant->id : null;

        return [
            'inventory_item_id' => 'sometimes|exists:inventory_items,id',
            'product_id' => 'nullable|string|max:255',
            'variant_name' => 'sometimes|string|max:255',
            'type' => 'nullable|string|max:255',
            // Frontend sends only the variant SKU part; backend composes the full SKU.
            // Here we only validate format of the variant part; uniqueness is applied on full_sku.
            'sku' => 'sometimes|string|max:255|regex:/^[A-Z0-9-]+$/',
            'full_sku' => 'nullable|string|max:255|unique:inventory_item_variants,sku,' . $variantId,
            'description' => 'nullable|string',
            'in_stock' => 'nullable|integer|min:0',
            'projected_stock' => 'nullable|integer|min:0',
            'utilised_stock' => 'nullable|integer|min:0',
            'incoming_stock' => 'nullable|integer|min:0',
            'supply_price' => 'nullable|numeric|min:0',
            'install_price' => 'nullable|numeric|min:0',
            'total_balance' => 'nullable|integer|min:0',
            'alert_level' => 'nullable|integer|min:0|max:1000',
            'status' => 'nullable|string|in:active,inactive',
            'color' => 'nullable|string|max:255',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'material' => 'nullable|string|max:255',
            // 'zone' is read-only - inherited from parent inventory_item automatically
            // Backend keeps validation simple (1–2 characters); frontend enforces stricter pattern (A–Z or 1–99)
            'shelf_level' => 'nullable|string|min:1|max:2',
            'rack_no' => 'nullable|string|min:1|max:2',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shelf_level.min' => 'Shelf Level must be at least 1 character.',
            'shelf_level.max' => 'Shelf Level must not be more than 2 characters.',
            'rack_no.min' => 'Rack No must be at least 1 character.',
            'rack_no.max' => 'Rack No must not be more than 2 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     * If variant_name or sku are not provided, merge them from the existing model.
     * Normalize shelf_level and rack_no: uppercase letters, remove leading zeros from numbers.
     * Also compose full_sku for uniqueness validation: <parent_item.sku>-<variant_sku_part>.
     */
    protected function prepareForValidation(): void
    {
        $variant = $this->route('inventory_variant');
        
        if ($variant instanceof \App\Models\InventoryItemVariant) {
            // Merge existing values if not provided in request
            $mergeData = [];
            
            if (!$this->has('variant_name')) {
                $mergeData['variant_name'] = $variant->variant_name;
            }
            
            if (!$this->has('sku')) {
                $mergeData['sku'] = $variant->sku;
            }
            
            if (!$this->has('inventory_item_id')) {
                $mergeData['inventory_item_id'] = $variant->inventory_item_id;
            }
            
            $this->merge($mergeData);
        }

        // Normalize shelf_level and rack_no
        if ($this->has('shelf_level') && !empty($this->shelf_level)) {
            $value = $this->shelf_level;
            // Convert to uppercase if it's a letter
            if (preg_match('/^[A-Za-z]$/', $value)) {
                $this->merge(['shelf_level' => strtoupper($value)]);
            }
            // Remove leading zero if it's a number (e.g., "01" -> "1", "12" -> "12")
            // Only accept 1-99 (not 0, not 100+)
            elseif (preg_match('/^(0?)([1-9]|[1-9][0-9])$/', $value, $matches)) {
                $num = (int) $value; // This automatically removes leading zeros
                // Ensure number is between 1-99
                if ($num >= 1 && $num <= 99) {
                    $this->merge(['shelf_level' => (string) $num]);
                }
            }
        }

        if ($this->has('rack_no') && !empty($this->rack_no)) {
            $value = $this->rack_no;
            // Convert to uppercase if it's a letter
            if (preg_match('/^[A-Za-z]$/', $value)) {
                $this->merge(['rack_no' => strtoupper($value)]);
            }
            // Remove leading zero if it's a number (e.g., "01" -> "1", "12" -> "12")
            // Only accept 1-99 (not 0, not 100+)
            elseif (preg_match('/^(0?)([1-9]|[1-9][0-9])$/', $value, $matches)) {
                $num = (int) $value; // This automatically removes leading zeros
                // Ensure number is between 1-99
                if ($num >= 1 && $num <= 99) {
                    $this->merge(['rack_no' => (string) $num]);
                }
            }
        }

        // Compose full_sku for uniqueness validation if we have parent item and variant sku part
        if ($this->has('inventory_item_id') && !empty($this->inventory_item_id) && $this->has('sku') && $this->sku !== null && $this->sku !== '') {
            $parentItem = InventoryItem::find($this->inventory_item_id);

            if ($parentItem && !empty($parentItem->sku)) {
                $variantSkuPart = $this->sku;

                // Normalize variant SKU part (same rules as model)
                $variantSkuPart = strtoupper($variantSkuPart);
                $variantSkuPart = preg_replace('/\s+/', '-', $variantSkuPart);
                $variantSkuPart = preg_replace('/[^A-Z0-9-]/', '', $variantSkuPart);
                $variantSkuPart = preg_replace('/-+/', '-', $variantSkuPart);
                $variantSkuPart = trim($variantSkuPart, '-');

                if ($variantSkuPart !== '') {
                    $fullSku = $parentItem->sku . '-' . $variantSkuPart;
                    $this->merge(['full_sku' => $fullSku]);
                }
            }
        }
    }
}

