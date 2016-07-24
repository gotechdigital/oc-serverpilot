<?php namespace Awebsome\Serverpilot\Classes;

use Awebsome\Serverpilot\Classes\ServerPilot;

class Curl
{
	/**
	 * core request function
	 *
	 * used as the main communication layer between API and local code
	 *
	 * @param string $method defines the method for the request
	 *
	 * @return void
	 */
	public function request($auth=null, $path=null, $data=null, $method=ServerPilot::SP_HTTP_METHOD_GET) {

		$url = ServerPilot::SP_API_ENDPOINT .'/'. $path;
		
		$ch = curl_init();
		$options = array(
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
		);
		
		// handle the data
		switch($method) {
			case ServerPilot::SP_HTTP_METHOD_GET:
				if($data !== null && !empty($data)) $options[CURLOPT_URL] = $url . '?' . implode('&', $data);
			break;
			case ServerPilot::SP_HTTP_METHOD_POST: 
				if($data === null || empty($data)) throw new Exception('Curl::request() - parameter 2 is required for method ServerPilot::SP_HTTP_METHOD_POST');
				
				$data = json_encode($data);
				
				$options[CURLOPT_CUSTOMREQUEST] = ServerPilot::SP_HTTP_METHOD_POST;
				$options[CURLOPT_POST] = TRUE;
				$options[CURLOPT_POSTFIELDS] = $data;
				
				$options[CURLOPT_HTTPHEADER] = array(                                                                          
				    'Content-Type: application/json',                                                                                
				    'Content-Length: ' . strlen($data)                                                                      
				);
			break;
			#case ServerPilot::SP_HTTP_METHOD_DELETE: 
			#	$options[CURLOPT_CUSTOMREQUEST] = ServerPilot::SP_HTTP_METHOD_DELETE;
			#break;
		}
		
		// set the options
		curl_setopt_array($ch, $options);
		
		// response
        $response = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		// check for common errors
		switch ($status_code) {
			case 200: break;
			case 400: throw new Exception('We could not understand your request. Typically missing a parameter or header.'); break;
			case 401: throw new Exception('Either no authentication credentials were provided or they are invalid.'); break;
			case 402: throw new Exception('Method is restricted to users on the Coach or Business plan.'); break;
			case 403: throw new Exception('Typically when trying to alter or delete protected resources.'); break;
			case 404: throw new Exception('You requested a resource that does not exist.'); break;
			case 409: throw new Exception('Typically when trying creating a resource that already exists.'); break;
			case 500: throw new Exception('Internal server error. Try again at a later time.'); break;
			default:  break;
		}
	
		// close connection	
        curl_close($ch);
        
        if(empty($response)) {
			throw new Exception('Empty Response');
        }
        
        // if we get here, assume we have a JSON string - decode
        if($response = json_decode($response)) {
	        // check for any SP specific errors
	        if(!empty($response->error)) {
		        throw new Exception($response->error);
	        }
        }
        
        return $response;
	}
}

/**
 * ServerPilot Exceptions
 */
class Exception extends \Exception {
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        $message = '[ServerPilot]: ' . $message;
    
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}