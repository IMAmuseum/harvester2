<?php

namespace Imamuseum\Harvester2\Models\Types;

use Illuminate\Database\Eloquent\Model;

class DateType extends Model
{
    public function dates()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Date');
    }
}
