<?php

use Uncanny_Automator\Recipe;

/**
 * Class Sendinblue_Add_User_To_Waiting_List
 */
class Sendinblue_Add_User_To_Waiting_List_Action {
	use Recipe\Actions;

	/**
	 * Sendinblue_Add_User_To_Waiting_Action constructor.
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
		$this->set_action_code( 'SB_ADD_USER_TO_WAITING_LIST' );
		$this->set_requires_user( false );
		$this->set_action_meta( 'SB_EMAIL' );
		/* translators: Action - WordPress */
		$this->set_sentence( sprintf( esc_attr__( 'Add {{contact:%1$s}} to a waiting list', 'automator-sendinblue-integration' ), $this->get_action_meta() ) );
		/* translators: Action - WordPress */
		$this->set_readable_sentence( esc_attr__( 'Add a {{contact}} to a waiting list', 'automator-sendinblue-integration' ) );
		$options_group = array(
			$this->get_action_meta() => array(
				/* translators: Email field */
				Automator()->helpers->recipe->field->text(
					array(
						'option_code' => $this->get_action_meta(),
						'label'       => 'Email',
						'input_type'  => 'email',
						'required'    => true,
					)
				),
				/* translators: First name field */
				Automator()->helpers->recipe->field->text(
					array(
						'option_code' => 'SB_FIRSTNAME',
						'label'       => esc_attr__( 'First name', 'automator-sendinblue-integration' ),
						'input_type'  => 'text',
						'required'    => false,
					)
				),
				/* translators: Last name field */
				Automator()->helpers->recipe->field->text(
					array(
						'option_code' => 'SB_LASTNAME',
						'label'       => esc_attr__( 'Last name', 'automator-sendinblue-integration' ),
						'input_type'  => 'text',
						'required'    => false,
					)
				),
				/* translators: Folder ID field */
				Automator()->helpers->recipe->field->int(
					array(
						'option_code' => 'SB_FOLDER_ID',
						'label'       => 'Folder ID',
						'input_type'  => 'int',
						'description' => 'The folder ID where waiting list should be created',
						'required'    => true,
					)
				),
				/* translators: List name field */
				Automator()->helpers->recipe->field->text(
					array(
						'option_code' => 'SB_LIST_NAME',
						'label'       => esc_attr__( 'List name', 'automator-sendinblue-integration' ),
						'input_type'  => 'text',
						'required'    => true,
					)
				)
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
		$helpers = Automator()->helpers->recipe->sendinblue;
		
		$email      = $helpers->get_sanitized_email( $parsed[ $this->get_action_meta() ]);
		$first_name = $helpers->get_sanitized_text( $parsed[ 'SB_FIRSTNAME' ] );
		$last_name  = $helpers->get_sanitized_text( $parsed[ 'SB_LASTNAME' ] );
		$folder_id  = $helpers->get_sanitized_text( $parsed[ 'SB_FOLDER_ID' ] );
		$list_name  = $helpers->get_sanitized_text( $parsed[ 'SB_LIST_NAME' ] );

		if ( empty($email) ) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Got empty email from automator' );
			return;
		}

		if ( empty($folder_id) ) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Got empty folder from automator' );
			return;
		}

		if ( empty($list_name) ) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Got empty list_name from automator' );
			return;
		}

		/**
		 * Check if the waiting list already exists
		 */
		try {
			$response = $helpers->get_lists_in_folder(intval($folder_id));
		} catch (Exception $e) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Exception when calling get_lists_in_folder: ' . $e->getMessage() );
			return;
		}

		$list_id = NULL;
		if (!empty($response['lists'])) {
			foreach ($response['lists'] as $list) {
				if ($list['name'] == $list_name) {
					$list_id = $list['id'];
				}
			}
		}		

		// If waiting list was not found, create it
		if ($list_id == NULL) {			
			try {
				$response = $helpers->create_list($list_name, intval($folder_id));
				$list_id = $response['id'];
			} catch (Exception $e) {
				$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Exception when calling create_list: ' . $e->getMessage() );
				return;
			}
		}

		// Create or update contact and add to the waiting list
		$list_ids = [ $list_id ];
		try {
			$helpers->create_or_update_contact($email, $first_name, $last_name, $list_ids);			
		} catch (Exception $e) {
			$this->complete_with_error( $user_id, $action_data, $recipe_id, 'Exception when calling create_or_update_contact: ' . $e->getMessage() );
			return;
		}

		Automator()->complete->action( $user_id, $action_data, $recipe_id );
	}
}
