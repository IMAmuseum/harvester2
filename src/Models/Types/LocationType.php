<?php

namespace Imamuseum\Harvester2\Models\Types;

use Illuminate\Database\Eloquent\Model;

class LocationType extends Model
{
    public function locations()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Location');
    }
}
