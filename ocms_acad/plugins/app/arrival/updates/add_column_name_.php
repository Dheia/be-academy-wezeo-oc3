<?php namespace App\Arrival\Updates;

use Backend\Classes\Controller;
use Schema;
use October\Rain\Database\Updates\Migration;

class AddColumnName extends Migration
{
    public function up()
    {
		Schema::table('app_arrival_', function ($table) {
			$table->string('name');
		});

    }
    
    public function down()
    {
        Schema::table('app_arrival_', function ($table) {
			$table->dropColumn('name');
		});

    }
}
