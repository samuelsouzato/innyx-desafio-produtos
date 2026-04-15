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
    
}