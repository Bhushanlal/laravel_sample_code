<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessForm extends Model {

    protected $table = 'business_forms';
    protected $fillable = [
        'type','content'
    ];

}
