<?php

namespace ME\Hr\Models;

use ME\Hr\Models\BaseHrModel;

class SubSection extends BaseHrModel
{
    protected $table = 'hr_sub_sections';

    protected $casts = [
        'is_individual_roster' => 'boolean',
    ];
}
