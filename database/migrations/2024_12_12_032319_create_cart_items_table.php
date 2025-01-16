<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('guest_id')->nullable()->index();
            $table->foreignIdFor(Product::class)->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Variant::class)->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('quantity');
            $table->boolean('selected')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'guest_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
