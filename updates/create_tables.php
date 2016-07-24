<?php namespace Awebsome\Serverpilot\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTables extends Migration
{

    public function up()
    {

    /*       SERVERPILOT       
    * ====================== 
    */

        /**
         * Scheluding
         */
        Schema::create('awebsome_serverpilot_scheduling', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('schedule');
            $table->mediumText('log');
            $table->timestamps();
        });

        /**
         * Servers
         */
        Schema::create('awebsome_serverpilot_servers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('server_id');                      #id,

            $table->string('name');                                 #hostname,
            $table->boolean('autoupdates')->default(0);              #is enabled,
            $table->boolean('firewall')->default(0);                #is enabled,
            $table->string('lastaddress');                          #ip,
            $table->string('datecreated');                          #created_at,
            $table->string('lastconn');                             #Last_conecction                                           
            
            $table->timestamps();
        });


        /**
         * SystemUsers
         */
        Schema::create('awebsome_serverpilot_system_users', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('user_id');                  
            $table->string('server_id');                

            $table->string('name');                     
            
            $table->timestamps();
        });


        /**
         * Apps
         */
        Schema::create('awebsome_serverpilot_apps', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('app_id');
            $table->string('user_id');
            $table->string('server_id');

            $table->string('name');                 
            $table->string('runtime');                 
            $table->longText('ssl')->nullable();                 
            $table->longText('domains')->nullable();                 
            
            $table->timestamps();
        });

        /**
         * Databases
         */
        Schema::create('awebsome_serverpilot_databases', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('db_id');
            $table->string('app_id');
            $table->string('server_id');

            $table->string('user');            
            
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
        Schema::dropIfExists('awebsome_serverpilot_apps');
        Schema::dropIfExists('awebsome_serverpilot_databases');
        Schema::dropIfExists('awebsome_serverpilot_scheduling');
    }
}