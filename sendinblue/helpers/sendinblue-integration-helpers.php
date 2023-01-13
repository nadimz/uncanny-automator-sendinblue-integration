<?php 

require_once(__DIR__ . '/../../vendor/autoload.php');

/**
 * Class Sendinblue_Integration
 */
class Sendinblue_Integration_Helpers {
	/*
	 * Sendinblue API implementation
	 */
	public $api;

	/**
	 * Sendinblue_Integration_Helpers constructor.
	 */
	public function __construct() {
		// Load settings
		$this->load_settings();
		
		$config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', get_option( 'ua_sendinblue_integration_api_key', '' ) );
		$this->api = new SendinBlue\Client\Api\ContactsApi(
			new GuzzleHttp\Client(),
			$config
		);
	}

	/**
	 * Load the settings
	 * 
	 * @return void
	 */
	private function load_settings() {
		include_once __DIR__ . '/../settings/settings-sendinblue.php';
		new Sendinblue_Integration_Settings();
	}

	private function get_verified_arg($arg) {
		return isset( $arg ) ? $arg :  '';
	}

	public function get_sanitized_email($arg) {
		return sanitize_email($this->get_verified_arg( $arg ) );
	}

	public function get_sanitized_text($arg) {
		return sanitize_text_field($this->get_verified_arg( $arg ) );
	}

	/**
	 * Sendinblue API
	 */

	public function create_or_update_contact($email, $first_name, $last_name, $list_ids = []) {
		if (empty($email)) {
			throw new Exception('email must be passed to create_or_update_contact function');
		}

		$createContact = new \SendinBlue\Client\Model\CreateContact();
		$createContact['email'] = $email;
		
		if ( !empty( $list_ids ) ) {		
			$createContact['listIds'] = $list_ids;
		}

		$attributes = [];
        if ( !empty($first_name) ) {
            $attributes = array_merge( $attributes, [ 'FIRSTNAME' => $first_name ] );
        }

        if ( !empty($last_name) ) {
            $attributes = array_merge( $attributes, [ 'LASTNAME' => $last_name ] );
        }

        if  ( !empty( $attributes ) ) {
            $createContact['attributes'] = $attributes;
        }

		$createContact['updateEnabled'] = true;
				
		return $this->api->createContact($createContact);		
	}

	public function get_lists_in_folder($folder_id) {
		return $this->api->getFolderLists($folder_id, 50, 0);
	}

	public function create_list($list_name, $folder_id) {
		$createList = new \SendinBlue\Client\Model\CreateList();
		$createList['name'] = $list_name;
		$createList['folderId'] = $folder_id;

		return $this->api->createList($createList);		
	}
}
