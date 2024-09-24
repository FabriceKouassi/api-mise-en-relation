<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $with = 'services';
    
    protected $fillable = [
        'libelle',
        'slug',
        'description',
    ];
    
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
