<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    //
    protected $table = 'faq_db';
    protected $fillable = ['id','question','answer','created_At'];

}
