<?php

use App\Models\Order;
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
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('student_1');
            $table->string('student_1_yrlvl');

            $table->string('student_2')->nullable();
            $table->string('student_2_yrlvl')->nullable();

            $table->string('student_3')->nullable();
            $table->string('student_3_yrlvl')->nullable();

            $table->string('student_4')->nullable();
            $table->string('student_4_yrlvl')->nullable();

            $table->foreignIdFor(Order::class)->nullable()
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->date('starting_date');
            $table->date('ending_date');
            $table->decimal('amount');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_deductions');
    }
};
