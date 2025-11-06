<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Data\Business\AddonQuotationData; // Import the DTO
use Spatie\LaravelData\WithData; // Import the trait

class AddonQuotation extends Model
{
    use HasFactory, SoftDeletes, WithData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'addon_quotations';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The data class associated with the model.
     *
     * @var string
     */
    protected $dataClass = AddonQuotationData::class;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the main quotation that this addon belongs to.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Quotation::class, 'quotation_id');
    }
}