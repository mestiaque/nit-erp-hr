<?php

namespace ME\Hr\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionRate extends Model
{
    protected $table = 'production_rates';
    protected $fillable = [
        'local_agent',
        'buyer',
        'style_name',
        'style_number',
        'gauge',
        'order_qty',
        'merchandiser',
        'process',
        'rate',
        'pro_process',
    ];

    public function processes()
    {
        return $this->hasMany(ProductionRateProcess::class);
    }
}
