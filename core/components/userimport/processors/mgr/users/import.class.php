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

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/model/userimport/importhandler.class.php';

/**
 * User import processor
 *
 * @package userimport
 * @subpackage processors
 */

class UserImportProcessor extends modProcessor {    

    public $languageTopics = array('userimport:default');

    /** @var ImportHandler $importhandler A reference to the ImportHandler object */
    public $importhandler = null;

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function initialize() {
        set_time_limit(0);
        $this->importhandler = new ImportHandler($this->modx);
        return parent::initialize();
    }

	/**
	 * 
     * 
     * @return mixed
	 */    
	public function process() {

        $error = false;
        
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('userimport.import_users_log_prep_csv_import'));
        sleep(1);

        if (!($this->importhandler instanceof ImportHandler)) {
            $this->modx->log(modX::LOG_LEVEL_FATAL, $this->modx->lexicon('userimport.import_users_log_no_class'));
            $error = true;
        }

        // Make sure a supported file was specified ($file is an array!)
        $file = $this->getProperty('file');
        if (empty($file['name'])) {
            $this->addFieldError('file', $this->modx->lexicon('userimport.import_users_log_ns_file'));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_ns_file'));
            sleep(1);
        } elseif (!$this->importhandler->csvMimeType($file['type'])) {
            $this->addFieldError('file', $this->modx->lexicon('userimport.import_users_log_wrong_filetype'));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_wrong_filetype'));
            sleep(1);
        }

        // First row of CSV file holds column headers?
        $hasHeader = $this->getProperty('hasheader') ? true : false;

        // Make sure a batchsize was specified
        $batchsize = $this->getProperty('batchsize');
        if (empty($batchsize) && !is_numeric($batchsize)) {
            $this->addFieldError('batchsize', $this->modx->lexicon('userimport.import_users_log_ns_batchsize'));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_ns_batchsize'));
            sleep(1);
        }
        
        // Make sure a delimiter was specified
        $delimiter = $this->getProperty('delimiter');
        if (empty($delimiter)) {
            $this->addFieldError('delimiter', $this->modx->lexicon('userimport.import_users_log_ns_delimiter'));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_ns_delimiter'));
            sleep(1);
        }
        
        // Make sure an enclosure was specified */
        $enclosure = $this->getProperty('enclosure');
        if (empty($enclosure)) {
            $this->addFieldError('enclosure', $this->modx->lexicon('userimport.import_users_log_ns_enclosure'));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_ns_enclosure'));
            sleep(1);
        }

        // Get autoUsername setting (username = email?)
        $autoUsername = $this->getProperty('autousername') ? true : false;

        // Get setImportmarker setting (write import-markers to extended fields?)
        $setImportmarker = $this->getProperty('setimportmarker') ? true : false;

        // Get notifyUsers setting (notify imported users via email?)
        $notifyUsers = $this->getProperty('notifyusers') ? true : false;

        // Get mailSubject setting
        $mailSubject = $this->getProperty('mailsubject');
        if ($notifyUsers && empty($mailSubject)) {
            $this->addFieldError('mailsubject', $this->modx->lexicon('userimport.import_users_log_ns_mailsubject'));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_ns_mailsubject'));
            sleep(1);
        }

        // Get mailBody setting
        $mailBody = $this->getProperty('mailbody');
        if ($notifyUsers && empty($mailBody)) {
            $this->addFieldError('mailbody', $this->modx->lexicon('userimport.import_users_log_ns_mailbody'));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_ns_mailbody'));
            sleep(1);
        }

        // Get selected MODX user group(s)
        $usergroups = $this->getProperty('usergroups');
        $groups = array();
        if (!empty($usergroups)) {
            // extract group IDs
            // (e.g. n_ug_5,n_ug_6,n_ug_7 )
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
        $role = $this->getProperty('role');

        // Only continue with processing if no errors occurred
        if ($error || $this->hasErrors()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_failed'));
            sleep(2);
            unset($this->importhandler);
            return $this->failure();
        }

        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('userimport.import_users_log_importing_csv').' '.$file['name']);
        sleep(1);
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('userimport.import_users_log_batchsize').' '.$batchsize);
        sleep(1);

        // Initialize the ImportHandler object
        if ($this->importhandler->init($file['tmp_name'], $hasHeader, $delimiter, $enclosure) == false) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_err_open_file'));
            sleep(1);
            $error = true;
        }
        
        // Only continue with processing if no errors occurred
        if ($error) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_failed'));
            sleep(2);
            unset($this->importhandler);
            return $this->failure();
        }
        
        $result = $this->importhandler->importUsers($batchsize, $groups, $role, $autoUsername, $setImportmarker, $notifyUsers, $mailSubject, $mailBody);


        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('userimport.import_users_log_finished').$result);
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('userimport.import_users_unique_import_key').$this->importhandler->getImportKey());
        sleep(2);
        unset($this->importhandler);
        return $this->success();
	}
}
return 'UserImportProcessor';
