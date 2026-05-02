<?php

namespace ME\Hr\Models;

use ME\Hr\Models\BaseHrModel;

class Attendance extends BaseHrModel
{
    protected $table = 'attendances';
    protected $fillable = [
        'user_id',
        'date',
        'in_time',
        'out_time',
        'overtime_minutes',
        'status',
        'remarks',
    ];
}


// ME\Hr\Models\Shift.php
//shift_starting_time
//shift_closing_time
//card_accept_from = Start Allow Time
//red_marking_on = Late Allow Time
//card_accept_to = Out Time Start

// Status: Present, Absent, Late, Punch Missing
