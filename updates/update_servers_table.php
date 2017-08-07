<?php namespace Awebsome\Serverpilot\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateServerTable extends Migration
{
    public function up()
    {
        Schema::table('awebsome_serverpilot_servers', function($table)
        {
            $table->boolean('deny_unknown_domains')->default(0)->after('firewall');
        });
    }

    public function down()
    {
        Schema::table('awebsome_serverpilot_servers', function($table)
        {
            $table->dropColumn('deny_unknown_domains');
        });
    }
}
