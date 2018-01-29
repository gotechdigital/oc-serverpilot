<?php namespace Awebsome\Serverpilot\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTables extends Migration
{

    public function up()
    {
        // delete deprecated tables & recreate new structure.
        Schema::dropIfExists('awebsome_serverpilot_servers');
        Schema::dropIfExists('awebsome_serverpilot_system_users');
        Schema::dropIfExists('awebsome_serverpilot_sysusers');
        Schema::dropIfExists('awebsome_serverpilot_apps');
        Schema::dropIfExists('awebsome_serverpilot_databases');
        Schema::dropIfExists('awebsome_serverpilot_scheduling');
        Schema::dropIfExists('awebsome_serverpilot_actions');

        /**
         * Servers
         */
        Schema::create('awebsome_serverpilot_servers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');                              #id
            $table->string('account_id')->nullable();              # serverpilot owner account by multiaccount feature.

            # API Cols.
            $table->string('api_id')->index();
            $table->string('name');                                 #hostname,
            $table->boolean('autoupdates')->default(0);             #is enabled,
            $table->boolean('firewall')->default(0);                #is enabled,
            $table->boolean('deny_unknown_domains')->default(0);    #is enabled,
            $table->string('lastaddress');                          #ip,
            $table->string('datecreated');                          #created_at,
            $table->string('lastconn');                             #Last_conecction

            $table->timestamps();
        });


        /**
         * SystemUsers
         */
        Schema::create('awebsome_serverpilot_sysusers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            # API Cols.
            $table->string('api_id')->index();
            $table->string('server_api_id');
            $table->string('name');

            //Custom Cols.
            $table->text('password')->nullable();

            $table->timestamps();
        });


        /**
         * Apps
         */
        Schema::create('awebsome_serverpilot_apps', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            # API Cols.
            $table->string('api_id')->index();          # id.
            $table->string('server_api_id');
            $table->string('sysuser_api_id');

            $table->string('name');
            $table->string('runtime');
            $table->longText('ssl')->nullable();
            $table->text('autossl')->nullable();
            $table->longText('domains')->nullable();
            $table->string('datecreated');

            # Custom cols.
            $table->text('annotations')->nullable();

            $table->timestamps();
        });

        /**
         * Databases
         */
        Schema::create('awebsome_serverpilot_databases', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            # API Cols.
            $table->string('api_id')->index();
            $table->string('app_api_id');
            $table->string('server_api_id');
            $table->string('name');
            $table->text('user');

            # Custom Cols.
            $table->text('password')->nullable();

            $table->timestamps();
        });


        /**
         * Actions
         */
        Schema::create('awebsome_serverpilot_actions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            # API Cols.
            $table->string('api_id')->index();
            $table->string('server_api_id');
            $table->string('status');
            $table->string('datecreated');

            $table->timestamps();
        });
    }

    /**
     * Drop Tables
     */
    public function down()
    {
        Schema::dropIfExists('awebsome_serverpilot_servers');
        Schema::dropIfExists('awebsome_serverpilot_system_users');
        Schema::dropIfExists('awebsome_serverpilot_sysusers');
        Schema::dropIfExists('awebsome_serverpilot_apps');
        Schema::dropIfExists('awebsome_serverpilot_databases');
        Schema::dropIfExists('awebsome_serverpilot_scheduling');
        Schema::dropIfExists('awebsome_serverpilot_actions');
    }
}
