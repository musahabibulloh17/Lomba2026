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
        Schema::create('nlp_commands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('command');
            $table->string('intent')->nullable();
            $table->jsonb('entities')->default('{}');
            $table->text('response')->nullable();
            $table->string('action_taken')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->decimal('processing_time', 10, 2)->nullable()->comment('Processing time in milliseconds');
            $table->jsonb('workflow_spec')->nullable()->comment('OpenSpec compliant workflow specification');
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('intent');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nlp_commands');
    }
};
