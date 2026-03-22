<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'timezone', 'currency'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function tutors(): HasMany
    {
        return $this->hasMany(Tutor::class);
    }

    public function managers(): HasMany
    {
        return $this->hasMany(RegionalManager::class);
    }
}
