<?php

namespace App\Models\Business;

use App\Data\Business\UnitPropertyConfigData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelData\WithData;

class UnitPropertyConfig extends Model
{
    use HasFactory, SoftDeletes, WithData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unit_property_configs';

    /**
     * The data class associated with the model.
     *
     * @var string
     */
    protected $dataClass = UnitPropertyConfigData::class;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'bedroom_count' => 'integer',
        'single_bedroom_count' => 'integer',
        'queen_bedroom_count' => 'integer',
        'studio_count' => 'integer',
        'bathroom_count' => 'integer',
        'include_partition' => 'boolean',
    ];

    /**
     * Get the main quotation that this config belongs to.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Quotation::class, 'quotation_id');
    }
}