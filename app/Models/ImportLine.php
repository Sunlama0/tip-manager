<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_address',
        'postal_code',
        'city',
        'cadaster_number',
        'landlord',
        'landlord_address',
        'landlord_postal_code',
        'landlord_city',
        'import_pack_id',
        'assigned_to',
        'status',
    ];

    public function pack()
    {
        return $this->belongsTo(ImportPack::class, 'import_pack_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function importPack()
    {
        return $this->belongsTo(\App\Models\ImportPack::class);
    }

    public function landlords()
    {
        return $this->hasMany(ImportLandlord::class);
    }
}
