<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefaultOptionsParserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('converters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('converter_type', [
                'elife',
                'ubiCa',
                'ubiAu',
                'ubiEu',
                'ubiNz',
                'ubiUs',
                'owEuDirectLine'
            ]);
            $table->string('prefix')->nullable();
            $table->string('service_options')->nullable();
            $table->string('battery_packing')->nullable();
            $table->string('battery_type')->nullable();

            $table->text('description')->nullable();
            $table->text('description_cn')->nullable();

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
        Schema::drop('converters');
    }
}
