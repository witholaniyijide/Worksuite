<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'relationship', 'phone', 'alt_email', 'address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }
}
