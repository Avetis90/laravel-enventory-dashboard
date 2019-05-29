<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserRole extends Migration {
    public function up() {
        Schema::table('users', function(Blueprint $table) {
           $table->enum('role', ['admin', 'manager']);
        });
    }

    public function down() {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
