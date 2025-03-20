<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkflowStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workflow_id',
        'nama',
        'deskripsi',
        'urutan',
        'minimal_verifikasi',
        'verifikator',
        'durasi'
    ];

    // Relationships
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
} 