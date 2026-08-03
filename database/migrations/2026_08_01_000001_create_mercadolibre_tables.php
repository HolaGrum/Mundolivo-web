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
        Schema::create('mercadolibre_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ml_user_id')->index()->nullable();
            $table->string('nickname')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('site_id')->default('MLV');
            $table->string('status')->default('disconnected');
            $table->timestamps();
        });

        Schema::create('mercadolibre_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('ml_item_id')->unique();
            $table->string('ml_category_id')->nullable();
            $table->string('title')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('available_quantity')->default(0);
            $table->string('status')->default('active');
            $table->string('permalink')->nullable();
            $table->string('listing_type_id')->default('gold_special');
            $table->string('condition')->default('new');
            $table->timestamp('last_synced_at')->nullable();
            $table->text('sync_error')->nullable();
            $table->timestamps();
        });

        Schema::create('mercadolibre_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('ml_order_id')->unique();
            $table->string('ml_buyer_id')->nullable();
            $table->string('ml_buyer_nickname')->nullable();
            $table->string('ml_status')->nullable();
            $table->string('ml_shipping_id')->nullable();
            $table->string('ml_payment_id')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercadolibre_orders');
        Schema::dropIfExists('mercadolibre_products');
        Schema::dropIfExists('mercadolibre_accounts');
    }
};
