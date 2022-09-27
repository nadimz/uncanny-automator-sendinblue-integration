<?php 

/**
 * Class SendinBlue_Integration
 */
class SendinBlue_Integration_Helpers {
	/**
	 * SendinBlue_Integration_Helpers constructor.
	 */
	public function __construct() {
		// Load settings
		$this->load_settings();
	}

	/**
	 * Load the settings
	 * 
	 * @return void
	 */
	private function load_settings() {
		include_once __DIR__ . '/../settings/settings-sample.php';
		new SendinBlue_Integration_Settings();
	}
}

new SendinBlue_Integration_Helpers();
