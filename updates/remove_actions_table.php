<?php namespace Awebsome\Serverpilot\Updates;

use Db;
use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class RemoveActionsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('awebsome_serverpilot_actions');
    }

    public function down()
    {
        Schema::create('awebsome_serverpilot_actions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            # API Cols.
            $table->string('api_id')->index()->nullable();
            $table->string('server_api_id')->index()->nullable();
            $table->string('status');
            $table->string('datecreated');

            $table->timestamps();
        });
    }
}
