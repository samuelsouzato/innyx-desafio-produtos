<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     * * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'expiration_date',
        'category_id',
        'image',
    ];

    protected function image(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value ? asset('storage/' . $value) : null,
    );
}
public function category()
{
    return $this->belongsTo(Category::class);
}

public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}