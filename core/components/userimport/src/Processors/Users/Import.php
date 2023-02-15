<?php

/**
 * This file is part of the UserImport package.
 *
 * @copyright bitego (Martin Gartner)
 * @license GNU General Public License v2.0 (and later)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bitego\UserImport\Processors\Users;

use MODX\Revolution\modX;
use MODX\Revolution\Processors\Processor;
use Bitego\UserImport\ImportHandler;

/**
 * User import processor
 *
 * @package userimport
 * @subpackage processors
 */

class Import extends Processor
{
    /** @var ImportHandler $importhandler A reference to the ImportHandler object */
    public $importhandler = null;

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['user', 'userimport:default'];
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function initialize()
    {
        set_time_limit(0);
        $this->importhandler = new ImportHandler($this->modx);
        return parent::initialize();
    }

    /**
     * Trigger the import process.
     *
     * @return mixed
     */
    public function process()
    {
        $error = false;

        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('userimport.import_users_log_prep_csv_import'));
        sleep(1);

        if (!$this->importhandler instanceof ImportHandler) {
            $this->modx->log(modX::LOG_LEVEL_FATAL, $this->modx->lexicon('userimport.import_users_log_no_class'));
            $error = true;
        }

        // Make sure a supported file was specified and the temporary upload succeeded:
        // $file is an array:
        //
        // Array(
        //     [name] => users.txt
        //     [type] => text/plain
        //     [tmp_name] => /tmp/php4HmGt8
        //     [error] => 0
        //     [size] => 1021
        // )

        $file = $this->getProperty('file');
        if (empty($file['name'])) {
            $this->addFieldError('file', $this->modx->lexicon('userimport.import_users_log_ns_file'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_ns_file')
            );
            $error = true;
            sleep(1);
        } elseif ($file['error'] || empty($file['tmp_name'])) {
            $this->addFieldError('file', $this->modx->lexicon('userimport.import_users_log_file_upload_failed'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_file_upload_failed')
            );
            $error = true;
            sleep(1);
        } elseif (!$this->importhandler->csvMimeType($file['type'])) {
            $this->addFieldError('file', $this->modx->lexicon('userimport.import_users_log_wrong_filetype'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_wrong_filetype')
            );
            $error = true;
            sleep(1);
        }

        // First row of CSV file holds column headers?
        $hasHeader = $this->getProperty('hasheader') ? true : false;

        // Make sure a batchsize was specified
        $batchsize = $this->getProperty('batchsize', 0);
        if (empty($batchsize) && !is_numeric($batchsize)) {
            $this->addFieldError('batchsize', $this->modx->lexicon('userimport.import_users_log_ns_batchsize'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_ns_batchsize')
            );
            $error = true;
            sleep(1);
        }

        // Make sure a delimiter was specified
        $delimiter = $this->getProperty('delimiter');
        if (empty($delimiter)) {
            $this->addFieldError('delimiter', $this->modx->lexicon('userimport.import_users_log_ns_delimiter'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_ns_delimiter')
            );
            $error = true;
            sleep(1);
        }

        // Make sure an enclosure was specified */
        $enclosure = $this->getProperty('enclosure');
        if (empty($enclosure)) {
            $this->addFieldError('enclosure', $this->modx->lexicon('userimport.import_users_log_ns_enclosure'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_ns_enclosure')
            );
            $error = true;
            sleep(1);
        }

        // Get autoUsername setting (username = email?)
        $autoUsername = $this->getProperty('autousername') ? true : false;

        // Get setImportmarker setting (write import-markers to extended fields?)
        $setImportmarker = $this->getProperty('setimportmarker') ? true : false;

        // Get notifyUsers setting (notify imported users via email?)
        $notifyUsers = $this->getProperty('notifyusers') ? true : false;

        // Get mailSubject setting
        $mailSubject = $this->getProperty('mailsubject', '');
        if ($notifyUsers && empty($mailSubject)) {
            $this->addFieldError('mailsubject', $this->modx->lexicon('userimport.import_users_log_ns_mailsubject'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_ns_mailsubject')
            );
            $error = true;
            sleep(1);
        }

        // Get mailBody setting
        $mailBody = $this->getProperty('mailbody', '');
        if ($notifyUsers && empty($mailBody)) {
            $this->addFieldError('mailbody', $this->modx->lexicon('userimport.import_users_log_ns_mailbody'));
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_ns_mailbody')
            );
            $error = true;
            sleep(1);
        }

        // Get mailFormat setting
        $mailFormat = $this->getProperty('mail_format') ? true : false;

        // Get selected MODX user group(s)
        $usergroups = $this->getProperty('usergroups');
        $groups = [];
        if (!empty($usergroups)) {
            // extract group IDs
            // (e.g. n_ug_5,n_ug_6,n_ug_7)
            // $nodeparts[0] = 'n'
            // $nodeparts[1] = 'ug'
            // $nodeparts[2] = grpID
            // $nodeparts[3] = parent grpID (or empty)

            $nodes = explode(',', $usergroups);

            foreach ($nodes as $node) {
                $nodeparts = explode('_', $node);
                if ($nodeparts[1] == 'ug') {
                    $groups[] = $nodeparts[2];
                }
            }
        }

        // Get MODX user role
        $role = $this->getProperty('role', 0);

        // Only continue with processing if no errors occurred
        if ($error || $this->hasErrors()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_failed'));
            sleep(2);
            unset($this->importhandler);
            return $this->failure();
        }

        $this->modx->log(
            modX::LOG_LEVEL_INFO,
            $this->modx->lexicon('userimport.import_users_log_importing_csv') . ' ' . $file['name']
        );
        sleep(1);
        $this->modx->log(
            modX::LOG_LEVEL_INFO,
            $this->modx->lexicon('userimport.import_users_log_batchsize') . ' ' . $batchsize
        );
        sleep(1);

        // Initialize the ImportHandler object
        if ($this->importhandler->init($file['tmp_name'], $hasHeader, $delimiter, $enclosure) == false) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_err_open_file')
            );
            sleep(1);
            $error = true;
        }

        // Only continue with processing if no errors occurred
        if ($error) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_failed')
            );
            sleep(2);
            unset($this->importhandler);
            return $this->failure();
        }

        $result = $this->importhandler->importUsers(
            $batchsize,
            $groups,
            $role,
            $autoUsername,
            $setImportmarker,
            $notifyUsers,
            $mailSubject,
            $mailBody,
            $mailFormat
        );

        $this->modx->log(
            modX::LOG_LEVEL_INFO,
            $this->modx->lexicon('userimport.import_users_log_finished') . $result
        );
        $this->modx->log(
            modX::LOG_LEVEL_INFO,
            $this->modx->lexicon('userimport.import_users_unique_import_key') . $this->importhandler->getImportKey()
        );
        sleep(2);
        unset($this->importhandler);
        return $this->success();
    }
}
