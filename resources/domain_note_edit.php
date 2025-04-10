<?php

/*
 * FusionPBX
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is FusionPBX
 *
 * The Initial Developer of the Original Code is
 * Mark J Crane <markjcrane@fusionpbx.com>
 * Portions created by the Initial Developer are Copyright (C) 2008-2025
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 * Mark J Crane <markjcrane@fusionpbx.com>
 * Tim Fry <tim@fusionpbx.com>
 */

	//includes files
	require_once dirname(__DIR__, 3) . "/resources/require.php";
	require_once dirname(__DIR__, 3) . "/resources/check_auth.php";

	//create the text object
	$text = (new text())->get();

	//set defaults
	$domain_uuid = $_SESSION['domain_uuid'] ?? '';
	$user_uuid = $_SESSION['user_uuid'] ?? '';
	$note = $_REQUEST['domain_note'] ?? '';
	$response = ['status' => $text['label-note_update_failed'] ?? 'Note Update Failed', 'note' => $note];

	//connect to the database
	if (empty($database) || !($database instanceof database)) {
		$database = database::new();
	}

	//set up settings object
	if (empty($settings) || !($settings instanceof settings)) {
		$settings = new settings(['database' => $database, $domain_uuid, $user_uuid]);
	}

	//create a token
	$token = (new token())->create($_SERVER['PHP_SELF']);

	//check permisions
	$has_domain_note_add = permission_exists('domain_note_add');
	$has_domain_note_edit = permission_exists('domain_note_edit');
	$has_domain_note_view = permission_exists('domain_note_view');
	$has_domain_note_add = true;
	$has_domain_note_edit = true;
	$has_domain_note_view = true;

	//check for user submitted note
	if ($has_domain_note_edit) {
		//check for existing note
		$note_record = $database->select('select domain_note_uuid, domain_note_text from v_domain_notes where domain_uuid=:domain_uuid limit 1;', ['domain_uuid' => $domain_uuid], 'row');
		if ($note_record === false) {
			$note_record['domain_note_uuid'] = uuid();
		}

		//record the new note for the domain
		$note_record['domain_uuid'] = $domain_uuid;
		$note_record['domain_note_text'] = $note;

		//set up the record for database
		$table['domain_notes'][] = $note_record;

		//create temporary permissions to add
		$permissions = new permissions($database, $domain_uuid, $user_uuid);
		$permissions->add('domain_note_add', 'temp');

		//set the app name for the transaction
		$database->app_name = 'domain_notes';
		$database->app_uuid = '25351d47-2ca6-405b-b989-1b8f2f899dff';

		//save the record in the table
		$database->save($table);

		//get the status code as a string
		$status_code = "" . $database->message['code'];

		//check the database status code for success or failure and set to language specific text
		if ($status_code === "200") {
			//success
			$status = $text['label-note_updated'] ?? 'Note Updated';
		} else {
			//failure
			$status = $text['label-note_update_failed'] ?? 'Note Update Failed';
		}

		//remove the temporary permission
		$permissions->delete('domain_note_add', 'temp');

		//set response to language status
		$response = ["status" => $status, "note" => $note];
	}

	//send the response back as JSON text
	echo json_encode($response);

	//should not continue to process any more PHP
	exit();
