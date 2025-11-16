<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $table = 'contact_groups';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /**
     * One group has many contacts.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'group_id');
    }
}
