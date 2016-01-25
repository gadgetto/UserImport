<?php
/**
 * UserImport
 *
 * Copyright 2014 by bitego <office@bitego.com>
 *
 * UserImport is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * UserImport is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this software; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 */

/**
 * UserImport default
 *
 * @package userimport
 * @subpackage lexicon
 * @language en
 */

$_lang['userimport']                                            = 'User Import';
$_lang['userimport.desc']                                       = 'Imports users into the MODX user database.';
$_lang['userimport.cmp_title']                                  = 'User Import';

$_lang['userimport.about_tab']                                  = 'About User Import';
$_lang['userimport.about_credits']                              = 'Credits';
$_lang['userimport.about_credits_modx_community']               = 'Many thanks to the stunning MODx community for their tireless help!';

$_lang['userimport.import_users_tab']                           = 'Users';
$_lang['userimport.import_users_tab_desc']                      = 'Import users to the MODX user database, automatically add them to MODX user groups and assign MODX user roles. For informations about how to format the CSV file, please click the <strong>Help!</strong> button.';
$_lang['userimport.import_users_file']                          = 'CSV File';
$_lang['userimport.import_users_file_desc']                     = 'Choose a CSV file from a local file path which holds the user records to import. The file will be uploaded to the server.';
$_lang['userimport.import_users_file_button']                   = 'Choose file...';
$_lang['userimport.import_users_batchsize']                     = 'Batch Size';
$_lang['userimport.import_users_batchsize_desc']                = 'Number of records to import. If set to 0, all records will be imported. Default is 0.';
$_lang['userimport.import_users_first_row_headers']             = 'First row holds column headers';
$_lang['userimport.import_users_first_row_headers_desc']        = 'Activate this checkbox, if the first row of the CSV file holds the column headers.';
$_lang['userimport.import_users_delimiter']                     = 'Field Delimiter Character';
$_lang['userimport.import_users_delimiter_desc']                = 'Set the field delimiter (one character only). Default is ,';
$_lang['userimport.import_users_enclosure']                     = 'Field Enclosure Character';
$_lang['userimport.import_users_enclosure_desc']                = 'The field enclosure character (one character only). Default is "';
$_lang['userimport.import_users_assign_groups_roles']           = 'Assign MODX User Group(s) and Roles';
$_lang['userimport.import_users_groups']                        = 'MODX User Groups';
$_lang['userimport.import_users_roles']                         = 'MODX User Roles';
$_lang['userimport.import_users_options']                       = 'Import Options';
$_lang['userimport.import_users_email_as_username']             = 'Set Email Adress as Username';
$_lang['userimport.import_users_email_as_username_desc']        = 'If activated, the username is generated from the email address.';
$_lang['userimport.import_users_set_importmarker']              = 'Create import-markers';
$_lang['userimport.import_users_set_importmarker_desc']         = 'If activated, import-markers are saved as extended fields (Date, Key) for each imported user.';

$_lang['userimport.import_users_button_start']                  = 'Start import';
$_lang['userimport.import_users_status']                        = 'Import Status';
$_lang['userimport.import_users_msgbox_start_now']              = 'Start import now?';
$_lang['userimport.import_users_msgbox_start_now_desc']         = '<strong>It is recommended to backup the database before importing!</strong><br>Do you want to start the import process now?';
$_lang['userimport.import_users_msg_importing']                 = 'Importing...';
$_lang['userimport.import_users_msg_successfull']               = 'Import successfull';
$_lang['userimport.import_users_msg_failed']                    = 'Import failed';
$_lang['userimport.import_users_unique_import_key']             = 'Import-Key: ';
$_lang['userimport.import_users_row']                           = 'Row: ';

$_lang['userimport.import_users_log_prep_csv_import']           = 'Preparing CSV import...';
$_lang['userimport.import_users_log_importing_csv']             = 'Importing CSV file';
$_lang['userimport.import_users_log_batchsize']                 = 'Batch size: ';
$_lang['userimport.import_users_log_imported_user']             = 'Imported: ';
$_lang['userimport.import_users_log_err_user_data']             = 'Not imported. Wrong data: ';
$_lang['userimport.import_users_log_err_user_failed']           = 'Not imported. Saving failed: ';
$_lang['userimport.import_users_log_err_ns_username']           = 'Not imported. Username missing.';
$_lang['userimport.import_users_log_err_username_ae']           = 'Not imported. Username already exists: ';
$_lang['userimport.import_users_log_err_ns_email']              = 'Not imported. Email address missing.';
$_lang['userimport.import_users_log_err_email_ae']              = 'Not imported. Email address already exists: ';
$_lang['userimport.import_users_log_err_email_invalid']         = 'Not imported. No valid email address: ';
$_lang['userimport.import_users_log_err_dob_invalid']           = 'Not imported. No valid date of birth: ';
$_lang['userimport.import_users_log_err_gender_invalid']        = 'Not imported. No valid gender: ';
$_lang['userimport.import_users_log_ns_file']                   = 'Please select a CSV file.';
$_lang['userimport.import_users_log_wrong_filetype']            = 'Only CSV files are accepted.';
$_lang['userimport.import_users_log_no_class']                  = 'ImportHandler class could not be instantiated.';
$_lang['userimport.import_users_log_ns_batchsize']              = 'Please specify a batch-size.';
$_lang['userimport.import_users_log_ns_delimiter']              = 'Please specify the field delimiter character.';
$_lang['userimport.import_users_log_ns_enclosure']              = 'Please specify the field enclosure character.';
$_lang['userimport.import_users_log_ns_grp']                    = 'Please select at least one MODX usergroup.';
$_lang['userimport.import_users_log_finished']                  = 'Import finished. Successfully imported users: ';
$_lang['userimport.import_users_log_failed']                    = 'Import failed. Please check error messages.';
$_lang['userimport.import_users_log_err_open_file']             = 'Error opening the file.';

$_lang['userimport.import_users_log_err_email_max_len']         = 'Not imported. Max. field length for email exceeded.';
$_lang['userimport.import_users_log_err_username_max_len']      = 'Not imported. Max. field length for username exceeded.';
$_lang['userimport.import_users_log_err_fullname_max_len']      = 'Not imported. Max. field length for fullname exceeded.';
$_lang['userimport.import_users_log_err_phone_max_len']         = 'Not imported. Max. field length for phone exceeded.';
$_lang['userimport.import_users_log_err_mobilephone_max_len']   = 'Not imported. Max. field length for mobilephone exceeded.';
$_lang['userimport.import_users_log_err_address_max_len']       = 'Not imported. Max. field length for address exceeded.';
$_lang['userimport.import_users_log_err_country_max_len']       = 'Not imported. Max. field length for county exceeded.';
$_lang['userimport.import_users_log_err_city_max_len']          = 'Not imported. Max. field length for city exceeded.';
$_lang['userimport.import_users_log_err_state_max_len']         = 'Not imported. Max. field length for state exceeded.';
$_lang['userimport.import_users_log_err_zip_max_len']           = 'Not imported. Max. field length for zip exceeded.';
$_lang['userimport.import_users_log_err_fax_max_len']           = 'Not imported. Max. field length for fax exceeded.';
$_lang['userimport.import_users_log_err_photo_max_len']         = 'Not imported. Max. field length for photo exceeded.';
$_lang['userimport.import_users_log_err_comment_max_len']       = 'Not imported. Max. field length for comment exceeded.';
$_lang['userimport.import_users_log_err_website_max_len']       = 'Not imported. Max. field length for website exceeded.';
