<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Carbon\Carbon;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workflows = [
            [
                'nama' => 'Persetujuan Dokumen Keuangan',
                'deskripsi' => 'Workflow untuk persetujuan dokumen keuangan dan pengeluaran',
                'dibuat' => 'Admin Keuangan',
                'tgl_dibuat' => '2024-03-18',
                'status' => 'Active',
                'tahapan' => 3,
                'steps' => [
                    [
                        'nama' => 'Review oleh Supervisor Keuangan',
                        'deskripsi' => 'Pemeriksaan awal dokumen keuangan',
                        'urutan' => 1,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Supervisor Keuangan',
                        'durasi' => 2
                    ],
                    [
                        'nama' => 'Verifikasi oleh Manager Keuangan',
                        'deskripsi' => 'Pemeriksaan dan verifikasi oleh manager',
                        'urutan' => 2,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Manager Keuangan',
                        'durasi' => 2
                    ],
                    [
                        'nama' => 'Persetujuan Final Direktur',
                        'deskripsi' => 'Persetujuan akhir oleh direktur',
                        'urutan' => 3,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Direktur',
                        'durasi' => 1
                    ]
                ]
            ],
            [
                'nama' => 'Review Laporan Bulanan',
                'deskripsi' => 'Proses review dan persetujuan laporan bulanan departemen',
                'dibuat' => 'Manager HR',
                'tgl_dibuat' => '2024-03-17',
                'status' => 'Active',
                'tahapan' => 2,
                'steps' => [
                    [
                        'nama' => 'Review Department Head',
                        'deskripsi' => 'Review oleh kepala departemen',
                        'urutan' => 1,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Department Head',
                        'durasi' => 3
                    ],
                    [
                        'nama' => 'Approval Direktur',
                        'deskripsi' => 'Persetujuan final oleh direktur',
                        'urutan' => 2,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Direktur',
                        'durasi' => 2
                    ]
                ]
            ],
            [
                'nama' => 'Pengajuan Cuti',
                'deskripsi' => 'Workflow untuk pengajuan dan persetujuan cuti karyawan',
                'dibuat' => 'Admin HR',
                'tgl_dibuat' => '2024-03-16',
                'status' => 'Active',
                'tahapan' => 2,
                'steps' => [
                    [
                        'nama' => 'Persetujuan Supervisor',
                        'deskripsi' => 'Persetujuan dari supervisor langsung',
                        'urutan' => 1,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Supervisor',
                        'durasi' => 1
                    ],
                    [
                        'nama' => 'Verifikasi HR',
                        'deskripsi' => 'Verifikasi ketersediaan cuti oleh HR',
                        'urutan' => 2,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'HR Manager',
                        'durasi' => 1
                    ]
                ]
            ],
            [
                'nama' => 'Permintaan Pengadaan',
                'deskripsi' => 'Proses permintaan dan persetujuan pengadaan barang',
                'dibuat' => 'Admin Procurement',
                'tgl_dibuat' => '2024-03-15',
                'status' => 'Draft',
                'tahapan' => 4,
                'steps' => [
                    [
                        'nama' => 'Review Supervisor',
                        'deskripsi' => 'Review awal oleh supervisor',
                        'urutan' => 1,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Supervisor',
                        'durasi' => 1
                    ],
                    [
                        'nama' => 'Verifikasi Budget',
                        'deskripsi' => 'Verifikasi ketersediaan budget',
                        'urutan' => 2,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Finance Manager',
                        'durasi' => 2
                    ],
                    [
                        'nama' => 'Approval Procurement',
                        'deskripsi' => 'Persetujuan dari procurement',
                        'urutan' => 3,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Procurement Manager',
                        'durasi' => 2
                    ],
                    [
                        'nama' => 'Final Approval',
                        'deskripsi' => 'Persetujuan akhir',
                        'urutan' => 4,
                        'minimal_verifikasi' => 1,
                        'verifikator' => 'Direktur',
                        'durasi' => 1
                    ]
                ]
            ]
        ];

        foreach ($workflows as $workflowData) {
            $steps = $workflowData['steps'];
            unset($workflowData['steps']);
            
            $workflow = Workflow::create($workflowData);
            
            foreach ($steps as $step) {
                $workflow->steps()->create($step);
            }
        }
    }
}
