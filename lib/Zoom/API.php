<?php
namespace Zoom;

/**
 * Class API
 *
 * @package Zoom
 */
class API
{
    static $instance;

    private $api_key;
    private $api_secret;
    private $api_url;
    private $api_version;

    /**
     * Build a ZoomAPI object prepare the API key, secret and URL.
     * The function is private as this is a singleton class.
     *
     * @return API
     */
    private function __construct()
    {
        $this->api_key     = ZOOM_API_KEY;
        $this->api_secret  = ZOOM_API_SECRET;
        $this->api_version = 'v1';
        $this->api_url     = 'https://api.zoom.us/' . $this->api_version . '/';
    }

    /**
     * Return a new instance of the ZoomAPI object if one doesn't already exist, otherwise
     * return the existing instance.
     *
     * @return mixed
     */
    public static function getInstance()
    {
        if ( empty( self::$instance ) ) {
            self::instantiate();
        }

        return self::$instance;
    }

    /**
     * Create new instance of the ZoomAPI object.
     *
     * @return void
     */
    public static function instantiate()
    {
        self::$instance = new API();
    }

    /**
     * Send a request to Zoom.us
     *
     * @param string $action  - The Zoom API call to invoke
     * @param array  $request - The request data in a key-value array to pass to the API call
     *
     * @return bool|string
     */
    public function sendRequest( $action, $request )
    {
        $endpoint = $this->api_url . $action;

        $request['api_key']    = $this->api_key;
        $request['api_secret'] = $this->api_secret;
        $request['data_type']  = 'JSON';

        $params = http_build_query( $request );

        $curl = curl_init();

        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curl, CURLOPT_URL, $endpoint );
        curl_setopt( $curl, CURLOPT_POST, 1 );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

        $response = curl_exec( $curl );

        curl_close( $curl );

        if ( !$response ) {
            return false;
        }

        return $response;
    }
}