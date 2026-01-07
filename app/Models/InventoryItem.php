<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'inventory_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'description',
        'type',
        'status',
        'zone',
        'deleted_at',
    ];

    /**
     * Boot the model and register event listeners.
     * Automatically sync zone to all variants when parent item's zone changes.
     * Auto-generate SKU from name if SKU is not provided.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate SKU from name if not provided
        static::saving(function ($item) {
            // Only auto-generate if SKU is empty/null and name exists
            if (empty($item->sku) && !empty($item->name)) {
                $item->sku = $item->generateSkuFromName($item->name);
            } elseif (!empty($item->sku)) {
                // Ensure SKU format is correct (uppercase, no spaces, only alphanumeric and hyphens)
                $item->sku = strtoupper($item->sku);
                $item->sku = preg_replace('/\s+/', '-', $item->sku); // Replace spaces with hyphens
                $item->sku = preg_replace('/[^A-Z0-9-]/', '', $item->sku); // Remove invalid characters
                // Remove multiple consecutive hyphens
                $item->sku = preg_replace('/-+/', '-', $item->sku);
                // Trim hyphens from start and end
                $item->sku = trim($item->sku, '-');
                // If SKU becomes empty after normalization, generate fallback SKU
                if (empty($item->sku)) {
                    $item->sku = $item->generateFallbackSku();
                }
            }
        });

        // When an inventory item's zone is updated, sync it to all variants
        static::updated(function ($item) {
            if ($item->isDirty('zone')) {
                $item->variants()->update(['zone' => $item->zone]);
            }
        });
    }

    /**
     * Generate SKU from item name.
     * Converts name to uppercase, replaces spaces with hyphens, removes invalid characters.
     * If the result is empty (e.g., name contains only special characters), generates a fallback SKU.
     *
     * @param string $name
     * @return string
     */
    protected function generateSkuFromName(string $name): string
    {
        // Convert to uppercase
        $sku = strtoupper($name);
        
        // Replace spaces with hyphens
        $sku = preg_replace('/\s+/', '-', $sku);
        
        // Remove any characters that are not uppercase letters, numbers, or hyphens
        $sku = preg_replace('/[^A-Z0-9-]/', '', $sku);
        
        // Remove multiple consecutive hyphens
        $sku = preg_replace('/-+/', '-', $sku);
        
        // Trim hyphens from start and end
        $sku = trim($sku, '-');
        
        // If SKU is empty after processing (e.g., name contains only special characters),
        // generate a fallback SKU to prevent unique constraint violations
        if (empty($sku)) {
            $sku = $this->generateFallbackSku();
        }
        
        return $sku;
    }

    /**
     * Generate a fallback SKU when the name-based generation results in an empty string.
     * Uses a prefix and unique identifier to ensure uniqueness.
     *
     * @return string
     */
    protected function generateFallbackSku(): string
    {
        $prefix = 'ITEM-';
        $maxAttempts = 10;
        $attempt = 0;
        
        do {
            // Use ID if available, otherwise use uniqid with more entropy for better uniqueness
            if ($this->id) {
                $uniqueId = $this->id;
            } else {
                // Generate unique identifier using uniqid with more entropy to prevent race conditions
                $uniqueId = uniqid('', true); // More entropy: includes microseconds and random prefix
                // Remove dots and convert to alphanumeric for cleaner SKU
                $uniqueId = str_replace('.', '', $uniqueId);
            }
            
            $sku = $prefix . $uniqueId;
            
            // Check if SKU already exists (excluding current record if updating and soft-deleted records)
            $query = static::where('sku', $sku)->whereNull('deleted_at');
            if ($this->id) {
                $query->where('id', '!=', $this->id);
            }
            $exists = $query->exists();
            
            $attempt++;
            
            // If SKU exists and we haven't exceeded max attempts, try again with different ID
            if ($exists && $attempt < $maxAttempts && !$this->id) {
                $uniqueId = uniqid('', true);
                $uniqueId = str_replace('.', '', $uniqueId);
                $sku = $prefix . $uniqueId;
                $exists = static::where('sku', $sku)->whereNull('deleted_at')->exists();
            }
        } while ($exists && $attempt < $maxAttempts);
        
        // Last resort fallback if all attempts fail
        if ($exists) {
            $sku = $prefix . 'FALLBACK-' . uniqid('', true);
            $sku = str_replace('.', '', $sku);
        }
        
        return $sku;
    }

    public function variants()
    {
        return $this->hasMany(InventoryItemVariant::class, 'inventory_item_id', 'id');
    }
}

