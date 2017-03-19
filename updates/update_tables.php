<?php namespace Awebsome\Serverpilot\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateTables extends Migration
{
    public function up()
    {
        Schema::table('awebsome_serverpilot_apps', function($table)
        {
            $table->string('editor')->default('Atom')->after('domains');
            $table->longText('atom_config')->nullable()->after('editor');
            $table->longText('sublime_config')->nullable()->after('atom_config');

        });

        Schema::table('awebsome_serverpilot_system_users', function($table)
        {
            $table->text('password')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('awebsome_serverpilot_apps', function($table)
        {
            $table->dropColumn('editor');
            $table->dropColumn('atom_config');
            $table->dropColumn('sublime_config');
        });

        Schema::table('awebsome_serverpilot_system_users', function($table)
        {
            $table->dropColumn('password');
        });
    }
}
