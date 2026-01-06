<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class InventoryItemVariant extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'inventory_item_variants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_item_id',
        'product_id',
        'variant_name',
        'type',
        'sku',
        'description',
        'in_stock',
        'projected_stock',
        'utilised_stock',
        'incoming_stock',
        'supply_price',
        'install_price',
        // 'quantity' removed - now calculated dynamically as in_stock + projected_stock
        'alert_level',
        'status',
        'color',
        'width',
        'height',
        'depth',
        'material',
        'zone',
        'shelf_level',
        'rack_no',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'in_stock' => 'integer',
        'projected_stock' => 'integer',
        'utilised_stock' => 'integer',
        'incoming_stock' => 'integer',
        'total_balance' => 'integer',
        'supply_price' => 'decimal:2',
        'install_price' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'depth' => 'decimal:2',
        'product_id' => 'string',
        'alert_level' => 'integer',
        'status' => 'string',
    ];


    /**
     * Boot the model and register event listeners.
     * Automatically calculate total_balance whenever in_stock or projected_stock changes.
     * Automatically inherit zone from parent inventory_item if not explicitly set.
     * Auto-generate SKU from variant_name if SKU is not provided.
     */
    protected static function boot()
    {
        parent::boot();

        // Calculate and store total_balance before saving/updating
        static::saving(function ($variant) {
            $inStock = $variant->in_stock ?? 0;
            $projectedStock = $variant->projected_stock ?? 0;
            // Calculate total_balance and store it in the database
            $variant->attributes['total_balance'] = (int) ($inStock + $projectedStock);
            
            // Inherit zone from parent inventory_item if zone is not explicitly set
            if (empty($variant->zone) && $variant->inventory_item_id) {
                $parentItem = InventoryItem::find($variant->inventory_item_id);
                if (!$parentItem) {
                    Log::warning('Parent inventory item not found for variant during zone inheritance', [
                        'variant_id' => $variant->id ?? 'new',
                        'inventory_item_id' => $variant->inventory_item_id
                    ]);
                } elseif ($parentItem->zone) {
                    $variant->zone = $parentItem->zone;
                }
            }
            
            // Compose full SKU: <parent_item.sku>-<variant_sku>
            // Frontend sends only the variant SKU part; backend composes the full SKU
            if ($variant->inventory_item_id) {
                $parentItem = InventoryItem::find($variant->inventory_item_id);
                
                if (!$parentItem) {
                    Log::warning('Parent inventory item not found for variant during SKU composition', [
                        'variant_id' => $variant->id ?? 'new',
                        'inventory_item_id' => $variant->inventory_item_id
                    ]);
                } elseif (empty($parentItem->sku)) {
                    Log::warning('Parent inventory item has no SKU for variant SKU composition', [
                        'variant_id' => $variant->id ?? 'new',
                        'inventory_item_id' => $variant->inventory_item_id
                    ]);
                } else {
                    // Get the variant SKU part (what frontend sends)
                    $variantSkuPart = $variant->sku;
                    
                    // Check if SKU already contains the parent prefix (to avoid duplication)
                    // This happens when updating an existing variant
                    $parentPrefix = $parentItem->sku . '-';
                    if (!empty($variantSkuPart) && strpos($variantSkuPart, $parentPrefix) === 0) {
                        // SKU already has parent prefix, extract just the variant part
                        $variantSkuPart = substr($variantSkuPart, strlen($parentPrefix));
                    }
                    
                    // If variant SKU is empty, auto-generate from variant_name
                    if (empty($variantSkuPart) && !empty($variant->variant_name)) {
                        $variantSkuPart = $variant->generateSkuFromName($variant->variant_name);
                    }
                    
                    // Normalize variant SKU part
                    if (!empty($variantSkuPart)) {
                        // Ensure SKU format is correct (uppercase, no spaces, only alphanumeric and hyphens)
                        $variantSkuPart = strtoupper($variantSkuPart);
                        $variantSkuPart = preg_replace('/\s+/', '-', $variantSkuPart); // Replace spaces with hyphens
                        $variantSkuPart = preg_replace('/[^A-Z0-9-]/', '', $variantSkuPart); // Remove invalid characters
                        // Remove multiple consecutive hyphens
                        $variantSkuPart = preg_replace('/-+/', '-', $variantSkuPart);
                        // Trim hyphens from start and end
                        $variantSkuPart = trim($variantSkuPart, '-');
                        
                        // If variant SKU part becomes empty after normalization, generate fallback
                        if (empty($variantSkuPart)) {
                            $variantSkuPart = $variant->generateFallbackSkuPart();
                        }
                    } else {
                        // If still empty, generate fallback
                        $variantSkuPart = $variant->generateFallbackSkuPart();
                    }
                    
                    // Compose full SKU: <parent_sku>-<variant_sku_part>
                    $variant->sku = $parentItem->sku . '-' . $variantSkuPart;
                }
            } else {
                // If no parent item ID, fall back to old behavior (shouldn't happen in normal flow)
                if (empty($variant->sku) && !empty($variant->variant_name)) {
                    $variant->sku = $variant->generateSkuFromName($variant->variant_name);
                } elseif (!empty($variant->sku)) {
                    // Normalize SKU
                    $variant->sku = strtoupper($variant->sku);
                    $variant->sku = preg_replace('/\s+/', '-', $variant->sku);
                    $variant->sku = preg_replace('/[^A-Z0-9-]/', '', $variant->sku);
                    $variant->sku = preg_replace('/-+/', '-', $variant->sku);
                    $variant->sku = trim($variant->sku, '-');
                    if (empty($variant->sku)) {
                        $variant->sku = $variant->generateFallbackSku();
                    }
                }
            }
        });
        
        // After saving, ensure zone is inherited if still empty
        static::saved(function ($variant) {
            if (empty($variant->zone) && $variant->inventory_item_id) {
                $parentItem = InventoryItem::find($variant->inventory_item_id);
                if ($parentItem && $parentItem->zone) {
                    $variant->zone = $parentItem->zone;
                    // Save again to persist the inherited zone
                    $variant->saveQuietly(); // Use saveQuietly to avoid triggering events again
                }
            }
        });
    }

    /**
     * Get the total_balance attribute.
     * If total_balance is not set or stock values changed, calculate it.
     *
     * @return int
     */
    public function getTotalBalanceAttribute($value)
    {
        // Always calculate from current stock values to ensure accuracy
        $inStock = $this->attributes['in_stock'] ?? 0;
        $projectedStock = $this->attributes['projected_stock'] ?? 0;
        return (int) ($inStock + $projectedStock);
    }

    /**
     * Prevent direct assignment to total_balance.
     * Total balance is automatically calculated from in_stock + projected_stock.
     * To change total_balance, update in_stock or projected_stock instead.
     *
     * @param mixed $value
     * @return void
     */
    public function setTotalBalanceAttribute($value)
    {
        // Calculate total_balance from stock values instead of using the provided value
        $inStock = $this->attributes['in_stock'] ?? 0;
        $projectedStock = $this->attributes['projected_stock'] ?? 0;
        $this->attributes['total_balance'] = (int) ($inStock + $projectedStock);
    }

    /**
     * Generate SKU from variant name.
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
     * Generate a fallback SKU part (variant part only, without parent SKU prefix).
     * Used when variant SKU part cannot be generated from name.
     *
     * @return string
     */
    protected function generateFallbackSkuPart(): string
    {
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
            
            $skuPart = 'VAR-' . $uniqueId;
            
            // Check if this SKU part would create a duplicate full SKU
            // We need to check against the composed full SKU, so we need the parent SKU
            if ($this->inventory_item_id) {
                $parentItem = InventoryItem::find($this->inventory_item_id);
                if ($parentItem && $parentItem->sku) {
                    $fullSku = $parentItem->sku . '-' . $skuPart;
                    $query = static::where('sku', $fullSku)->whereNull('deleted_at');
                    if ($this->id) {
                        $query->where('id', '!=', $this->id);
                    }
                    $exists = $query->exists();
                } else {
                    // If no parent SKU, just use the part as-is (shouldn't happen in normal flow)
                    $exists = false;
                }
            } else {
                // If no parent item, check uniqueness of the part itself (fallback)
                $query = static::where('sku', 'LIKE', '%' . $skuPart)->whereNull('deleted_at');
                if ($this->id) {
                    $query->where('id', '!=', $this->id);
                }
                $exists = $query->exists();
            }
            
            $attempt++;
            
            // If SKU exists and we haven't exceeded max attempts, try again with different ID
            if ($exists && $attempt < $maxAttempts && !$this->id) {
                $uniqueId = uniqid('', true);
                $uniqueId = str_replace('.', '', $uniqueId);
                $skuPart = 'VAR-' . $uniqueId;
                if ($this->inventory_item_id) {
                    $parentItem = InventoryItem::find($this->inventory_item_id);
                    if ($parentItem && $parentItem->sku) {
                        $fullSku = $parentItem->sku . '-' . $skuPart;
                        $exists = static::where('sku', $fullSku)->whereNull('deleted_at')->exists();
                    } else {
                        $exists = false;
                    }
                } else {
                    $exists = static::where('sku', 'LIKE', '%' . $skuPart)->whereNull('deleted_at')->exists();
                }
            }
        } while ($exists && $attempt < $maxAttempts);
        
        // Last resort fallback if all attempts fail
        if ($exists) {
            $skuPart = 'VAR-FALLBACK-' . uniqid('', true);
            $skuPart = str_replace('.', '', $skuPart);
        }
        
        return $skuPart;
    }

    /**
     * Generate a fallback SKU when the name-based generation results in an empty string.
     * Uses a prefix and unique identifier to ensure uniqueness.
     * NOTE: This method is kept for backward compatibility but should not be used for variants.
     * Variants should use generateFallbackSkuPart() instead.
     *
     * @return string
     */
    protected function generateFallbackSku(): string
    {
        // For variants, this should not be called directly
        // But if called, generate the part and compose with parent SKU
        $skuPart = $this->generateFallbackSkuPart();
        
        if ($this->inventory_item_id) {
            $parentItem = InventoryItem::find($this->inventory_item_id);
            if ($parentItem && $parentItem->sku) {
                return $parentItem->sku . '-' . $skuPart;
            }
        }
        
        // Fallback if no parent (shouldn't happen)
        return 'VAR-' . $skuPart;
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id', 'id');
    }
}

