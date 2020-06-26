<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organizations extends Model
{
    protected $table = 'organizations';
    public $incrementing = true; // if IDs are auto-incrementing.
    public $timestamps = true; // if the model should be timestamped.

    public function objectives()
    {
        return $this->belongsToMany('App\Objective','objective_organization','organization_id','objective_id');
    }
}