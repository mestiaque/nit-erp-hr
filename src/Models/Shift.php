<?php

namespace ME\Hr\Models;

use ME\Hr\Models\BaseHrModel;

class Shift extends BaseHrModel
{

    //shift_starting_time
    //shift_closing_time
    //card_accept_from = Start Allow Time
    //red_marking_on = Late Allow Time
    //card_accept_to = Out Time Start
    protected $table = 'hr_shifts';

    protected $casts = [
        'shift_closing_time_next_day' => 'boolean',
        'over_time_allowed_up_to_next_day' => 'boolean',
        'over_time_1_allowed_up_to_next_day' => 'boolean',
        'card_accept_to_next_day' => 'boolean',
        'no_lunch_hour_holiday' => 'boolean',
        'dinner_allowance' => 'boolean',
        'double_shift' => 'boolean',
    ];
}
