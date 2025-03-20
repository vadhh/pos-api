<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('dibuat');
            $table->date('tgl_dibuat');
            $table->enum('status', ['Active', 'Inactive', 'Draft'])->default('Draft');
            $table->integer('tahapan')->default(1);
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes for better data management
        });

        // Create workflow_steps table for managing steps in each workflow
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('urutan');
            $table->integer('minimal_verifikasi')->default(1);
            $table->string('verifikator');
            $table->integer('durasi')->default(1); // Duration in days
            $table->timestamps();
            $table->softDeletes();
        });

        // Create workflow_assignments table for tracking workflow assignments
        Schema::create('workflow_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->string('assigned_to');
            $table->string('assigned_by');
            $table->timestamp('assigned_at');
            $table->timestamp('due_date')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Rejected'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_assignments');
        Schema::dropIfExists('workflow_steps');
        Schema::dropIfExists('workflows');
    }
};
