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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('distribution_id')
                ->constrained()
                ->restrictOnDelete();
            
            $table->dateTime('incident_at');
            $table->text('description');
            $table->unsignedSmallInteger('people_affected')->default(1);
            $table->string('evidence_path')->nullable();
            $table->text('review_note')->nullable();
            $table->boolean('flagged_for_followup')->default(false);
            $table->string('forwarded_to')->nullable();
            $table->timestamp('forwarded_at')->nullable();
            $table->foreignId('forwarded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')->default('dikirim');
            $table->index(['status', 'incident_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
