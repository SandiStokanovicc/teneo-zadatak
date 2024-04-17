<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarArchive extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'absence_id', 'archive_number'];
    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }
}
