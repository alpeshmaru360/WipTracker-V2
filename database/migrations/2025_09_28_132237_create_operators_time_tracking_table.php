<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('operators_time_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained('project_process_std_times')->onDelete('cascade');
            $table->string('operator_id'); // Keeping as string to match your current structure
            $table->enum('status', ['running', 'paused', 'stopped'])->default('stopped');
            $table->timestamp('session_start')->nullable();
            $table->timestamp('session_end')->nullable();
            $table->integer('total_seconds')->default(0);
            $table->integer('pause_duration')->default(0); // Track total pause time
            $table->timestamp('last_activity')->nullable(); // For cleanup of stale sessions
            $table->timestamps();
            
            // Ensure one record per operator per process
            $table->unique(['process_id', 'operator_id']);
            
            // Indexes for better performance
            $table->index(['process_id', 'status']);
            $table->index('operator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators_time_tracking');
    }
};
