<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $guarded = [];

    public function object()
    {
    	return $this->hasOne('Imamuseum\Harvester2\Models\Object');
    }

    public function assets()
    {
    	return $this->hasMany('Imamuseum\Harvester2\Models\Asset');
    }

}
