<?php

namespace ME\Hr\Models;

use Illuminate\Database\Eloquent\Model;

class RegularToWeekend extends Model
{
    protected $table = 'regular_to_weekends';
    protected $fillable = [
        'section_id', 'date', 'type', 'is_active',
    ];

    public function section()
    {
        // Section is an Attribute with type 29
        return $this->belongsTo(\App\Models\Attribute::class, 'section_id');
    }
}
