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

echo "<style>\n";
echo "	#domain_note:focus {\n";
echo "		outline: none !important;\n";
echo "		border: none !important;\n";
echo "		box-shadow: none !important;\n";
echo "	}\n";
echo "	#domain_note {\n";
echo "		overflow: hidden;\n";
echo "		min-height: 38px;\n";
echo "		transition: height 0.2s ease;\n";
echo "	}\n";
echo "	#domain_note.collapsed {\n";
echo "		height: 38px !important;\n";
echo "		overflow: hidden;\n";
echo "	}\n";
echo "	.note-container {\n";
echo "		position: relative;\n";
echo "	}\n";
echo "	.note-container.has-overflow::after {\n";
echo "		content: '';\n";
echo "		position: absolute;\n";
echo "		bottom: 0;\n";
echo "		left: 0;\n";
echo "		right: 0;\n";
echo "		height: 15px;\n";
echo "		background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0.9));\n";
echo "		pointer-events: none;\n";
echo "	}\n";
echo "	#note_more_indicator {\n";
echo "		position: absolute;\n";
echo "		left: 50%;\n";
echo "		transform: translateX(-50%);\n";
echo "		color: #999;\n";
echo "		font-size: 16px;\n";
echo "		font-weight: bold;\n";
echo "		letter-spacing: 2px;\n";
echo "		line-height: 34px;\n";
echo "		cursor: pointer;\n";
echo "		display: none;\n";
echo "		user-select: none;\n";
echo "	}\n";
echo "	#note_more_indicator:hover {\n";
echo "		color: #666;\n";
echo "	}\n";
echo "	#domain_note.collapsed.has-overflow {\n";
echo "		cursor: pointer;\n";
echo "	}\n";
echo "	#save_note_btn {\n";
echo "		padding: 4px 12px;\n";
echo "		font-size: 13px;\n";
echo "		transition: all 0.3s ease;\n";
echo "	}\n";
echo "	#save_note_btn:hover {\n";
echo "		transform: translateY(-1px);\n";
echo "		box-shadow: 0 2px 8px rgba(0,123,255,0.3);\n";
echo "	}\n";
echo "	#save_note_btn.saved {\n";
echo "		background-color: #28a745 !important;\n";
echo "		border-color: #28a745 !important;\n";
echo "	}\n";
echo "	#note_status_bar {\n";
echo "		padding: 5px !important;\n";
echo "		min-height: auto !important;\n";
echo "	}\n";
echo "</style>\n";
echo "<div class='hud_box' style='cursor: default;'>\n";
echo "	<form id='form_note' name='form_note'>\n";
echo "		<div class='panel panel-default'>\n";
echo "			<span class='hud_title'>$dashboard_name</span>\n";
echo "			<div class='panel-body' style='padding: 5px;'>\n";
echo "				<div id='note_container' class='note-container'>\n";
echo "					<textarea name='domain_note' id='domain_note' class='form-control' rows='1' style='resize: none; border: none;'>$domain_note</textarea>\n";
echo "				</div>\n";
echo "			</div>\n";
echo "			<div id='note_status_bar' class='panel-footer' style='position: relative;'>\n";
echo "				<span id='note_more_indicator'>...</span>\n";
if ($has_domain_note_edit) {
    echo "			<span style='float:right;'>\n";
    echo "				<button type='submit' id='save_note_btn' class='btn btn-primary' value='Save'>\n";
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
echo "	var textarea = document.getElementById('domain_note');\n";
echo "	var noteContainer = document.getElementById('note_container');\n";
echo "	var moreIndicator = document.getElementById('note_more_indicator');\n";
echo "	var isExpanded = false;\n";
echo "	\n";
echo "	// Check if content is overflowing\n";
echo "	function checkOverflow() {\n";
echo "		// Temporarily expand to check real scroll height\n";
echo "		var currentHeight = textarea.style.height;\n";
echo "		textarea.style.height = 'auto';\n";
echo "		var hasOverflow = textarea.scrollHeight > 45;\n";
echo "		textarea.style.height = currentHeight || '38px';\n";
echo "		\n";
echo "		if (!isExpanded && hasOverflow) {\n";
echo "			noteContainer.classList.add('has-overflow');\n";
echo "			textarea.classList.add('has-overflow');\n";
echo "			moreIndicator.style.display = 'block';\n";
echo "		} else {\n";
echo "			noteContainer.classList.remove('has-overflow');\n";
echo "			textarea.classList.remove('has-overflow');\n";
echo "			moreIndicator.style.display = 'none';\n";
echo "		}\n";
echo "		return hasOverflow;\n";
echo "	}\n";
echo "	\n";
echo "	// Expand textarea to show all content\n";
echo "	function expandTextarea() {\n";
echo "		isExpanded = true;\n";
echo "		// Remove collapsed class to allow expansion\n";
echo "		textarea.classList.remove('collapsed');\n";
echo "		textarea.classList.remove('has-overflow');\n";
echo "		// Calculate the needed height\n";
echo "		textarea.style.height = 'auto';\n";
echo "		var scrollHeight = textarea.scrollHeight;\n";
echo "		var newHeight = Math.min(Math.max(scrollHeight + 5, 80), 200);\n";
echo "		textarea.style.height = newHeight + 'px';\n";
echo "		noteContainer.classList.remove('has-overflow');\n";
echo "	}\n";
echo "	\n";
echo "	// Collapse textarea to default height\n";
echo "	function collapseTextarea() {\n";
echo "		isExpanded = false;\n";
echo "		textarea.classList.add('collapsed');\n";
echo "		textarea.style.height = '38px';\n";
echo "		checkOverflow();\n";
echo "	}\n";
echo "	\n";
echo "	// Expand on focus/click\n";
echo "	textarea.addEventListener('focus', expandTextarea);\n";
echo "	textarea.addEventListener('click', expandTextarea);\n";
echo "	\n";
echo "	// Click on dots to expand\n";
echo "	moreIndicator.addEventListener('click', function() {\n";
echo "		expandTextarea();\n";
echo "		textarea.focus();\n";
echo "	});\n";
echo "	\n";
echo "	// Collapse when clicking away (blur)\n";
echo "	textarea.addEventListener('blur', function() {\n";
echo "		// Small delay to allow save button click to register\n";
echo "		setTimeout(collapseTextarea, 150);\n";
echo "	});\n";
echo "	\n";
echo "	// Update height as user types while expanded\n";
echo "	textarea.addEventListener('input', function() {\n";
echo "		if (isExpanded) {\n";
echo "			textarea.style.height = 'auto';\n";
echo "			var newHeight = Math.min(textarea.scrollHeight + 5, 200);\n";
echo "			textarea.style.height = newHeight + 'px';\n";
echo "		} else {\n";
echo "			checkOverflow();\n";
echo "		}\n";
echo "	});\n";
echo "	\n";
echo "	// Initialize as collapsed\n";
echo "	textarea.classList.add('collapsed');\n";
echo "	textarea.style.height = '38px';\n";
echo "	\n";
echo "	// Check overflow on load (with delay to ensure DOM is ready)\n";
echo "	setTimeout(function() {\n";
echo "		checkOverflow();\n";
echo "	}, 200);\n";
echo "	\n";
echo "	document.getElementById('form_note').addEventListener('submit', function (event) {\n";
echo "		event.preventDefault();\n";
echo "		var form = document.getElementById('form_note');\n";
echo "		var formData = new FormData(form);\n";
echo "		var saveBtn = document.getElementById('save_note_btn');\n";
echo "		\n";
echo "		// Disable button during save\n";
echo "		saveBtn.disabled = true;\n";
echo "		saveBtn.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i> Saving...';\n";
echo "		\n";
echo "		fetch('/app/domain_notes/resources/domain_note_edit.php', {\n";
echo "			method: 'POST',\n";
echo "			body: new URLSearchParams(formData)\n";
echo "		})\n";
echo "				.then(response => response.json())\n";
echo "				.then(data => {\n";
echo "					// Show success state on button\n";
echo "					saveBtn.classList.add('saved');\n";
echo "					saveBtn.innerHTML = '<i class=\"fas fa-check\"></i> Saved!';\n";
echo "					\n";
echo "					// Reset button after 3 seconds (longer visible feedback)\n";
echo "					setTimeout(() => {\n";
echo "						saveBtn.classList.remove('saved');\n";
echo "						saveBtn.innerHTML = '<i class=\"fas fa-save\"></i> Save';\n";
echo "						saveBtn.disabled = false;\n";
echo "					}, 3000);\n";
echo "					\n";
echo "					// Update the textarea value with the new data\n";
echo "					if (data.note !== undefined) {\n";
echo "						document.getElementById('domain_note').value = data.note;\n";
echo "					}\n";
echo "					\n";
echo "					// Collapse the textarea after save\n";
echo "					collapseTextarea();\n";
echo "				})\n";
echo "				.catch(error => {\n";
echo "					console.error('Error:', error);\n";
echo "					// Reset button on error\n";
echo "					saveBtn.innerHTML = '<i class=\"fas fa-save\"></i> Save';\n";
echo "					saveBtn.disabled = false;\n";
echo "				});\n";
echo "	});\n";
echo "</script>\n";
echo "</form>\n";
echo "</div>\n";
