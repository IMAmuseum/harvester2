<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Deaccession extends Model
{
    public function object()
    {
    	return $this->belongsTo('Imamuseum\Harvester2\Models\Object');
	}
}
