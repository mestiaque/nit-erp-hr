<?php

namespace ME\Hr\Models;

use ME\Hr\Models\BaseHrModel;

class EmployeeIncrement extends BaseHrModel
{
    protected $table = 'employee_increments';
    protected $fillable = [
        'user_id',
        'increment_date',
        'previous_salary',
        'increment_amount',
        'increment_percentage',
        'new_salary',
        'remarks',
        'previous_salary_comp_1',
        'new_salary_comp_1',
        'previous_salary_comp_2',
        'new_salary_comp_2',
        // 'approved_by', // IGNORE
    ];
}
