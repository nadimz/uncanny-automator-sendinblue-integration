<?php 

/**
 * Class Sendinblue_API
 */
class Sendinblue_API {
    /**
     * Sendinblue API V3 base URL
     */
    private $base_url = 'https://api.sendinblue.com/v3';

    private $endpoints = [
        'contacts' => '/contacts',
    ];

    /**
     * API Key
     */
    private $api_key;
    
	/**
	 * Sendinblue_API constructor.
	 */
	public function __construct() {
		$this->api_key = get_option( 'ua_sendinblue_integration_api_key', '' );
	}

    private function api_call_completed() {
        return [
            'success' => true,
            'message' => ''
        ];
    }

    private function api_call_completed_with_error( $code, $message ) {
        return [
            'success' => false,
            'code'    => $code,
            'message' => $message
        ];
    }

    private function make_api_call( $body, $endpoint ) {
        $json = wp_json_encode( $body );
		if ( $body == false ) {
            return $this->api_call_completed_with_error( 'internal_error', 'Error while calling wp_json_encode' );
		}

		$args = [
			'body'        => $json,
			'headers'     => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'api-key'      =>  $this->api_key,
			],
		];
		
        $url = $this->base_url . $this->endpoints[$endpoint];

		$response = wp_remote_post( $url, $args );
		if ( is_wp_error($response) ) {
            return $this->api_call_completed_with_error( 'internal_error', 'Error while calling wp_remote_post' );
		}				

		if ( wp_remote_retrieve_response_code( $response ) == 400 ) {
			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
			return $this->api_call_completed_with_error( $response_body['code'], $response_body['message'] );
		}

        return $this->api_call_completed();
    }

	public function create_contact( $email, $first_name = '', $last_name = '', $list_ids = array() ) {
        /**
         * Email. The only madatory field
         */
        $body = [ 'email' => $email ];

        /**
         * Attributes
         */
        $attributes = [];
        if ( !empty($first_name) ) {
            $attributes = array_merge( $attributes, [ 'FIRSTNAME' => $first_name ] );
        }

        if ( !empty($last_name) ) {
            $attributes = array_merge( $attributes, [ 'LASTNAME' => $last_name ] );
        }

        if  ( !empty( $attributes ) ) {
            $body = array_merge( $body, [ 'attributes' => $attributes ] );
        }

        /**
         * List IDs
         */
        if ( !empty( $list_ids ) ) {
            $body = array_merge( $body, [ 'listIds' => $list_ids ] );
        }

        return $this->make_api_call( $body, 'contacts' );		
    }
}
