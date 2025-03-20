<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workflows = Workflow::with('steps')->get();
        return response()->json($workflows);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dibuat' => 'required|string',
            'tgl_dibuat' => 'required|date',
            'status' => 'required|string|in:Active,Inactive,Draft',
            'tahapan' => 'required|integer|min:1',
            'steps' => 'nullable|array',
            'steps.*.nama' => 'required|string|max:255',
            'steps.*.deskripsi' => 'nullable|string',
            'steps.*.urutan' => 'required|integer|min:1',
            'steps.*.minimal_verifikasi' => 'required|integer|min:1',
            'steps.*.verifikator' => 'required|string|max:255',
            'steps.*.durasi' => 'required|integer|min:1',
        ]);

        $workflow = Workflow::create($validated);
        
        // Create workflow steps if provided
        if ($request->has('steps') && is_array($request->steps)) {
            foreach ($request->steps as $stepData) {
                $workflow->steps()->create($stepData);
            }
        }
        
        return response()->json(Workflow::with('steps')->find($workflow->id), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workflow = Workflow::with('steps')->findOrFail($id);
        return response()->json($workflow);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workflow = Workflow::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dibuat' => 'required|string',
            'tgl_dibuat' => 'required|date',
            'status' => 'required|string|in:Active,Inactive,Draft',
            'tahapan' => 'required|integer|min:1',
            'steps' => 'nullable|array',
            'steps.*.id' => 'nullable|integer|exists:workflow_steps,id',
            'steps.*.nama' => 'required|string|max:255',
            'steps.*.deskripsi' => 'nullable|string',
            'steps.*.urutan' => 'required|integer|min:1',
            'steps.*.minimal_verifikasi' => 'required|integer|min:1',
            'steps.*.verifikator' => 'required|string|max:255',
            'steps.*.durasi' => 'required|integer|min:1',
        ]);

        $workflow->update($validated);
        
        // Update workflow steps if provided
        if ($request->has('steps') && is_array($request->steps)) {
            // Get existing step IDs
            $existingStepIds = $workflow->steps->pluck('id')->toArray();
            $updatedStepIds = [];
            
            foreach ($request->steps as $stepData) {
                if (isset($stepData['id'])) {
                    // Update existing step
                    $step = $workflow->steps()->find($stepData['id']);
                    if ($step) {
                        $step->update($stepData);
                        $updatedStepIds[] = $step->id;
                    }
                } else {
                    // Create new step
                    $step = $workflow->steps()->create($stepData);
                    $updatedStepIds[] = $step->id;
                }
            }
            
            // Delete steps that weren't updated
            $stepsToDelete = array_diff($existingStepIds, $updatedStepIds);
            if (!empty($stepsToDelete)) {
                $workflow->steps()->whereIn('id', $stepsToDelete)->delete();
            }
        }
        
        return response()->json(Workflow::with('steps')->find($workflow->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workflow = Workflow::findOrFail($id);
        $workflow->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
