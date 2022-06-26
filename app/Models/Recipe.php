<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Recipe extends Model
{
    
    protected $appends = ['imageUrl'];

    public function getImageUrlAttribute(){
        return Storage::url('img/recipeImages/'.$this->id.'_image.jpg');
    }
}