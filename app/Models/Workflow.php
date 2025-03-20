<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'deskripsi',
        'dibuat',
        'tgl_dibuat',
        'status',
        'tahapan'
    ];

    protected $casts = [
        'tgl_dibuat' => 'date',
    ];

    // Relationships
    public function steps()
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('urutan');
    }

    public function assignments()
    {
        return $this->hasMany(WorkflowAssignment::class);
    }
} 