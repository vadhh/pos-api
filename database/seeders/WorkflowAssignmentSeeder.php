<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Workflow;
use App\Models\WorkflowAssignment;
use Carbon\Carbon;

class WorkflowAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workflows = Workflow::with('steps')->get();
        
        foreach ($workflows as $workflow) {
            // Create 2-3 assignments for each workflow
            $numAssignments = rand(2, 3);
            
            for ($i = 0; $i < $numAssignments; $i++) {
                $status = ['Pending', 'In Progress', 'Completed'][rand(0, 2)];
                $assignedTo = ['John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Wilson'][rand(0, 3)];
                $assignedBy = ['Admin', 'Manager', 'Supervisor', 'Director'][rand(0, 3)];
                $assignedAt = Carbon::now()->subDays(rand(1, 30));
                
                WorkflowAssignment::create([
                    'workflow_id' => $workflow->id,
                    'assigned_to' => $assignedTo,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => $assignedAt,
                    'due_date' => $assignedAt->copy()->addDays(rand(3, 7)),
                    'status' => $status,
                    'notes' => $this->generateNote($workflow->nama, $status)
                ]);
            }
        }
    }

    private function generateNote($workflowName, $status)
    {
        $notes = [
            'Pending' => [
                'Menunggu review dari supervisor',
                'Dokumen dalam antrian untuk diproses',
                'Menunggu verifikasi',
            ],
            'In Progress' => [
                'Sedang dalam proses review',
                'Dokumen sedang diverifikasi',
                'Menunggu persetujuan final',
            ],
            'Completed' => [
                'Semua tahapan telah selesai',
                'Dokumen telah disetujui',
                'Proses selesai dengan persetujuan',
            ]
        ];

        return $notes[$status][rand(0, 2)] . ' - ' . $workflowName;
    }
}
