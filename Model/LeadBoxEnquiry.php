<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadBoxEnquiry extends Model
{
  protected $table = 'lead_box_enquiry';
  protected $fillable = [
    'advertisement_id',
    'advertiser_id',
    'form_data',
  ];

  public function enquiry(){
    return $this->belongsTo('App\BusinessEblast', 'advertisement_id');
  }

  public function advertiser(){
    return $this->belongsTo('App\User', 'advertiser_id');
  }
}
