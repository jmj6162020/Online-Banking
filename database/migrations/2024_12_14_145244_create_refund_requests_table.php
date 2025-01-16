<?php

use App\Models\OrderItem;
use App\Models\User;
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
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(OrderItem::class)->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('campus');
            $table->text('reason');
            $table->string('status')->default('processing');
            $table->integer('quantity');
            $table->decimal('total');
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
