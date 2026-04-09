<?php

namespace ME\Hr\Models;

use ME\Hr\Models\BaseHrModel;

class Shift extends BaseHrModel
{
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
