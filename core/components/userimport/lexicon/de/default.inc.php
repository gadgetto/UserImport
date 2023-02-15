<?php

/**
 * UserImport default
 *
 * @package userimport
 * @subpackage lexicon
 */

$_lang['userimport']                                            = 'User Import';
$_lang['userimport.desc']                                       = 'Importiert Benutzer in die MODX Benutzer-Datenbank.';
$_lang['userimport.cmp_title']                                  = 'User Import';
$_lang['userimport.settings_save_button']                       = 'Einstellungen Speichern';
$_lang['userimport.msg_loading_defaults']                       = 'Standardeinstellungen werden geladen...';
$_lang['userimport.msg_loading_defaults_failed']                = 'Laden der Standardeinstellungen fehlgeschlagen';
$_lang['userimport.msg_saving_defaults']                        = 'Standardeinstellungen werden gespeichert...';
$_lang['userimport.msg_saving_defaults_successfull']            = 'Die Standardeinstellungen wurden gespeichert';
$_lang['userimport.msg_saving_defaults_failed']                 = 'Speichern der Standardeinstellungen fehlgeschlagen';

$_lang['userimport.about_tab']                                  = 'Über User Import';
$_lang['userimport.about_credits']                              = 'Credits';
$_lang['userimport.about_credits_modx_community']               = 'Herzlichen Dank der fantastischen MODx Community für ihre unermüdliche Hilfe!';

$_lang['userimport.notification_template_tab']                  = 'Benachrichtigung Vorlage';
$_lang['userimport.notification_template_tab_desc']             = 'Wenn aktiviert (Benutzer Tab -> Import Optionen -> Benutzer benachrichtigen), erhalten alle neu importierten Benutzer eine Email-Benachrichtigung. Sie können die Vorlage für die Benachrichtigung hier einrichten.';
$_lang['userimport.notification_template_mail_subject']         = 'Email-Betreff';
$_lang['userimport.notification_template_mail_subject_desc']    = 'Geben Sie den Betreff für die Email-Benachrichtigung hier ein.';
$_lang['userimport.notification_template_mail_body']            = 'Email-Text';
$_lang['userimport.notification_template_mail_body_desc']       = 'Geben Sie den Textkörper für die Email-Benachrichtigung hier ein.';
$_lang['userimport.notification_mail_options']                  = 'Mail Optionen';
$_lang['userimport.notification_mail_format']                   = 'Mail Format';
$_lang['userimport.notification_mail_format_desc']              = 'Das Format des Mail Inhaltes.';
$_lang['userimport.notification_mail_format_html']              = 'HTML';
$_lang['userimport.notification_mail_format_plaintext']         = 'Nur Text';

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
$_lang['userimport.import_users_notify_users']                  = 'Benutzer benachrichtigen';
$_lang['userimport.import_users_notify_users_desc']             = 'Wenn aktiviert, erhalten alle neu importierten Benutzer eine Email-Benachrichtigung.';

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
$_lang['userimport.import_users_log_file_upload_failed']        = 'Upload der CSV Datei fehlgeschlagen.';
$_lang['userimport.import_users_log_wrong_filetype']            = 'Nur CSV Dateien sind zulässig.';
$_lang['userimport.import_users_log_wrong_delimiter_detected']  = 'Die CSV Datei scheint falsche Feld-Trennzeichen zu enthalten. Erkannt wurde: ';
$_lang['userimport.import_users_log_delimiter_not_detected']    = 'Es konnten keine eindeutigen Feld-Trennzeichen erkannt werden.';
$_lang['userimport.import_users_log_wrong_enclosure_detected']  = 'Die CSV Datei scheint falsche Feld-Begrenzerzeichen zu enthalten. Erkannt wurde: ';
$_lang['userimport.import_users_log_enclosure_not_detected']    = 'Es konnten keine eindeutigen Feld-Begrenzerzeichen erkannt werden.';
$_lang['userimport.import_users_log_no_class']                  = 'ImportHandler Klasse konnte nicht instanziert werden.';
$_lang['userimport.import_users_log_ns_batchsize']              = 'Bitte geben Sie eine Batch Größe an.';
$_lang['userimport.import_users_log_ns_delimiter']              = 'Bitte geben Sie das Feld-Trennzeichen an.';
$_lang['userimport.import_users_log_ns_enclosure']              = 'Bitte geben Sie das Feld-Begrenzerzeichen an.';
$_lang['userimport.import_users_log_ns_grp']                    = 'Bitte wählen Sie mindestens eine MODX Benutzer-Gruppe aus.';
$_lang['userimport.import_users_log_ns_mailsubject']            = 'Bitte geben Sie den Betreff für die Email-Benachrichtigung ein.';
$_lang['userimport.import_users_log_ns_mailbody']               = 'Bitte geben Sie den Textkörper für die Email-Benachrichtigung ein.';
$_lang['userimport.import_users_log_finished']                  = 'Import abgeschlossen. Erfolgreich importierte Benutzer: ';
$_lang['userimport.import_users_log_failed']                    = 'Import fehlgeschlagen. Bitte überprüfen Sie die Fehlermeldungen.';
$_lang['userimport.import_users_log_err_open_file']             = 'Fehler beim Öffnen der Datei.';
$_lang['userimport.import_users_log_diff_fields_values_count']  = 'Zeile mit unterschiedlicher Feld-Wert Anzahl gefunden - wird nicht importiert: ';

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

$_lang['setting_userimport.delimiter']                          = 'Feld-Trennzeichen';
$_lang['setting_userimport.delimiter_desc']                     = 'Legen Sie das Feld-Trennzeichen fest (nur ein Zeichen). Standard ist ,';
$_lang['setting_userimport.enclosure']                          = 'Feld-Begrenzerzeichen';
$_lang['setting_userimport.enclosure_desc']                     = 'Legen Sie das Feld-Begrenzerzeichen fest (nur ein Zeichen). Standard ist "';
$_lang['setting_userimport.autousername']                       = 'Email Adresse als Benutzername setzen';
$_lang['setting_userimport.autousername_desc']                  = 'Wenn aktiviert, wird der Benutzername aus der Email Adresse generiert.';
$_lang['setting_userimport.setimportmarker']                    = 'Import-Markierungen erzeugen';
$_lang['setting_userimport.setimportmarker_desc']               = 'Wenn aktiviert, werden für jeden importierten Benutzer Import-Markierungen als erweiterte Felder (Date, Key) gespeichert.';
$_lang['setting_userimport.notifyusers']                        = 'Benutzer benachrichtigen';
$_lang['setting_userimport.notifyusers_desc']                   = 'Wenn aktiviert, erhalten alle neu importierten Benutzer eine Email-Benachrichtigung.';
$_lang['setting_userimport.mailsubject']                        = 'Email-Betreff';
$_lang['setting_userimport.mailsubject_desc']                   = 'Geben Sie den Betreff für die Email-Benachrichtigung ein.';
$_lang['setting_userimport.mailbody']                           = 'Email-Text';
$_lang['setting_userimport.mailbody_desc']                      = 'Geben Sie den Textkörper für die Email-Benachrichtigung ein.';
$_lang['setting_userimport.mail_format']                        = 'Mail Format';
$_lang['setting_userimport.mail_format_desc']                   = 'Das Format des Mail Inhaltes (1 = HTML, 0 = Nur Text)';
