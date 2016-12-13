<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Object extends Model
{
    protected $guarded = [];

    public function actors()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Actor')->withPivot('sequence', 'role');
    }

    public function assets()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Asset');
    }

    public function source()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Source');
    }

    public function terms()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Term');
    }

    public function texts()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Text');
    }

    public function locations()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Location');
    }

    public function dates()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Date');
    }

    public function deaccession()
    {
        return $this->hasOne('Imamuseum\Harvester2\Models\Deaccession');
    }

    public function transactions()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Transaction', 'table_id');
    }

    public function ownedBy()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Object', 'relationships', 'object_id', 'related_id')
            ->withPivot('relationship', 'notes');
    }

    public function owns()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Object', 'relationships', 'related_id', 'object_id')
            ->withPivot('relationship', 'notes');
    }
}
