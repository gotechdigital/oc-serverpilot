<?php namespace Awebsome\Serverpilot\Classes;

/**
 * Sublime Editor
 */
class Sublime {

    public static function config($data = null)
    {
        $remote_path = "/srv/users/{user}/apps/{app}/public";

        $config = [
            "type" => "sftp",
            "host" => "",
            "user" => "",
            "port" => 22,
            "password" => "",
            "remote_path" => $remote_path,
            "connect_timeout" => 30,
            "keepalive" => 120,
            "save_before_upload" => true,
            "upload_on_save" => true,
            "sync_down_on_open" => false,
            "sync_skip_deletes" => false,
            "sync_same_age" => true,
            "confirm_downloads" => false,
            "confirm_sync" => false,
            "confirm_overwrite_newer" => false
        ];

        if($data){
            foreach ($data as $key => $value) {
                if(array_key_exists($key, $config))
                    $config[$key] = $value;
            }

            $config['remote_path'] = str_replace(['{user}', '{app}'],[$data['user'], $data['app']], $remote_path);
        }

        return $config;
    }
}
