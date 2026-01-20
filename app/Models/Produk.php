<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'about',
        'price',
        'stock',
        'is_popular',
        'category_id',
        'brand_id',
    ]; 

   public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug(title:$value);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(related:Brand::class, foreignKey:'brand_id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(related:Category::class, foreignKey:'category_id');
    }
    public function photos(): hasMany
    {
        return $this->hasMany(related:ProdukPhoto::class);
    }
    public function sizes(): hasMany
    {
        return $this->hasMany(related:ProdukSize::class);
    }
}
