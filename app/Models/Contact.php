<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'gender', 'profile_image', 'additional_file', 'is_active', 'merged_into'
    ];

    protected $appends = [
        'profile_image_url',
        'additional_file_url',
        'additional_file_name'
    ];

    public function customFieldValues()
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {
            return null;
        }
        return asset($this->profile_image);
    }

    public function getAdditionalFileUrlAttribute()
    {
        if (!$this->additional_file) {
            return null;
        }
        return asset($this->additional_file);
    }
    public function getAdditionalFileNameAttribute()
    {
        if (!$this->additional_file) {
            return null;
        }
        return basename($this->additional_file);
    }

    public function meta(){
        return $this->hasMany(ContactCustomFieldValue::class);
    }
}