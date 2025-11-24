<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
  'name', 'description', 'image', 'slug', 'type', 'is_active'
];

    public function offers() {
        return $this->hasMany(Offer::class);
    }
}