<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkflowAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workflow_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'due_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    // Relationships
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
} 