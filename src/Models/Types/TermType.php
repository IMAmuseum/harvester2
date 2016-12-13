<?php

namespace Imamuseum\Harvester2\Models\Types;

use Illuminate\Database\Eloquent\Model;

class TermType extends Model
{
    public function terms()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Term');
    }
}
