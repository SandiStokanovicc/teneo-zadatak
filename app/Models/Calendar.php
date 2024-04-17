<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'absence_id'];
    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }
}
