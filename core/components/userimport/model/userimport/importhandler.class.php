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
        'username',    // varchar 100
        'email',       // varchar 100
        'fullname',    // varchar 100
        'dob',         // int 10 (unix timestamp)
        'gender',      // int 1 (1 = male, 2 = female, 3 = other)
        'address',     // text
        'country',     // varchar 255
        'city',        // varchar 255
        'state',       // varchar 25
        'zip',         // varchar 25
        'phone',       // varchar 100
        'mobilephone', // varchar 100
        'fax',         // varchar 100
        'website',     // varchar 255
        'comment',     // text
    );


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
     * @param bool $hasHeader (default: false)
     * @param string $delimiter (default: ,)
     * @param string $enclosure (default: ")
     * @param string $escape (default: \)
     * @param int $lineLength (default: 4096)
     * @return boolean
     */
    public function init($filePath, $hasHeader = false, $delimiter = ',', $enclosure = '"', $escape = '\\', $lineLength = 4096) {
        if ($this->_openFile($filePath) == false) {
            return false;
        }
        $this->delimiter   = $delimiter;
        $this->enclosure   = $enclosure; 
        $this->escape      = $escape; 
        $this->lineLength  = $lineLength; 
        $this->hasHeader   = $hasHeader; 
        return true;
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
        $this->header = fgetcsv($this->fileHandle, $this->lineLength, $this->delimiter, $this->enclosure, $this->escape); 
    }

    /**
     * Import a batch of users into MODX database.
     * 
     * @access public
     * @param int $batchSize (default: 0) If set to 0, get all the data at once
     * @param array $groups Array of MODX User Group ids
     * @param int $role A MODX User Role id
     * @param bool $autoUsername Automatically use email address as username?
     * @return mixed int $importCount || false
     */
    public function importUsers($batchSize = 0, $groups = array(), $role = 0, $autoUsername = false) {
        $this->batchSize = $batchSize;

        if ($this->hasHeader) {
            $this->_getHeader();
        }

        $newUsers = $this->_getImportUsers();
        if (!$newUsers) {
            return false;
        }

        // Main impot loop
        $importCount = 0;
        foreach ($newUsers as $row => $newUser) {
            if ($this->_saveUser($row, $newUser, $groups, $role, $autoUsername)) {
                $importCount++;
            }
        }
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
        while ($lineCount < $this->batchSize && 
            ($row = fgetcsv($this->fileHandle, $this->lineLength, $this->delimiter, $this->enclosure, $this->escape)) !== false) { 

            // assign row values to user fields array
            $importUsers[] = $this->_combineArrays($this->userFields, $row);

            if ($this->batchSize > 0) {
                $lineCount++;
            }

        } 
        return $importUsers; 
    }

    /**
     * Save a new user + profile.
     * 
     * @access private
     * @param array $fieldvalues The field values for the new MODX user ($fieldvalues[0] = email, $fieldvalues[1] = fullname)
     * @param array $groups The MODX User Group IDs for the new MODX user
     * @param int $role The MODX User Role ID for the new MODX user
     * @param bool $autoUsername Automatically use email address as username?
     * @return boolean
     */
    private function _saveUser($row, $fieldvalues, $groups, $role, $autoUsername) {
        // (array key 0 = row number 1)
        $row = $row + 1;
        
        // username -> required!
        if ($autoUsername) {
            $fieldvalues['username'] = $fieldvalues['email'];
        }
        if (empty($fieldvalues['username'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_ns_username'));
            return false;
        }
        if ($this->usernameExists($fieldvalues['username'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_username_ae').$fieldvalues['username']);
            return false;
        }
        if (strlen($fieldvalues['username']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_username_max_len'));
            return false;
        }
        
        // email -> required!
        if (empty($fieldvalues['email'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_ns_email'));
            return false;
        }
        if (!$this->validEmail($fieldvalues['email'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_email_invalid').$fieldvalues['email']);
            return false;
        }
        if ($this->emailExists($fieldvalues['email'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_email_ae').$fieldvalues['email']);
            return false;
        }
        if (strlen($fieldvalues['email']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_email_max_len'));
            return false;
        }
        
        // fullname
        if (strlen($fieldvalues['fullname']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_fullname_max_len'));
            return false;
        }

        // dob -> needs check if provided!
        if (!empty($fieldvalues['dob']) || $fieldvalues['dob'] == '0') {
            // dob can be provided as UNIX timestamp or 
            // any valid php date format
            // -> but always saved and handled as UNIX timestamp!
            if (!$this->validTimestamp($fieldvalues['dob']) && !$this->validDate($fieldvalues['dob'])) {
        		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_dob_invalid').$fieldvalues['dob']);
                return false;
            }
            // if date format convert to timestamp
            if ($this->validDate($fieldvalues['dob'])) {
                $fieldvalues['dob'] = strtotime($fieldvalues['dob']);
            }
        }
        
        // gender -> needs check if provided!
        if (!empty($fieldvalues['gender']) && !$this->validGender($fieldvalues['gender'])) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_gender_invalid').$fieldvalues['gender']);
            return false;
        }

        // address
        if (strlen($fieldvalues['address']) > 65535) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_address_max_len'));
            return false;
        }

        // country
        if (strlen($fieldvalues['country']) > 255) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_country_max_len'));
            return false;
        }

        // city
        if (strlen($fieldvalues['city']) > 255) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_city_max_len'));
            return false;
        }

        // state
        if (strlen($fieldvalues['state']) > 25) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_state_max_len'));
            return false;
        }

        // zip
        if (strlen($fieldvalues['zip']) > 25) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_zip_max_len'));
            return false;
        }

        // phone
        if (strlen($fieldvalues['phone']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_phone_max_len'));
            return false;
        }

        // mobilephone
        if (strlen($fieldvalues['mobilephone']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_mobilephone_max_len'));
            return false;
        }

        // fax
        if (strlen($fieldvalues['fax']) > 100) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_fax_max_len'));
            return false;
        }

        // website
        if (strlen($fieldvalues['website']) > 255) {
    		$this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_website_max_len'));
            return false;
        }

        $userSaved = false;
        
        // New modUser
        $user = $this->modx->newObject('modUser');
        $password = $user->generatePassword(8);
        
        // Add modUser -> required fields
        $user->set('username', $fieldvalues['username']);
		$user->set('password', $password);
		$user->set('active',   1);
		$user->set('blocked',  0);
		
		// Add modUserProfile -> required fields
        $userProfile = $this->modx->newObject('modUserProfile');
        $userProfile->set('email',       $fieldvalues['email']);
        $userProfile->set('fullname',    $fieldvalues['fullname']);
        
		// Add modUserProfile -> optional fields
        $userProfile->set('dob',         $fieldvalues['dob']         ? $fieldvalues['dob']         : '0');
        $userProfile->set('gender',      $fieldvalues['gender']      ? $fieldvalues['gender']      : '0');
        $userProfile->set('address',     $fieldvalues['address']     ? $fieldvalues['address']     : '');
        $userProfile->set('country',     $fieldvalues['country']     ? $fieldvalues['country']     : '');
        $userProfile->set('city',        $fieldvalues['city']        ? $fieldvalues['city']        : '');
        $userProfile->set('state',       $fieldvalues['state']       ? $fieldvalues['state']       : '');
        $userProfile->set('zip',         $fieldvalues['zip']         ? $fieldvalues['zip']         : '');
        $userProfile->set('phone',       $fieldvalues['phone']       ? $fieldvalues['phone']       : '');
        $userProfile->set('mobilephone', $fieldvalues['mobilephone'] ? $fieldvalues['mobilephone'] : '');
        $userProfile->set('fax',         $fieldvalues['fax']         ? $fieldvalues['fax']         : '');
        $userProfile->set('website',     $fieldvalues['website']     ? $fieldvalues['website']     : '');

		// Add modUserProfile -> import info to comment field
        $importedon = strftime('%Y-%m-%d %H:%M:%S');
        $userProfile->set('comment', 'Imported: '.$importedon.' ('.$this->importKey.')');
        
        $user->addOne($userProfile);
        
		if ($user->save()) {
            $userSaved = true;
            $userId = $user->get('id'); // preserve id of new user for later use
            
            // Add user to MODX user group and assign role
            if (is_array($groups) && !empty($groups)) {
                foreach ($groups as $group) {
                    // With the current joinGroup method of the MODX moduser.class.php it's not possible 
                    // to programmatically add a user to a group without assigning a role.
                    // So we need to use our own modified joingGroup method until the bug in MODX is fixed!
                    if (!$this->joinGroup(intval($group), intval($role), $user)) {
                        $userSaved = false;
                        break;
                    }
                }
            }
		}
		
		if (!$userSaved) {
            // Rollback if one of the savings failed!
            $user->remove();
            $this->modx->log(modX::LOG_LEVEL_WARN, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_err_user_failed').$fieldvalues['username'].' ('.$fieldvalues['email'].')');
		} else {
    		$this->modx->log(modX::LOG_LEVEL_INFO, '-> '.$this->modx->lexicon('userimport.import_users_row').$row.' '.$this->modx->lexicon('userimport.import_users_log_imported_user').$fieldvalues['username'].' ('.$fieldvalues['email'].')');
		}
		return $userSaved;
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
     * Combine arrays with different field counts.
     *
     * @access private
     * @param array $fields
     * @param array $values 
     * @return array $combined The combined array
     */
    private function _combineArrays($fields, $values) {
        $acount = count($fields);
        $bcount = count($values);
        
        $size = ($acount > $bcount) ? $bcount : $acount;
        $fields = array_slice($fields, 0, $size);
        $values = array_slice($values, 0, $size);
        $combined = array_combine($fields, $values);
        
        return $combined;
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
     * Join a User Group, and optionally assign a Role.
     * (This method is borrowed from MODX moduser.class.php and 
     * modified, as the original method has some bugs in 2.3.1.
     * All references to xpdo are replaced with modx.)
     *
     * @access public
     * @param mixed $groupId Either the name or ID of the User Group to join.
     * @param mixed $roleId Optional. Either the name or ID of the Role to assign to for the group.
     * @param modUser $user The user object (Needed because $this is not a modUser object as in moduser.class.php)
     * @return boolean True if successful.
     */
    public function joinGroup($groupId, $roleId = null, $user) {
        $joined = false;

        $groupPk = is_string($groupId) ? array('name' => $groupId) : $groupId;
        /** @var modUserGroup $userGroup */
        $userGroup = $this->modx->getObject('modUserGroup', $groupPk);
        if (empty($userGroup)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'User Group not found with key: '.$groupId);
            return $joined;
        }

        /** @var modUserGroupRole $role */
        if (!empty($roleId)) {
            $rolePk = is_string($roleId) ? array('name' => $roleId) : $roleId;
            $role = $this->modx->getObject('modUserGroupRole', $rolePk);
            if (empty($role)) {
                //$this->xpod->log(xPDO::LOG_LEVEL_ERROR,'Role not found with key: '.$role);
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Role not found with key: '.$roleId); // bugfix
                return $joined;
            }
        }

        /** @var modUserGroupMember $member */
        $member = $this->modx->getObject('modUserGroupMember', array(
            //'member' => $this->get('id'), // Original line from moduser.class.php
            'member' => $user->get('id'),   // Needed because $this is not a modUser object as in moduser.class.php
            'user_group' => $userGroup->get('id'),
        ));
        if (empty($member)) {
            $rank = count($user->getMany('UserGroupMembers'));
            $member = $this->modx->newObject('modUserGroupMember');
            //$member->set('member', $this->get('id')); // Original line from moduser.class.php
            $member->set('member', $user->get('id'));   // Needed because $this is not a modUser object as in moduser.class.php
            $member->set('user_group', $userGroup->get('id'));
            $member->set('rank', $rank);
            if (!empty($role)) {
                $member->set('role', $role->get('id'));
            }
            // -- additional code: allow joining User Groups without assigning a role
            if ($roleId === '0' || $roleId === 0) { 
                $member->set('role', 0);
            }
            // -- end: additional code
            $joined = $member->save();
            if (!$joined) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'An unknown error occurred preventing adding the User to the User Group.');
            } else {
                //unset($_SESSION["modx.user.{$this->get('id')}.userGroupNames"]); // Original line from moduser.class.php
                unset($_SESSION["modx.user.{$user->get('id')}.userGroupNames"]);   // Needed because $this is not a modUser object as in moduser.class.php
            }
        } else {
            $joined = true;
        }
        return $joined;
    }
}
