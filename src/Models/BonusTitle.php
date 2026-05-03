<?php

namespace ME\Hr\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use ME\Hr\Models\BaseHrModel;

class BonusTitle extends BaseHrModel
{
    protected $table = 'hr_bonus_titles';

    public function policies(): HasMany
    {
        return $this->hasMany(BonusPolicy::class, 'bonus_title_id');
    }
}
