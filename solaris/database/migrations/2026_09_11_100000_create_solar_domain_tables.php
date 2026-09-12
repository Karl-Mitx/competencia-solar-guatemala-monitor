<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('name', 80)->unique();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('color', 7);
            $table->timestamps();
        });

        Schema::create('solar_panels', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->decimal('nominal_power_kw', 10, 3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['brand', 'model']);
        });

        Schema::create('solar_farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('location_name', 180);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('families_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('commissioned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['department_id', 'is_active']);
        });

        Schema::create('farm_panel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('solar_panel_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(['solar_farm_id', 'solar_panel_id']);
        });

        Schema::create('generation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_farm_id')->constrained()->restrictOnDelete();
            $table->date('period');
            $table->decimal('real_kwh', 16, 2);
            $table->decimal('expected_kwh', 16, 2);
            $table->timestamps();
            $table->unique(['solar_farm_id', 'period']);
            $table->index('period');
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generation_record_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status', 16)->default('active')->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('projections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_farm_id')->constrained()->restrictOnDelete();
            $table->date('period');
            $table->decimal('projected_kwh', 16, 2);
            $table->string('method', 80);
            $table->date('training_through');
            $table->unsignedSmallInteger('sample_size');
            $table->timestamp('generated_at');
            $table->timestamps();
            $table->unique(['solar_farm_id', 'period']);
            $table->index('period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projections');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('generation_records');
        Schema::dropIfExists('farm_panel');
        Schema::dropIfExists('solar_farms');
        Schema::dropIfExists('solar_panels');
        Schema::dropIfExists('departments');
    }
};
