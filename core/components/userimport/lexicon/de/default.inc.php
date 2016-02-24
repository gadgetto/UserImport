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
 * @language de
 */

$_lang['userimport']                                            = 'User Import';
$_lang['userimport.desc']                                       = 'Importiert Benutzer in die MODX Benutzer-Datenbank.';
$_lang['userimport.cmp_title']                                  = 'User Import';

$_lang['userimport.about_tab']                                  = 'Über User Import';
$_lang['userimport.about_credits']                              = 'Credits';
$_lang['userimport.about_credits_modx_community']               = 'Herzlichen Dank der fantastischen MODx Community für ihre unermüdliche Hilfe!';

$_lang['userimport.import_users_tab']                           = 'Benutzer';
$_lang['userimport.import_users_tab_desc']                      = 'Import von Benutzern in die MODX Benutzer Datenbank, automatische Zuordnung zu MODX Benutzer-Gruppen und Zuweisung von MODX Benutzer-Rollen. Für Informationen betreffend Formatierung der CSV Datei, klicken Sie bitte auf den <strong>Hilfe!</strong> Button.';
$_lang['userimport.import_users_file']                          = 'CSV Datei';
$_lang['userimport.import_users_file_desc']                     = 'Auswahl einer CSV Datei, welche die zu importierenden Benutzerdatensätze enthält. Die Datei wird auf den Server hochgeladen.';
$_lang['userimport.import_users_file_button']                   = 'Datei wählen...';
$_lang['userimport.import_users_batchsize']                     = 'Batch Größe';
$_lang['userimport.import_users_batchsize_desc']                = 'Anzahl der Datensätze, die importiert werden sollen. Wird 0 eingetragen, werden alle Datensätze importiert. Standard ist 0.';
$_lang['userimport.import_users_first_row_headers']             = 'Erste Zeile enthält Feldnamen';
$_lang['userimport.import_users_first_row_headers_desc']        = 'Aktivieren Sie diese Checkbox, wenn die erste Zeile der CSV Datei die Feldnamen enthält.';
$_lang['userimport.import_users_delimiter']                     = 'Feld-Trennzeichen';
$_lang['userimport.import_users_delimiter_desc']                = 'Legen Sie das Feld-Trennzeichen fest (nur ein Zeichen). Standard ist ,';
$_lang['userimport.import_users_enclosure']                     = 'Feld-Begrenzerzeichen';
$_lang['userimport.import_users_enclosure_desc']                = 'Legen Sie das Feld-Begrenzerzeichen fest (nur ein Zeichen). Standard ist "';
$_lang['userimport.import_users_assign_groups_roles']           = 'MODX Benutzer-Gruppe(n) und Rollen zuweisen';
$_lang['userimport.import_users_groups']                        = 'MODX Benutzer-Gruppen';
$_lang['userimport.import_users_roles']                         = 'MODX Benutzer-Rollen';
$_lang['userimport.import_users_options']                       = 'Import Optionen';
$_lang['userimport.import_users_email_as_username']             = 'Email Adresse als Benutzername setzen';
$_lang['userimport.import_users_email_as_username_desc']        = 'Wenn aktiviert, wird der Benutzername aus der Email Adresse generiert.';
$_lang['userimport.import_users_set_importmarker']              = 'Import-Markierungen erzeugen';
$_lang['userimport.import_users_set_importmarker_desc']         = 'Wenn aktiviert, werden für jeden importierten Benutzer Import-Markierungen als erweiterte Felder (Date, Key) gespeichert.';

$_lang['userimport.import_users_button_start']                  = 'Import starten';
$_lang['userimport.import_users_status']                        = 'Import Status';
$_lang['userimport.import_users_msgbox_start_now']              = 'Import jetzt starten?';
$_lang['userimport.import_users_msgbox_start_now_desc']         = '<strong>Es wird empfohlen vor dem Import ein Backup der Datenbank zu erstellen!</strong><br>Möchten Sie den Import Prozess jetzt starten?';
$_lang['userimport.import_users_msg_importing']                 = 'Importiere...';
$_lang['userimport.import_users_msg_successfull']               = 'Import erfolgreich';
$_lang['userimport.import_users_msg_failed']                    = 'Import fehlgeschlagen';
$_lang['userimport.import_users_unique_import_key']             = 'Import-Schlüssel: ';
$_lang['userimport.import_users_row']                           = 'Zeile: ';

$_lang['userimport.import_users_log_prep_csv_import']           = 'Bereite CSV Import vor...';
$_lang['userimport.import_users_log_importing_csv']             = 'Importiere CSV Datei';
$_lang['userimport.import_users_log_extended_detected']         = 'Extended Fields erkannt: ';
$_lang['userimport.import_users_log_batchsize']                 = 'Batch Größe: ';
$_lang['userimport.import_users_log_imported_user']             = 'Importiert: ';
$_lang['userimport.import_users_log_err_user_data']             = 'Nicht importiert. Datenformat falsch: ';
$_lang['userimport.import_users_log_err_user_failed']           = 'Nicht importiert. Speichern fehlgeschlagen: ';
$_lang['userimport.import_users_log_err_ns_username']           = 'Nicht importiert. Benutzername fehlt.';
$_lang['userimport.import_users_log_err_username_ae']           = 'Nicht importiert. Benutzername existiert bereits: ';
$_lang['userimport.import_users_log_err_ns_email']              = 'Nicht importiert. Email Adresse fehlt.';
$_lang['userimport.import_users_log_err_email_ae']              = 'Nicht importiert. Email Adresse existiert bereits: ';
$_lang['userimport.import_users_log_err_email_invalid']         = 'Nicht importiert. Email Adresse ungültig: ';
$_lang['userimport.import_users_log_err_dob_invalid']           = 'Nicht importiert. Geburtsdatum ungültig: ';
$_lang['userimport.import_users_log_err_gender_invalid']        = 'Nicht importiert. Geschlecht ungültig: ';
$_lang['userimport.import_users_log_err_extended_invalid_json'] = 'Nicht importiert. Extended Fields Json String ungültig: ';
$_lang['userimport.import_users_log_ns_file']                   = 'Bitte wählen Sie eine CSV Datei.';
$_lang['userimport.import_users_log_wrong_filetype']            = 'Nur CSV Dateien sind zulässig.';
$_lang['userimport.import_users_log_no_class']                  = 'ImportHandler Klasse konnte nicht instanziert werden.';
$_lang['userimport.import_users_log_ns_batchsize']              = 'Bitte geben Sie eine Batch Größe an.';
$_lang['userimport.import_users_log_ns_delimiter']              = 'Bitte geben Sie das Feld-Trennzeichen an.';
$_lang['userimport.import_users_log_ns_enclosure']              = 'Bitte geben Sie das Feld-Begrenzerzeichen an.';
$_lang['userimport.import_users_log_ns_grp']                    = 'Bitte wählen Sie mindestens eine MODX Benutzer-Gruppe aus.';
$_lang['userimport.import_users_log_finished']                  = 'Import abgeschlossen. Erfolgreich importierte Benutzer: ';
$_lang['userimport.import_users_log_failed']                    = 'Import fehlgeschlagen. Bitte überprüfen Sie die Fehlermeldungen.';
$_lang['userimport.import_users_log_err_open_file']             = 'Fehler beim Öffnen der Datei.';

$_lang['userimport.import_users_log_err_email_max_len']         = 'Nicht importiert. Max. Feldlänge für email überschritten.';
$_lang['userimport.import_users_log_err_username_max_len']      = 'Nicht importiert. Max. Feldlänge für username überschritten.';
$_lang['userimport.import_users_log_err_fullname_max_len']      = 'Nicht importiert. Max. Feldlänge für fullname überschritten.';
$_lang['userimport.import_users_log_err_phone_max_len']         = 'Nicht importiert. Max. Feldlänge für phone überschritten.';
$_lang['userimport.import_users_log_err_mobilephone_max_len']   = 'Nicht importiert. Max. Feldlänge für mobilephone überschritten.';
$_lang['userimport.import_users_log_err_address_max_len']       = 'Nicht importiert. Max. Feldlänge für address überschritten.';
$_lang['userimport.import_users_log_err_country_max_len']       = 'Nicht importiert. Max. Feldlänge für county überschritten.';
$_lang['userimport.import_users_log_err_city_max_len']          = 'Nicht importiert. Max. Feldlänge für city überschritten.';
$_lang['userimport.import_users_log_err_state_max_len']         = 'Nicht importiert. Max. Feldlänge für state überschritten.';
$_lang['userimport.import_users_log_err_zip_max_len']           = 'Nicht importiert. Max. Feldlänge für zip überschritten.';
$_lang['userimport.import_users_log_err_fax_max_len']           = 'Nicht importiert. Max. Feldlänge für fax überschritten.';
$_lang['userimport.import_users_log_err_photo_max_len']         = 'Nicht importiert. Max. Feldlänge für photo überschritten.';
$_lang['userimport.import_users_log_err_comment_max_len']       = 'Nicht importiert. Max. Feldlänge für comment überschritten.';
$_lang['userimport.import_users_log_err_website_max_len']       = 'Nicht importiert. Max. Feldlänge für website überschritten.';

$_lang['userimport.import_users_log_password_autogenerated']    = 'Passwort auto-generiert.';
$_lang['userimport.import_users_log_password_len']              = 'Bereitgestelltes Passwort zu kurz. Min. Länge: ';
