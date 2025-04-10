<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImportLandlord extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_line_id',
        'name',
        'address',
        'postal_code',
        'city',
    ];

    public function importLine()
    {
        return $this->belongsTo(ImportLine::class);
    }
}
