<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WorkflowStepController extends Controller
{
    /**
     * Display a listing of the steps for a workflow.
     */
    public function index(string $workflowId)
    {
        $workflow = Workflow::findOrFail($workflowId);
        $steps = $workflow->steps;
        return response()->json($steps);
    }

    /**
     * Store a newly created step in storage.
     */
    public function store(Request $request, string $workflowId)
    {
        $workflow = Workflow::findOrFail($workflowId);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:1',
            'minimal_verifikasi' => 'required|integer|min:1',
            'verifikator' => 'required|string|max:255',
            'durasi' => 'required|integer|min:1',
        ]);

        $step = $workflow->steps()->create($validated);
        return response()->json($step, Response::HTTP_CREATED);
    }

    /**
     * Display the specified step.
     */
    public function show(string $workflowId, string $stepId)
    {
        $workflow = Workflow::findOrFail($workflowId);
        $step = $workflow->steps()->findOrFail($stepId);
        return response()->json($step);
    }

    /**
     * Update the specified step in storage.
     */
    public function update(Request $request, string $workflowId, string $stepId)
    {
        $workflow = Workflow::findOrFail($workflowId);
        $step = $workflow->steps()->findOrFail($stepId);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:1',
            'minimal_verifikasi' => 'required|integer|min:1',
            'verifikator' => 'required|string|max:255',
            'durasi' => 'required|integer|min:1',
        ]);

        $step->update($validated);
        return response()->json($step);
    }

    /**
     * Remove the specified step from storage.
     */
    public function destroy(string $workflowId, string $stepId)
    {
        $workflow = Workflow::findOrFail($workflowId);
        $step = $workflow->steps()->findOrFail($stepId);
        $step->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}