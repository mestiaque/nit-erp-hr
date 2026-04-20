<?php

namespace ME\Hr\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionRateProcess extends Model
{
    protected $table = 'production_rate_processes';
    protected $fillable = [
        'production_rate_id',
        'process',
        'rate',
        'pro_process',
    ];

    public function productionRate()
    {
        return $this->belongsTo(ProductionRate::class);
    }
}
