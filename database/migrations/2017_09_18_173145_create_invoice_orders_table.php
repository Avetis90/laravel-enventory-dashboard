<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prefix', 10);
            $table->string('order_id')->unique();
            $table->string('track_num')->unique();
            $table->text('sku');
            $table->decimal('weight', 5, 3);
            $table->decimal('product_cost', 5, 2);
            $table->decimal('shipping_cost', 5, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_orders');
    }
}
