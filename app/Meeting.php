<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = ['time','title','description'];

    //many to many
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
