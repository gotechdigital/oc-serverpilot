<?php namespace Awebsome\Serverpilot\Updates;

use Db;
use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateRuntimesTable extends Migration
{
    public function up()
    {
        Schema::create('awebsome_serverpilot_runtimes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('version');
            $table->string('label');
            $table->boolean('recommended')->default(1);
            $table->timestamps();
        });

        $runtimes = [
            ['version' => 'php5.4', 'label' => 'PHP 5.4', 'recommended' => 0],
            ['version' => 'php5.5', 'label' => 'PHP 5.5', 'recommended' => 0],
            ['version' => 'php5.6', 'label' => 'PHP 5.6', 'recommended' => 0],
            ['version' => 'php7.0', 'label' => 'PHP 7.0', 'recommended' => 1],
            ['version' => 'php7.1', 'label' => 'PHP 7.1', 'recommended' => 1],
            ['version' => 'php7.2', 'label' => 'PHP 7.2', 'recommended' => 1]
        ];

        Db::table('awebsome_serverpilot_runtimes')->insert($runtimes);
    }

    public function down()
    {
        Schema::dropIfExists('awebsome_serverpilot_runtimes');
    }
}
