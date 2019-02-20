<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use \Dimsav\Translatable\Translatable;

    protected $table = 'categories';
    protected $guarded = [];
    public $translatedAttributes = ['name'];

} // end of Model
