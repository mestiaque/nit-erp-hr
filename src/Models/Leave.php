<?php

namespace ME\Hr\Models;

use ME\Hr\Models\BaseHrModel;

class Leave extends BaseHrModel
{
    protected $table = 'leaves';
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'application_date',
        'application_no',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'remarks',
    ];

    public function leaveType()
    {
        return $this->belongsTo(LeaveInfo::class, 'leave_type_id');
    }

}
