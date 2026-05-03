<?php

namespace ME\Hr\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ME\Hr\Models\BaseHrModel;

class BonusPolicy extends BaseHrModel
{
    protected $table = 'hr_bonus_policies';

    public function bonusTitle(): BelongsTo
    {
        return $this->belongsTo(BonusTitle::class, 'bonus_title_id');
    }
}
