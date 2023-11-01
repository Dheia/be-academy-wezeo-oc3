<?php namespace App\Arrival\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class TableCreateAppArrival extends Migration
{
    public function up()
    {
        Schema::create('app_arrival_', function($table)
        {
            $table->increments('id');
            $table->text('date');
			$table->text('time');
			$table->text('message')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('app_arrival_');
    }
}
