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

//application details
	$apps[$x]['name'] = "Domain Notes";
	$apps[$x]['uuid'] = "25351d47-2ca6-405b-b989-1b8f2f899dff";
	$apps[$x]['category'] = "";
	$apps[$x]['subcategory'] = "";
	$apps[$x]['version'] = "1.0";
	$apps[$x]['license'] = "Mozilla Public License 1.1";
	$apps[$x]['url'] = "http://www.fusionpbx.com";
	$apps[$x]['description']['en-us'] = "Domain Notes";
	$apps[$x]['description']['en-gb'] = "Domain Notes";
	$apps[$x]['description']['ar-eg'] = "";
	$apps[$x]['description']['de-at'] = "";
	$apps[$x]['description']['de-ch'] = "";
	$apps[$x]['description']['de-de'] = "";
	$apps[$x]['description']['es-cl'] = "";
	$apps[$x]['description']['es-mx'] = "";
	$apps[$x]['description']['fr-ca'] = "";
	$apps[$x]['description']['fr-fr'] = "";
	$apps[$x]['description']['he-il'] = "";
	$apps[$x]['description']['it-it'] = "";
	$apps[$x]['description']['nl-nl'] = "";
	$apps[$x]['description']['pl-pl'] = "";
	$apps[$x]['description']['pt-br'] = "";
	$apps[$x]['description']['pt-pt'] = "";
	$apps[$x]['description']['ro-ro'] = "";
	$apps[$x]['description']['ru-ru'] = "";
	$apps[$x]['description']['sv-se'] = "";
	$apps[$x]['description']['uk-ua'] = "";
	$apps[$x]['description']['zh-cn'] = "";
	$apps[$x]['description']['ja-jp'] = "";
	$apps[$x]['description']['ko-kr'] = "";

//note log view permissions
	$y = 0;
	$apps[$x]['permissions'][$y]['name'] = 'domain_note_view';
	$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
	$y++;
	$apps[$x]['permissions'][$y]['name'] = 'domain_note_add';
	$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
	$y++;
	$apps[$x]['permissions'][$y]['name'] = 'domain_note_edit';
	$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';

//database table
	$table_index = 0;
	$apps[$x]['db'][$table_index]['table']['name'] = 'v_domain_notes';
	$apps[$x]['db'][$table_index]['table']['parent'] = '';
	$field_index = 0;
	$apps[$x]['db'][$table_index]['fields'][$field_index]['name'] = 'domain_note_uuid';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['pgsql'] = 'uuid';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['sqlite'] = 'text';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['mysql'] = 'char(36)';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['key']['type'] = 'primary';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['description']['en-us'] = "";
	$field_index++;
	$apps[$x]['db'][$table_index]['fields'][$field_index]['name'] = "domain_uuid";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['pgsql'] = "uuid";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['sqlite'] = "text";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['mysql'] = "char(36)";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['key']['type'] = "foreign";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['key']['reference']['table'] = "v_domains";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['key']['reference']['field'] = "domain_uuid";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['description']['en-us'] = "";
	$field_index++;
	$apps[$x]['db'][$table_index]['fields'][$field_index]['name'] = 'domain_note_text';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type'] = 'text';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['description']['en-us'] = "";
	$field_index++;
	$apps[$x]['db'][$table_index]['fields'][$field_index]['name'] = "insert_user";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['pgsql'] = "uuid";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['sqlite'] = "text";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['mysql'] = "char(36)";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['description']['en-us'] = "";
	$field_index++;
	$apps[$x]['db'][$table_index]['fields'][$field_index]['name'] = "insert_date";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['pgsql'] = 'timestamptz';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['sqlite'] = 'date';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['mysql'] = 'date';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['description']['en-us'] = "";
	$field_index++;
	$apps[$x]['db'][$table_index]['fields'][$field_index]['name'] = "update_user";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['pgsql'] = "uuid";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['sqlite'] = "text";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['mysql'] = "char(36)";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['description']['en-us'] = "";
	$field_index++;
	$apps[$x]['db'][$table_index]['fields'][$field_index]['name'] = "update_date";
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['pgsql'] = 'timestamptz';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['sqlite'] = 'date';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['type']['mysql'] = 'date';
	$apps[$x]['db'][$table_index]['fields'][$field_index]['description']['en-us'] = "";
