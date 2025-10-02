<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'notes',
    ];

    /**
     * Get the supplies for the provider.
     */
    public function supplies(): HasMany
    {
        return $this->hasMany(Supply::class);
    }
}
