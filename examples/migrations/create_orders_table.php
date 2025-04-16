<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            
            // Order details
            $table->decimal('subtotal', 12, 0);
            $table->decimal('tax', 12, 0)->default(0);
            $table->decimal('shipping', 12, 0)->default(0);
            $table->decimal('discount', 12, 0)->default(0);
            $table->decimal('total_amount', 12, 0);
            $table->text('notes')->nullable();
            
            // Payment related fields
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->default('zarinpal');
            $table->string('payment_authority')->nullable();
            $table->string('payment_reference_id')->nullable();
            $table->string('payment_card_pan')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            
            // Order status
            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])->default('new');
            
            $table->timestamps();
            
            // Foreign key relationship
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}