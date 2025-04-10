<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportPack extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'imported_by', 'region'];

    public function lines()
    {
        return $this->hasMany(ImportLine::class);
    }

    public function importedBy()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
