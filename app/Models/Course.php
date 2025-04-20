<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'author',
        'status'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_course', 'course_id', 'category_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'course_id');
    }
}
