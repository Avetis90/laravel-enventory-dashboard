<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('sets', function(Blueprint $table) {
            $table->string('prefix')->nullable()->after('title');
            $table->string('battery_packing')->nullable()->after('service_options');
            $table->string('battery_type')->nullable()->after('battery_packing');

            $table->text('description')->nullable()->after('battery_type');
            $table->text('description_cn')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('sets', function($table) {
			$table->dropColumn('prefix');
			$table->dropColumn('battery_packing');
			$table->dropColumn('battery_type');
			$table->dropColumn('description');
			$table->dropColumn('description_cn');
		});
    }
}
