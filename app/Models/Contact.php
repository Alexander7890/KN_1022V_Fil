<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $table = 'contacts';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'note',
        'group_id',
    ];

    /**
     * Contact belongs to one group (optional).
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
