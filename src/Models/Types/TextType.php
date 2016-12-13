<?php

namespace Imamuseum\Harvester2\Models\Types;

use Illuminate\Database\Eloquent\Model;

class TextType extends Model
{
    public function texts()
    {
        return $this->hasMany('Imamuseum\Harvester2\Models\Text');
    }

}
