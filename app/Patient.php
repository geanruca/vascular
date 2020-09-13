<?php

namespace App;

use App\User;
use App\PatientImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    
    protected $fillable = [
        'name',
        'diagnostic',
        'phone_number',
        'created_by',
        'updated_by'
    
    ];

    public function images()
    {
        return $this->hasMany(PatientImage::class,'patient_id','id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class,'created_by', 'id');
    }
    public function updated_by()
    {
        return $this->belongsTo(User::class,'updated_by','id');
    }
}
