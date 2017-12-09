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
 * ImportHandler class handles batch import of users into MODX users database
 * and batch assign MODX user groups + role.
 *
 * @package userimport
 */

class ImportHandler {

    /** @var modX $modx A reference to the modX object */
    public $modx = null;

    /** @var array $config An array of config values */
    public $config = array();
    
    /** @var string $importKey A unique string to identify a group of imported users (uik+timestamp) */
    public $importKey;
    
    /** @var resource $fileHandle A valid file pointer to a file successfully opened */
    public $fileHandle = false;
    
    /** @var int $lineLength Must be greater than the longest line (in characters) to be found in the CSV file */
    public $lineLength;

    /** @var string $delimiter The field delimiter (one character only) */
    public $delimiter;

    /** @var string $enclosure The field enclosure character (one character only) */
    public $enclosure;

    /** @var string $escape The escape character (one character only). Defaults to backslash. */
    public $escape;

    /** @var boolean $hasHeader If the first row includes field names */
    public $hasHeader;

    /** @var array $header The first row (field names) */
    public $header = array();
    
    /** @var int $batchSize Number of users to be imported in one batch */
    public $batchSize;

    /** @var array $userFields The internal MODX user field names (from modUser and modProfile) */
    public $userFields = array(
        'email',       // varchar 100
        'username',    // varchar 100
        'password',    // varchar 255
        'fullname',    // varchar 100
        'phone',       // varchar 100
        'mobilephone', // varchar 100
        'dob',         // int 10 (unix timestamp)
        'gender',      // int 1 (1 = male, 2 = female, 3 = other)
        'address',     // text
        'country',     // varchar 255
        'city',        // varchar 255
        'state',       // varchar 25
        'zip',         // varchar 25
        'fax',         // varchar 100
        'photo',       // varchar 255
        'comment',     // text
        'website',     // varchar 255
        'extended',    // text
    );
    
    /** @var array $extendedFields The extended user-field names (modProfile) */
    public $extendedFields = array();

    /** @var boolean $legacyFgetcsv If fgetcsv() is used under PHP version < 5.3.0 */
    public $legacyFgetcsv = false;


    /**
     * Constructor for ImportHandler object.
     *
     * @access public
     * @param modX &$modx A reference to the modX object
     */
    public function __construct(modX &$modx, array $config = array()) {
        $this->modx = &$modx;
        $this->modx->lexicon->load('user,userimport:default');
        $this->importKey = uniqid('uik', true);
        ini_set('auto_detect_line_endings', true);
        $this->config = array_merge(array(
            'use_multibyte' => (boolean)$this->modx->getOption('use_multibyte', null, false),
            'encoding'      => $this->modx->getOption('modx_charset', null, 'UTF-8'),
        ), $config);
        
        // Since PHP 5.3.0 the 'escape' parameter was added to the fgetcsv() function,
        // using on PHP 5.2 will throw a warning and not return an array.
        $this->legacyFgetcsv = version_compare(PHP_VERSION, '5.3.0', '<');
    }
    

    /**
     * Destructor for ImportHandler object.
     * 
     * @access public
     * @return void
     */
    public function __destruct() {
        $this->_closeFile();
        ini_set('auto_detect_line_endings', false);
    }

    /**
     * Initialize csv file import.
     * 
     * @access public
     * @param string $filePath
     * @param bool $hasHeader (default: true)
     * @param string $delimiter (default: ,)
     * @param string $enclosure (default: ")
     * @param string $escape (default: \)
     * @param int $lineLength (default: 4096)
     * @return boolean
     */
    public function init($filePath, $hasHeader = true, $delimiter = ',', $enclosure = '"', $escape = '\\', $lineLength = 4096) {
        if ($this->_openFile($filePath) == false) {
            return false;
        }
        $this->delimiter   = $delimiter;
        $this->enclosure   = $enclosure;
        $this->escape      = $escape;
        $this->lineLength  = $lineLength;
        $this->hasHeader   = $hasHeader;
        
        // Delimiter check
        $detectDelimiter = $this->_detectDelimiter();
        if ($detectDelimiter == 'mixed') {
    		$this->modx->log(modX::LOG_LEVEL_WARN, $this->modx->lexicon('userimport.import_users_log_delimiter_not_detected'));
        } elseif ($this->delimiter !== $detectDelimiter) {
    		$this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_wrong_delimiter_detected').$detectDelimiter);
            return false;
        }
        
        // Enclosure check
        $detectEnclosure = $this->_detectEnclosure();
        if ($detectEnclosure == 'mixed') {
    		$this->modx->log(modX::LOG_LEVEL_WARN, $this->modx->lexicon('userimport.import_users_log_enclosure_not_detected'));
        } elseif ($this->enclosure !== $detectEnclosure) {
    		$this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_wrong_enclosure_detected').$detectEnclosure);
            return false;
        }
        
        return true;
    }

    /**
     * Try to detect the delimiter character of CSV file, by reading the first row.
     *
     * @access private
     * @return string $delimiter || false
     */
    private function _detectDelimiter() {
        $delimiter = false;
        $line = '';
        $line = fgets($this->fileHandle); // Read until first newline

        // Delimiter = ;
        if (strpos($line, ';') !== false && strpos($line, ',') === false) {
            $delimiter = ';';
        // Delimiter = ,
        } elseif (strpos($line, ',') !== false && strpos($line, ';') === false) {
            $delimiter = ',';
        // Delimiter not detected uniquely (could happen if line holds ; and , characters)
        } else {
            $delimiter = 'mixed';
        }
        rewind($this->fileHandle); // Rewind the position of file pointer
        
        return $delimiter;
    }

    /**
     * Try to detect the enclosure character of CSV file, by reading the first row.
     *
     * @access private
     * @return string $enclosure || false
     */
    private function _detectEnclosure() {
        $enclosure = false;
        $line = '';
        $line = fgets($this->fileHandle); // Read until first newline

        // Enclosure = "
        if (strpos($line, '"') !== false && strpos($line, "'") === false) {
            $enclosure = '"';
        // Enclosure = '
        } elseif (strpos($line, "'") !== false && strpos($line, '"') === false) {
            $enclosure = "'";
        // Enclosure not detected uniquely (could happen if line holds " and ' characters)
        } else {
            $enclosure = 'mixed';
        }
        rewind($this->fileHandle); // Rewind the position of file pointer
        
        return $enclosure;
    }

    /**
     * Getter for the unique import key.
     * 
     * @access public
     * @return string The unique import key
     */
    public function getImportKey() {
        return $this->importKey;
    }

    /**
     * Open a file.
     * 
     * @access private
     * @param string $filePath
     * @return mixed file handle || false
     */
    private function _openFile($filePath) { 
        $this->fileHandle = @fopen($filePath, 'r');
        return $this->fileHandle;
    } 

    /**
     * Close a file.
     * 
     * @access private
     * @return void
     */
    private function _closeFile() { 
        if ($this->fileHandle) { 
            @fclose($this->fileHandle); 
        } 
    } 

    /**
     * Get first line of CSV file as field names.
     * 
     * @access private
     * @return void
     */
    private function _getHeader() {
        if ($this->legacyFgetcsv) {
            $this->header = fgetcsv($this->fileHandle, $this->lineLength, $this->delimiter, $this->enclosure); 
        } else {
            $this->header = fgetcsv($this->fileHandle, $this->lineLength, $this->delimiter, $this->enclosure, $this->escape); 
        }
        // Fields that aren't predefined in $this->userFields, are treated as extended user-fields
        $this->extendedFields = array_diff($this->header, $this->userFields);
        if (!empty($this->extendedFields)) {
    		$this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('userimport.import_users_log_extended_detected').implode(',', $this->extendedFields));
        }
    }

    /**
     * Import a batch of users into MODX database.
     * 
     * @access public
     * @param int $batchSize (default: 0) If set to 0, get all the data at once
     * @param array $groups Array of MODX User Group ids
     * @param int $role A MODX User Role id
     * @param bool $autoUsername Automatically use email address as username?
     * @param bool $setImportmarker Write import-markers to extended fields??
     * @return mixed int $importCount || false
     */
    public function importUsers($batchSize = 0, $groups = array(), $role = 0, $autoUsername = false, $setImportmarker = true) {
        $this->batchSize = $batchSize;

        if ($this->hasHeader) {
            $this->_getHeader();
        }

        $newUsers = $this->_getImportUsers();
        if (!$newUsers) {
            return false;
        }

        $this->modx->invokeEvent('onBeforeUserImport', array('groups' => $groups));

        // Main import loop
        $importCount = 0;
        foreach ($newUsers as $rowNumber => $newUser) {
            if ($this->_saveUser($rowNumber, $newUser, $groups, $role, $autoUsername, $setImportmarker)) {
                $importCount++;
            }
        }
        $this->modx->invokeEvent('onAfterUserImport', array('groups' => $groups));
        return $importCount;
    }

    /**
     * Get users data from CSV file. 
     * 
     * @todo Currently we only support CSV files with predifined columns/fields!
     *
     * @access private
     * @return mixed array $importUsers
     */
    private function _getImportUsers() {
            
        $importUsers = array(); 
        
        if ($this->batchSize > 0) {
            $lineCount = 0; 
        } else {
            $lineCount = -1; // loop limit is ignored 
        }
        while ($lineCount < $this->batchSize) {
            
            // Get next row from CSV file
            if ($this->legacyFgetcsv) {
                $row = fgetcsv($this->fileHandle, $this->lineLength, $this->delimiter, $this->enclosure);
            } else {
                $row = fgetcsv($this->fileHandle, $this->lineLength, $this->delimiter, $this->enclosure, $this->escape);
            }
            if (!$row) { break; }
            
            // With header row (field names available)
            if ($this->header) {
                $user = $this->_combineArrays($this->header, $row);
                if ($user) { $importUsers[] = $user; }
            
            // Without header row (no field names available)
            } else {
                $importUsers[] = $this->_combineArraysSeq($this->userFields, $row);
            }

            if ($this->batchSize > 0) {
                $lineCount++;
            }

        } 
        return $importUsers; 
    }

    /**
     * Combines header keys with row values.
     * (CSV file first row is header row)
     *
     * @access private
     * @param array $fields
     * @param array $values 
     * @return mixed array $combined The combined array of one row || false
     */
    private function _combineArrays(array $fields, array $values) {
        
        // Sanity check of row
        if (count($fields) != count($values)) {
    		$this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('userimport.import_users_log_diff_fields_values_count').print_r($values, true));
            return false;
        }

        $combined = array_combine($fields, $values);
        
        // Get elements from $combined which doesnt exist in $this->userFields
        $extended = array();
        foreach ($this->extendedFields as $key) {
            if (array_key_exists($key, $combined) && ($combined[$key] != '')) {
                $extended[$key] = $combined[$key];
                unset($combined[$key]);
            }
        }
        if (!empty($extended)) {
            $combined['extended'] = $extended;
        }
        return $combined;
    }

    /**
     * Combines predefined $userFields array with imported values based on sequence of values.
     * (CSV file has no header row)
     *
     * @access private
     * @param array $fields
     * @param array $values 
     * @return array $combined The combined array of one row
     */
    private function _combineArraysSeq(array $fields, array $values) {
        
        $fieldscount = count($fields);
        $valuescount = count($values);
        
        // More fields than values
        if ($fieldscount > $valuescount) {
            // How many fields are we missing at the end of the $values array?
            $more = $fieldscount - $valuescount;
            // Add empty strings to ensure arrays $fields and $values have same number of elements
            for($i = 0; $i < $more; $i++) {
                $values[] = '';
            }
        
        // More values than fields
        } elseif ($valuescount > $fieldscount) {
            // Slice extra values        
            $values = array_slice($values, 0, $fieldscount);
        }
        
        $combined = array_combine($fields, $values);
        return $combined;
    }

    /**
     * Save a new user + profile.
     * 
     * @access private
     * @param int $rowNumber The row counter
     * @param array $fieldvalues The field values for the new MODX user ($fieldvalues[0] = email, $fieldvalues[1] = fullname)
     * @param array $groups The MODX User Group IDs for the new MODX user
     * @param int $role The MODX User Role ID for the new MODX user
     * @param bool $autoUsername Automatically use email address as username?
     * @param bool $setImportmarker Write import-markers to extended fields??
     * @return boolean
     */
    private function _saveUser($rowNumber, $fieldvalues, $groups, $role, $autoUsername, $setImportmarker) {
        // (array key 0 = row number 1)
        $rowNumber = $rowNumber + 1;

        // email -> required!
        if (empty($fieldvalues['email'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_ns_email'));
            return false;
        }
        if (!$this->validEmail($fieldvalues['email'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_email_invalid').$fieldvalues['email']);
            return false;
        }
        if ($this->emailExists($fieldvalues['email'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_email_ae').$fieldvalues['email']);
            return false;
        }
        if (strlen($fieldvalues['email']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_email_max_len'));
            return false;
        }

        // username -> required!
        if ($autoUsername) {
            $fieldvalues['username'] = $fieldvalues['email'];
        }
        if (empty($fieldvalues['username'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_ns_username'));
            return false;
        }
        if ($this->usernameExists($fieldvalues['username'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_username_ae').$fieldvalues['username']);
            return false;
        }
        if (strlen($fieldvalues['username']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_username_max_len'));
            return false;
        }

        // fullname
        if (!empty($fieldvalues['fullname']) && strlen($fieldvalues['fullname']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_fullname_max_len'));
            return false;
        }

        // phone
        if (!empty($fieldvalues['phone']) && strlen($fieldvalues['phone']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_phone_max_len'));
            return false;
        }

        // mobilephone
        if (!empty($fieldvalues['mobilephone']) && strlen($fieldvalues['mobilephone']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_mobilephone_max_len'));
            return false;
        }

        // dob -> needs check!
        if (!empty($fieldvalues['dob']) || $fieldvalues['dob'] == '0') {
            // dob can be provided as UNIX timestamp or 
            // any valid php date format
            // -> but always saved and handled as UNIX timestamp!
            if (!$this->validTimestamp($fieldvalues['dob']) && !$this->validDate($fieldvalues['dob'])) {
        		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_dob_invalid').$fieldvalues['dob']);
                return false;
            }
            // if date format convert to timestamp
            if ($this->validDate($fieldvalues['dob'])) {
                $fieldvalues['dob'] = strtotime($fieldvalues['dob']);
            }
        }
        
        // gender -> needs check!
        if (!empty($fieldvalues['gender']) && !$this->validGender($fieldvalues['gender'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_gender_invalid').$fieldvalues['gender']);
            return false;
        }

        // address
        if (!empty($fieldvalues['address']) && strlen($fieldvalues['address']) > 65535) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_address_max_len'));
            return false;
        }

        // country
        if (!empty($fieldvalues['country']) && strlen($fieldvalues['country']) > 255) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_country_max_len'));
            return false;
        }

        // city
        if (!empty($fieldvalues['city']) && strlen($fieldvalues['city']) > 255) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_city_max_len'));
            return false;
        }

        // state
        if (!empty($fieldvalues['state']) && strlen($fieldvalues['state']) > 25) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_state_max_len'));
            return false;
        }

        // zip
        if (!empty($fieldvalues['zip']) && strlen($fieldvalues['zip']) > 25) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_zip_max_len'));
            return false;
        }

        // fax
        if (!empty($fieldvalues['fax']) && strlen($fieldvalues['fax']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_fax_max_len'));
            return false;
        }

        // photo
        if (!empty($fieldvalues['photo']) && strlen($fieldvalues['photo']) > 255) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_photo_max_len'));
            return false;
        }

        // comment
        if (!empty($fieldvalues['comment']) && strlen($fieldvalues['comment']) > 65535) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_comment_max_len'));
            return false;
        }

        // website
        if (!empty($fieldvalues['website']) && strlen($fieldvalues['website']) > 255) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_website_max_len'));
            return false;
        }

        // extended (can be array or json string!)
        if (!empty($fieldvalues['extended']) && is_string($fieldvalues['extended'])) {

            // Try to convert json to array (returns NULL if not)
            $extendedArray = $this->jsonToArray($fieldvalues['extended']);
            // Check if conversion was successfull
            if (!is_array($extendedArray)) {
        		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_extended_invalid_json').$fieldvalues['extended']);
                return false;
            }
            $fieldvalues['extended'] = $extendedArray;
        }

        $userSaved = false;

        // New modUser
        $user = $this->modx->newObject('modUser');
        
        // Use provided password or auto-generate one
        $password = $this->_setPassword($user, $fieldvalues, $rowNumber);
        
        // Add modUser -> required fields
        $user->set('username', $fieldvalues['username']);
		$user->set('password', $password);
		$user->set('active',   1);
		$user->set('blocked',  0);
		
		// Add modUserProfile -> required fields
        $userProfile = $this->modx->newObject('modUserProfile');
        
		// Add import info to extended profile field (if option is activated)
		$importInfo = array();
		if ($setImportmarker) {
            $importInfo = array(
                'UserImport' => array(
                    'Date' => strftime('%Y-%m-%d %H:%M:%S'),
                    'Key'  => $this->importKey,
                )
            );
		}
        
        // Add extended fields (combined with import info) if any
        if (!empty($fieldvalues['extended']) && is_array($fieldvalues['extended'])) {
            $fieldvalues['extended'] = array_merge($fieldvalues['extended'], $importInfo);
        } else {
            $fieldvalues['extended'] = $importInfo;
        }
        
        $userProfile->fromArray($fieldvalues);
        $user->addOne($userProfile);
        
		if ($user->save()) {
            $userSaved = true;
            $userId = $user->get('id'); // preserve id of new user for later use
            
            // Add user to MODX user group and assign role
            if (isset($groups) && is_array($groups) && !empty($groups)) {
                foreach ($groups as $group) {
                    if (!$user->joinGroup(intval($group), intval($role))) {
                        $userSaved = false;
                        break;
                    }
                }
            }
		}
		
		if (!$userSaved) {
            // Rollback if one of the savings failed!
            $user->remove();
            $this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_err_user_failed').$fieldvalues['username'].' ('.$fieldvalues['email'].')');
		} else {
    		$this->modx->log(modX::LOG_LEVEL_INFO, '-> '.$this->modx->lexicon('userimport.import_users_row').$rowNumber.' '.$this->modx->lexicon('userimport.import_users_log_imported_user').$fieldvalues['username'].' ('.$fieldvalues['email'].')');
		}
		return $userSaved;
    }
    
    /**
     * Looks for a field called "password" in the import file, validates it, and defaults to an auto-generated password
     *
     * @access private
     * @param modUser $user
     * @param array $fieldvalues
     * @param int $rowNumber
     * @return bool|string
     */
    private function _setPassword(modUser &$user, $fieldvalues, $rowNumber) {
        $generatedPasswordLength = (integer)$this->modx->getOption('password_generated_length', null, 8);
        if (!empty($fieldvalues['password']) && ($providedPassword = $this->_validateProvidedPassword($fieldvalues['password'], $rowNumber))) {
            $password = $providedPassword;
        } else {
            $password = $user->generatePassword($generatedPasswordLength);
        }
        return $password;
    }

    /**
     * Check that the password provided in the import file is suitable for use
     *
     * @access private
     * @param $password
     * @param $rowNumber
     * @return bool|string Returns false if not valid
     */
    private function _validateProvidedPassword($password, $rowNumber) {
        $minPasswordLength = (integer)$this->modx->getOption('password_min_length', null, 8);
        if (strlen($password) < $minPasswordLength) {
            $this->modx->log(modX::LOG_LEVEL_INFO, '&nbsp;&nbsp;&nbsp;*)'.$this->modx->lexicon('userimport.import_users_log_password_autogenerated').' '.$this->modx->lexicon('userimport.import_users_log_password_len').$minPasswordLength);
            return false;
        }
        return $password;
    }
    
    /**
     * Check if a username already exists.
     * 
     * @access public
     * @param string $username
     * @return mixed ID of MODX user or false
     */
    public function usernameExists($username) {
		$user = $this->modx->getObject('modUser', array('username' => $username));
		if (is_object($user)) {
    		return $user->get('id');
		} else {
    		return false;
		}
    }
    
    /**
     * Check if an email address already exists.
     * 
     * @access public
     * @param string $email
     * @return mixed ID of MODX user or false
     */
    public function emailExists($email) {
		$userProfile = $this->modx->getObject('modUserProfile', array('email' => $email));
		if (is_object($userProfile)) {
    		return $userProfile->get('internalKey');
		} else {
    		return false;
		}
    }

    /**
     * Checks if we have a CSV mime-type.
     *
     * @access public
     * @param string $mimetype The mime-type to check
     * @return boolean $iscsv
     */
    public function csvMimeType($mimetype) {
        $csv_mimetypes = array(
            'text/csv',
            'text/plain',
            'application/csv',
            'text/comma-separated-values',
            'application/excel',
            'application/vnd.ms-excel',
            'application/vnd.msexcel',
            'text/anytext',
            'application/octet-stream',
            'application/txt',
            'application/download',        
        );
        if (in_array($mimetype, $csv_mimetypes)) {
            return true;
        }
        return false;
    }
    
    /**
     * Checks if we have a valid email address.
     *
     * @access public
     * @param string $email The email address to check
     * @return boolean
     */
    public function validEmail($email) {

        // Validate length and @
        $pattern = "^[^@]{1,64}\@[^\@]{1,255}$";
        $condition = $this->config['use_multibyte'] ? @mb_ereg($pattern, $email) : @ereg($pattern, $email);
        if (!$condition) {
            return false;
        }

        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            $pattern = "^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$";
            $condition = $this->config['use_multibyte'] ? @mb_ereg($pattern, $local_array[$i]) : @ereg($pattern, $local_array[$i]);
            if (!$condition) {
                return false;
            }
        }
        // Validate domain name
        $pattern = "^\[?[0-9\.]+\]?$";
        $condition = $this->config['use_multibyte'] ? @mb_ereg($pattern, $email_array[1]) : @ereg($pattern, $email_array[1]);
        if (!$condition) {
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false;
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                $pattern = "^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$";
                $condition = $this->config['use_multibyte'] ? @mb_ereg($pattern, $domain_array[$i]) : @ereg($pattern, $domain_array[$i]);
                if (!$condition) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Checks if we have a valid UNIX timestamp.
     *
     * @access public
     * @param string $timestamp The timestamp to check.
     * @return boolean
     */
    public function validTimestamp($timestamp) {
    	$check = (is_int($timestamp) OR is_float($timestamp))
    		? $timestamp
    		: (string) (int) $timestamp;
     
    	return ($check === $timestamp)
        	AND ( (int) $timestamp <=  PHP_INT_MAX)
        	AND ( (int) $timestamp >= ~PHP_INT_MAX);
    }

    /**
     * Checks if we have a valid date.
     *
     * @access public
     * @param string $date The date string to check.
     * @return boolean
     */
    function validDate($date) {
        try {
            $dt = new DateTime(trim($date));
        }
        catch(Exception $e) {
            return false;
        }
        
        $month = $dt->format('m');
        $day   = $dt->format('d');
        $year  = $dt->format('Y');
        return checkdate($month, $day, $year);
    }

    /**
     * Checks if we have a valid gender.
     * (integer with value 1 = male, 2 = female, 3 = other)
     *
     * @access public
     * @param string $gender The value to check
     * @return boolean
     */
    public function validGender($gender) {
        if ($gender != '1' && $gender != '2' && $gender != '3') {
            return false;
        }
        return true;
    }

    /**
     * Converts json string to array.
     *
     * @access public
     * @param string $json The string to convert
     * @return mixed array || NULL
     */
    public function jsonToArray($json) {
        return json_decode($json, true);
    }
}
