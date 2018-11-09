<?php namespace Awebsome\Serverpilot\Classes\Api;

use Log;
use ValidationException;
use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Models\Settings as CFG;
use Awebsome\Serverpilot\Classes\ServerPilotException;

class Curl
{
    /**
     * Get the credentials in the form of CLIENT_ID:API_KEY
     *
     * @return string $credentials
     */
    public function credentials()
    {
        $CLIENT_ID  = CFG::get('CLIENT_ID');
        $API_KEY    = CFG::get('API_KEY');

        return $CLIENT_ID.':'.$API_KEY;
    }

    /**
     * Core request method, used as the main communication layer between API and local code
     *
     * @param string $resource Used in constructing the API endpoint to hit
     * @param array $data The data to send in the request
     * @param string $method The HTTP method to use for the request
     * @return mixed $response
     */
    public function request($resource=null, $data=null, $method=ServerPilot::SP_HTTP_METHOD_GET) {

        $auth = $this->credentials();

        $url = ServerPilot::SP_API_ENDPOINT .'/'. $resource;

        $ch = curl_init();
        $options = [
            // general
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => ServerPilot::SP_TIMEOUT,
            CURLOPT_USERAGENT => ServerPilot::SP_USERAGENT,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_ENCODING => 'gzip',

            // ssl
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,

            // auth
            CURLOPT_USERPWD => $auth,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        ];

        // handle the data
        switch($method) {
            case ServerPilot::SP_HTTP_METHOD_GET:
                if (!empty($data)) {
                    $options[CURLOPT_URL] = $url . '?' . implode('&', $data);
                }
            break;
            case ServerPilot::SP_HTTP_METHOD_POST:
                if (empty($data)) {
                    throw new ServerPilotException('Curl::request() - parameter 2 is required for method ServerPilot::SP_HTTP_METHOD_POST');
                }

                $data = json_encode($data);

                $options[CURLOPT_CUSTOMREQUEST] = ServerPilot::SP_HTTP_METHOD_POST;
                $options[CURLOPT_POST] = TRUE;
                $options[CURLOPT_POSTFIELDS] = $data;

                $options[CURLOPT_HTTPHEADER] = [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data)
                ];
            break;
            case ServerPilot::SP_HTTP_METHOD_DELETE:
                $options[CURLOPT_CUSTOMREQUEST] = ServerPilot::SP_HTTP_METHOD_DELETE;
            break;
        }

        // set the options
        curl_setopt_array($ch, $options);

        // response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // check for common errors
        $error = false;
        switch ($status_code) {
            case 401:
            case 402:
            case 403:
            case 409:
                $error = trans('awebsome.serverpilot::lang.error.'.$status_code);
            break;

            case 400:
            case 404:
            case 500:
                $error = trans('awebsome.serverpilot::lang.error.'.$status_code, ['data' => json_encode($data, JSON_PRETTY_PRINT)]);
            break;

            default: break;
        }

        // close connection
        curl_close($ch);

        if ($status_code !== 200)
        {
            if ($method == ServerPilot::SP_HTTP_METHOD_POST || $method == ServerPilot::SP_HTTP_METHOD_DELETE) {
                if (CFG::get('log_errors')) {
                    Log::error($error);
                }

                throw new ValidationException(['error_mesage' => $error]);
            } else {
                return json_decode(json_encode(['error' => ['code' => $status_code, 'message' => $error]]));
            }
        } else {
            return json_decode($response);
        }
    }
}