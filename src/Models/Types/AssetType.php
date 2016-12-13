<?php

namespace Imamuseum\Harvester2\Models\Types;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    public function assets()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Asset');
    }
}
