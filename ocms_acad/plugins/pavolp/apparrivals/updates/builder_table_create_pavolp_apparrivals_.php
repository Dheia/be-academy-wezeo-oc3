<?php namespace PavolP\AppArrivals\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePavolpApparrivals extends Migration
{
    public function up()
    {
        Schema::create('pavolp_apparrivals_', function($table)
        {
            $table->string('name');
            $table->text('message')->nullable();
            $table->dateTime('date_time');
            $table->boolean('is_late');
            $table->primary(['name']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('pavolp_apparrivals_');
    }
}
