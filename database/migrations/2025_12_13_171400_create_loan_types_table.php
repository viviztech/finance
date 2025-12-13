<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\LoanFrequency;
use App\Enums\InterestType;
use App\Enums\PenaltyType;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('frequency'); // daily, weekly, biweekly, monthly
            $table->string('interest_type'); // fixed, percentage
            $table->decimal('interest_rate', 10, 2);
            $table->integer('default_duration')->comment('Number of installments');
            $table->decimal('min_amount', 12, 2)->nullable();
            $table->decimal('max_amount', 12, 2)->nullable();

            // Penalty configuration (optional)
            $table->boolean('penalty_enabled')->default(false);
            $table->string('penalty_type')->nullable(); // fixed, percentage
            $table->decimal('penalty_rate', 10, 2)->nullable();
            $table->integer('grace_period_days')->default(0);

            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['branch_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};
