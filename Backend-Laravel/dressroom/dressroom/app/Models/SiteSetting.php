<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'tagline',
        'welcome_title',
        'welcome_description',
        'editorial_image',
        'editorial_small_text',
        'editorial_heading',
    ];

    public function getEditorialImageAttribute($value)
    {
        if (!$value) return $value;
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        return asset(ltrim($value, '/'));
    }
}