<?php

use App\Models\OrderItem;
use App\Models\Product;
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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Product::class)->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(OrderItem::class)->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('comment')->nullable();
            $table->integer('rating');
            $table->timestamps();

            $table->unique(['user_id', 'order_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
