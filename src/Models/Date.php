<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    public $timestamps = false;

    protected $dates = ['date_at'];

    public function objects()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Object');
    }

    public function actors()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Actor');
    }

    public function type()
    {
        return $this->belongsTo('Imamuseum\Harvester2\Models\Types\DateType', 'date_type_id');
    }
}
