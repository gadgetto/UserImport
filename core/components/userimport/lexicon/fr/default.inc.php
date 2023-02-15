<?php

/**
 * UserImport default
 *
 * @package userimport
 * @subpackage lexicon
 */

$_lang['userimport']                                            = '';
$_lang['userimport.desc']                                       = 'Importer des utilisateurs dans la base de données utilisateur MODX';
$_lang['userimport.cmp_title']                                  = '';
$_lang['userimport.settings_save_button']                       = 'Sauvegarder les Paramètres';
$_lang['userimport.msg_loading_defaults']                       = '';
$_lang['userimport.msg_loading_defaults_failed']                = '';
$_lang['userimport.msg_saving_defaults']                        = '';
$_lang['userimport.msg_saving_defaults_successfull']            = '';
$_lang['userimport.msg_saving_defaults_failed']                 = '';

$_lang['userimport.about_tab']                                  = 'A propos';
$_lang['userimport.about_credits']                              = 'Crédits';
$_lang['userimport.about_credits_modx_community']               = 'Un grand merci à la communauté MODX pour leur aide';

$_lang['userimport.notification_template_tab']                  = '';
$_lang['userimport.notification_template_tab_desc']             = '';
$_lang['userimport.notification_template_mail_subject']         = '';
$_lang['userimport.notification_template_mail_subject_desc']    = '';
$_lang['userimport.notification_template_mail_body']            = '';
$_lang['userimport.notification_template_mail_body_desc']       = '';
$_lang['userimport.notification_mail_options']                  = '';
$_lang['userimport.notification_mail_format']                   = '';
$_lang['userimport.notification_mail_format_desc']              = '';
$_lang['userimport.notification_mail_format_html']              = '';
$_lang['userimport.notification_mail_format_plaintext']         = '';

$_lang['userimport.import_users_tab']                           = 'Utilisateurs';
$_lang['userimport.import_users_tab_desc']                      = 'Importer des utilisateurs dans la base de donnée MODX. Attributer leur automatiquement un rôle et un groupe d\'utilisateur. Pour informations sur le format du fichier CSV, veuillez cliquer sur le bouton d\'<strong>aide !</strong>';
$_lang['userimport.import_users_file']                          = 'Fichier CSV';
$_lang['userimport.import_users_file_desc']                     = 'Choisissez un fichier CSV local qui contient les enregistrements d\'utilisateurs à importer. Le fichier sera téléchargé sur le serveur.';
$_lang['userimport.import_users_file_button']                   = 'Choisir un fichier...';
$_lang['userimport.import_users_batchsize']                     = 'Taille';
$_lang['userimport.import_users_batchsize_desc']                = 'Nombre de ligne à importer. Si défini à 0, toutes les lignes seront importées. 0 par défaut.';
$_lang['userimport.import_users_first_row_headers']             = '';
$_lang['userimport.import_users_first_row_headers_desc']        = '';
$_lang['userimport.import_users_delimiter']                     = 'Séparateur de champ';
$_lang['userimport.import_users_delimiter_desc']                = 'Définir un caractère séparateur de champ (un caractère seulement). , par défaut';
$_lang['userimport.import_users_enclosure']                     = 'Identificateur de texte';
$_lang['userimport.import_users_enclosure_desc']                = 'Définir un caractère identificateur de champ (un caractère seulement). " par défaut';
$_lang['userimport.import_users_assign_groups_roles']           = 'Assigner à un/des groupe(s) d\'utilisateur et rôle';
$_lang['userimport.import_users_groups']                        = 'Groupe d\'utilisateurs MODX';
$_lang['userimport.import_users_roles']                         = 'Rôles';
$_lang['userimport.import_users_options']                       = 'Options d\'import';
$_lang['userimport.import_users_email_as_username']             = 'Définir l\'adresse email comme identifiant';
$_lang['userimport.import_users_email_as_username_desc']        = 'Si activé, l\'identifiant sera généré à partir de l\'adresse email.';
$_lang['userimport.import_users_set_importmarker']              = '';
$_lang['userimport.import_users_set_importmarker_desc']         = '';
$_lang['userimport.import_users_notify_users']                  = '';
$_lang['userimport.import_users_notify_users_desc']             = '';

$_lang['userimport.import_users_button_start']                  = 'Démarrer import';
$_lang['userimport.import_users_status']                        = 'Statut';
$_lang['userimport.import_users_msgbox_start_now']              = 'Démarrer l\'import maintenant ?';
$_lang['userimport.import_users_msgbox_start_now_desc']         = '<strong>Il est recommandé de faire une sauvegarde de la base de donnée avant d\'importer !</strong><br>Voulez-vous démarrer le processus d\'importation maintenant ?';
$_lang['userimport.import_users_msg_importing']                 = 'Import...';
$_lang['userimport.import_users_msg_successfull']               = 'Import réussi';
$_lang['userimport.import_users_msg_failed']                    = 'Import échoué';
$_lang['userimport.import_users_unique_import_key']             = '';
$_lang['userimport.import_users_row']                           = 'Ligne: ';

$_lang['userimport.import_users_log_prep_csv_import']           = 'Préparation de \'import CSV...';
$_lang['userimport.import_users_log_importing_csv']             = 'Import du fichier CSV';
$_lang['userimport.import_users_log_extended_detected']         = '';
$_lang['userimport.import_users_log_batchsize']                 = 'Taille: ';
$_lang['userimport.import_users_log_imported_user']             = 'Importé: ';
$_lang['userimport.import_users_log_err_user_data']             = 'Non importé. Donnée erronée : ';
$_lang['userimport.import_users_log_err_user_failed']           = 'Non importé. Sauvegarde echouée : ';
$_lang['userimport.import_users_log_err_ns_username']           = 'Non importé. Identifiant manquant.';
$_lang['userimport.import_users_log_err_username_ae']           = 'Non importé. L\'identifiant  existe déjà : ';
$_lang['userimport.import_users_log_err_ns_email']              = 'Non importé. Adresse email manquante.';
$_lang['userimport.import_users_log_err_email_ae']              = 'Non importé. L\'adresse email existe déjà : ';
$_lang['userimport.import_users_log_err_email_invalid']         = 'Non importé. Adresse email invalide : ';
$_lang['userimport.import_users_log_err_dob_invalid']           = 'Non importé. Date de naissance invalide : ';
$_lang['userimport.import_users_log_err_gender_invalid']        = 'Non importé. Genre invalide : ';
$_lang['userimport.import_users_log_err_extended_invalid_json'] = '';
$_lang['userimport.import_users_log_ns_file']                   = 'Sélectionner un fichier CSV.';
$_lang['userimport.import_users_log_file_upload_failed']        = 'Le téléchargement du fichier CSV a échoué.';
$_lang['userimport.import_users_log_wrong_filetype']            = 'Seul les fichiers CSV sont acceptés.';
$_lang['userimport.import_users_log_wrong_delimiter_detected']  = '';
$_lang['userimport.import_users_log_delimiter_not_detected']    = '';
$_lang['userimport.import_users_log_wrong_enclosure_detected']  = '';
$_lang['userimport.import_users_log_enclosure_not_detected']    = '';
$_lang['userimport.import_users_log_no_class']                  = 'La classe ImportHandler ne peut pas être instanciée.';
$_lang['userimport.import_users_log_ns_batchsize']              = 'Veuillez spécifier une taille.';
$_lang['userimport.import_users_log_ns_delimiter']              = 'Veuillez spécifier un caractère séprateur de champ.';
$_lang['userimport.import_users_log_ns_enclosure']              = 'Veuillez spécifier un caractère identificateur de texte.';
$_lang['userimport.import_users_log_ns_grp']                    = 'Veuillez sélectionner au moins un groupe d\'utilisateur.';
$_lang['userimport.import_users_log_ns_mailsubject']            = '';
$_lang['userimport.import_users_log_ns_mailbody']               = '';
$_lang['userimport.import_users_log_finished']                  = 'Import terminé. Utilisateurs importés avec succès : ';
$_lang['userimport.import_users_log_failed']                    = 'Import échoué. Vérifiez les messages d\'erreurs.';
$_lang['userimport.import_users_log_err_open_file']             = 'Erreur à l\'ouverture du fichier.';
$_lang['userimport.import_users_log_diff_fields_values_count']  = '';

$_lang['userimport.import_users_log_err_email_max_len']         = 'Non importé. Longueur maximale du champ email dépassée.';
$_lang['userimport.import_users_log_err_username_max_len']      = 'Non importé. Longueur maximale du champ username dépassée.';
$_lang['userimport.import_users_log_err_fullname_max_len']      = 'Non importé. Longueur maximale du champ fullname dépassée.';
$_lang['userimport.import_users_log_err_phone_max_len']         = 'Non importé. Longueur maximale du champ phone dépassée.';
$_lang['userimport.import_users_log_err_mobilephone_max_len']   = 'Non importé. Longueur maximale du champ mobilephone dépassée.';
$_lang['userimport.import_users_log_err_address_max_len']       = 'Non importé. Longueur maximale du champ address dépassée.';
$_lang['userimport.import_users_log_err_country_max_len']       = 'Non importé. Longueur maximale du champ country dépassée.';
$_lang['userimport.import_users_log_err_city_max_len']          = 'Non importé. Longueur maximale du champ city dépassée.';
$_lang['userimport.import_users_log_err_state_max_len']         = 'Non importé. Longueur maximale du champ state dépassée.';
$_lang['userimport.import_users_log_err_zip_max_len']           = 'Non importé. Longueur maximale du champ zip dépassée.';
$_lang['userimport.import_users_log_err_fax_max_len']           = 'Non importé. Longueur maximale du champ fax dépassée.';
$_lang['userimport.import_users_log_err_photo_max_len']         = 'Non importé. Longueur maximale du champ photo dépassée.';
$_lang['userimport.import_users_log_err_comment_max_len']       = 'Non importé. Longueur maximale du champ comment dépassée.';
$_lang['userimport.import_users_log_err_website_max_len']       = 'Non importé. Longueur maximale du champ website dépassée.';

$_lang['userimport.import_users_log_password_autogenerated']    = '';
$_lang['userimport.import_users_log_password_len']              = '';

$_lang['setting_userimport.delimiter']                          = '';
$_lang['setting_userimport.delimiter_desc']                     = '';
$_lang['setting_userimport.enclosure']                          = '';
$_lang['setting_userimport.enclosure_desc']                     = '';
$_lang['setting_userimport.autousername']                       = '';
$_lang['setting_userimport.autousername_desc']                  = '';
$_lang['setting_userimport.setimportmarker']                    = '';
$_lang['setting_userimport.setimportmarker_desc']               = '';
$_lang['setting_userimport.notifyusers']                        = '';
$_lang['setting_userimport.notifyusers_desc']                   = '';
$_lang['setting_userimport.mailsubject']                        = '';
$_lang['setting_userimport.mailsubject_desc']                   = '';
$_lang['setting_userimport.mailbody']                           = '';
$_lang['setting_userimport.mailbody_desc']                      = '';
$_lang['setting_userimport.mail_format']                        = '';
$_lang['setting_userimport.mail_format_desc']                   = '';
