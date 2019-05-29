<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUbiOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubi_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id')->unique();
            $table->date('date');
            $table->string('tracking_number')->nullable();
            $table->decimal('weight', 5, 3);
            $table->decimal('cost', 5, 2);
            $table->string('item_skus')->nullable();
            $table->tinyInteger('invoiced')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubi_orders');
    }
}
