<?php

namespace ME\Hr\Models;

use ME\Hr\Models\BaseHrModel;
use App\Models\User;
use App\Models\Attribute;

class Roaster extends BaseHrModel
{
    protected $table = 'roasters';
    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'section_id',
        'sub_section_id',
        'remarks',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function section()
    {
        return $this->belongsTo(Attribute::class);
    }
    public function subSection()
    {
        return $this->belongsTo(SubSection::class, 'sub_section_id');
    }
}
