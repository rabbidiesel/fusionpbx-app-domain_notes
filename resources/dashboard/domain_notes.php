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
 * Portions created by the Initial Developer are Copyright (C) 2008-2024
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 * Mark J Crane <markjcrane@fusionpbx.com>
 * Tim Fry <tim@fusionpbx.com>
 */

//includes files
require_once dirname(__DIR__, 4) . "/resources/require.php";
require_once dirname(__DIR__, 4) . "/resources/check_auth.php";

//set defaults
$domain_uuid = $_SESSION['domain_uuid'] ?? '';
$user_uuid = $_SESSION['user_uuid'] ?? '';

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
$has_domain_note_edit = permission_exists('domain_note_edit');
$has_domain_note_view = permission_exists('domain_note_view');
$has_domain_note_edit = true;
$has_domain_note_view = true;

//get the note
$domain_note = $database->select('select domain_note_text from v_domain_notes where domain_uuid = :uuid', ['uuid' => $domain_uuid], 'column');
if ($domain_note === false) {
	$domain_note = "";
	$domain_note_empty = true;
}

//add multi-lingual support
$language = new text;
$text = $language->get($settings->get('domain', 'language', 'en-us'), 'core/user_settings');

//set the rows to alternate shading background
$c = 0;
$row_style = [];
$row_style[$c] = "row_style0";
$row_style[!$c] = "row_style1";

echo "<div class='hud_box' style='cursor: default;'>\n";
echo "	<form id='form_note' name='form_note'>\n";
echo "		<div class='panel panel-default'>\n";
echo "			<span class='hud_title'>$dashboard_name</span>\n";
echo "			<div class='panel-body' style='padding-left: 5px; padding-right: 5px; padding-bottom: 5px;'>\n";
echo "				<textarea name='domain_note' id='domain_note' class='form-control' rows='5' style='resize: none; border: none;'>$domain_note</textarea>\n";
echo "			</div>\n";
echo "			<div id='note_status_bar' class='panel-footer' style='position: relative;'>\n";
echo "			    <span id='note_status' style='float:left; display:inline-block; line-height: 36px;'></span>\n";
if ($has_domain_note_edit) {
    echo "			<span style='float:right;'>\n";
    echo "				<button type='submit' class='btn btn-primary' value='Save'>\n";
    echo "					<i class='fas fa-save'></i> Save\n";
    echo "				</button>\n";
    echo "				<input type='hidden' name='" . $token['name'] . "' value='" . $token['hash'] . "'>\n";
    echo "				<input type='hidden' name='domain_uuid' value='$domain_uuid'>\n";
    echo "			</span>\n";
}
echo "				<div style='clear:both;'></div>\n";
echo "			</div>\n";
echo "		</div>\n";
echo "<script>\n";
echo "	document.getElementById('form_note').addEventListener('submit', function (event) {\n";
echo "		event.preventDefault();\n";
echo "		var form = document.getElementById('form_note');\n";
echo "		var formData = new FormData(form);\n";
echo "		fetch('/app/domain_notes/resources/domain_note_edit.php', {\n";
echo "			method: 'POST',\n";
echo "			body: new URLSearchParams(formData)\n";
echo "		})\n";
echo "				.then(response => response.json())\n";
echo "				.then(data => {\n";
echo "					// Set the status and note\n";
echo "					var status = data.status;\n";
echo "					var note = data.note;\n";
echo "					// Get the note status element\n";
echo "					var statusElem = document.getElementById('note_status');\n";
echo "					statusElem.textContent = status;\n";
echo "					statusElem.style.display = 'block';\n";
echo "					statusElem.style.opacity = '1'; // Ensure it starts fully visible\n";
echo "					statusElem.style.transition = 'opacity 1s ease';\n";
echo "					// Fade out after 2 seconds\n";
echo "					setTimeout(() => {\n";
echo "						statusElem.style.opacity = '0';\n";
echo "					}, 2000);\n";
echo "					// Optionally, after fade-out complete, hide the element and reset its opacity for future saves\n";
echo "					setTimeout(() => {\n";
echo "						statusElem.style.display = 'none';\n";
echo "						statusElem.style.opacity = '1';  // Reset to fully visible for next time\n";
echo "					}, 3000);\n";
echo "					// Update the textarea value with the new data (instead of innerHTML)\n";
echo "					document.getElementById('note').value = note;\n";
echo "				})\n";
echo "				.catch(error => console.error('Error:', error));\n";
echo "	});\n";
echo "</script>\n";
echo "</form>\n";
echo "</div>\n";
