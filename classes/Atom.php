<?php namespace Awebsome\Serverpilot\Classes;

/**
 * Atom Editor
 */
class Atom
{
    public static function config($data = null)
    {
        $remote_path = "/srv/users/{user}/apps/{app}/public";
        $config = [
            "protocol" => "sftp",
            "host" => "",
            "port" => 22,
            "user" => "",
            "pass" => "",
            "promptForPass" => false,
            "remote" => $remote_path,
            "agent" => "",
            "privatekey" => "",
            "passphrase" => "",
            "hosthash" => "",
            "ignorehost" => true,
            "connTimeout" => 30000,
            "keepalive" => 10000,
            "keyboardInteractive" => false,
            "watch" => [],
            "watchTimeout" => 500
        ];

        if($data){
            foreach ($data as $key => $value) {
                if(array_key_exists($key, $config))
                    $config[$key] = $value;
            }

            $config['remote'] = str_replace(['{user}', '{app}'],[$data['user'], $data['app']], $remote_path);
        }

        return $config;
    }
}
