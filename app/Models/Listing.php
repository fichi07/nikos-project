<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded =[
        "id",
    ];
  /*   protected $fillable =[
        'titile',
        'slug',
        'description',
        'address',
        'sqft',
        'wifi_speed',
        'max_person',
        'price_per_day',
        'attachments',
        'full_support_available',
        'gym_area_available',
        'mini_cafe_available',
        'cinema_available'


    ]; */

    protected $casts = [
        'attachments'=> 'array'];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    public function setTitleAtAttribute($value){
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

}
