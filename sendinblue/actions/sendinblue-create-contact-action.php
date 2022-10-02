<?php

use Uncanny_Automator\Recipe;

/**
 * Class Sendinblue_Create_Contact_Action
 */
class Sendinblue_Create_Contact_Action {
	use Recipe\Actions;

	/**
	 * Sendinblue_Create_Contact_Action constructor.
	 */
	public function __construct() {		
		$this->setup_action();
	}

	public function complete_with_error( $user_id, $action_data, $recipe_id, $error_msg ) {
		$action_data['do-nothing']           = true;
		$action_data['complete_with_errors'] = true;
		Automator()->complete_action( $user_id, $action_data, $recipe_id, $error_msg );
	}

	/**
	 *
	 */

	protected function setup_action() {

		$this->set_integration( 'SENDINBLUE' );
		$this->set_action_code( 'SB_CREATE_CONTACT' );
		$this->set_requires_user( false );
		$this->set_action_meta( 'SB_ATTR_FIRSTNAME' );
		/* translators: Action - WordPress */
		$this->set_sentence( sprintf( esc_attr__( 'Create a contact from {{contact:%1$s}}', 'automator-sendinblue-integration' ), $this->get_action_meta() ) );
		/* translators: Action - WordPress */
		$this->set_readable_sentence( esc_attr__( 'Create a {{contact}}', 'automator-sendinblue-integration' ) );
		$options_group = array(
			$this->get_action_meta() => array(
				/* translators: First name field */
				Automator()->helpers->recipe->field->text(
					array(
						'option_code' => $this->get_action_meta(),
						'label'       => esc_attr__( 'First name', 'automator-sendinblue-integration' ),
						'input_type'  => 'text',
					)
				),
				/* translators: Email field */
				Automator()->helpers->recipe->field->text(
					array(
						'option_code' => 'SB_EMAIL',
						'label'       => 'Email',
						'input_type'  => 'email',
					)
				),
				/* translators: List ID field */
				Automator()->helpers->recipe->field->int(
					array(
						'option_code' => 'SB_LIST_ID',
						'label'       => 'List ID',
						'input_type'  => 'int',
						'description' => 'The list ID to add this contact to'
					)
				),
			),
		);

		$this->set_options_group( $options_group );

		$this->register_action();
	}

	/**
	 * @param int $user_id
	 * @param array $action_data
	 * @param int $recipe_id
	 * @param array $args
	 * @param $parsed
	 */
	protected function process_action( $user_id, $action_data, $recipe_id, $args, $parsed ) {
		$name  = isset( $parsed[ $this->get_action_meta() ] ) ? $parsed[ $this->get_action_meta() ] : '';
		$email = isset( $parsed[ 'SB_EMAIL' ] ) ? $parsed[ 'SB_EMAIL' ] : '';
		$list  = isset( $parsed[ 'SB_LIST_ID' ] ) ? $parsed[ 'SB_LIST_ID' ] : 0;

		if ( empty($name) || empty($email) || empty($list) ) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Got incomplete info from automator' );
			return;
		}
		
		$url = 'https://api.sendinblue.com/v3/contacts';

		$body = [
			'attributes' => array( 'FIRSTNAME' => sanitize_text_field($name) ),
			'email' => sanitize_email($email),
			'listIds' => array( intval(sanitize_text_field($list)) ),
		];

		$json = wp_json_encode( $body );
		if ( $body == false ) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Error encondig json' );
			return;
		}

		$args = [
			'body'        => $json,
			'headers'     => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'api-key'      =>  get_option( 'ua_sendinblue_integration_api_key', '' ),
			],
		];
		
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error($response) ) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Error while making API request' );
			return;
		}				

		if ( wp_remote_retrieve_response_code( $response ) != 201 ) {
			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
			$this->complete_with_error( $user_id, $action_data, $recipe_id, $response_body['message'] );
			return;
		}

        // complete this action successfully
        Automator()->complete->action( $user_id, $action_data, $recipe_id );
	}
}
