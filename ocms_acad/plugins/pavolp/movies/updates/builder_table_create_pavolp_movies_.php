<?php namespace PavolP\Movies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePavolpMovies extends Migration
{
    public function up()
    {
        Schema::create('pavolp_movies_', function($table)
        {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('year')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('pavolp_movies_');
    }
}
