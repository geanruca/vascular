<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientImage extends Model
{
    protected $table = 'patient_images';

    protected $fillable = [
    'url',
    'uploaded_by',
    'patient_id'];
}
