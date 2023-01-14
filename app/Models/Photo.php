<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable =['user_id', 'caption', 'tag', 'photo'];
    
    // accessor untuk mengganti url photo di API
	public function getPhotoAttribute($value)
	{
		return url('storage/' . $value);
	}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
