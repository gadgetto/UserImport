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

namespace Bitego\UserImport;

use MODX\Revolution\modX;
use MODX\Revolution\modUser;
use MODX\Revolution\modUserProfile;
use MODX\Revolution\modChunk;
use MODX\Revolution\Mail\modMail;
use Soundasleep\Html2Text;

/**
 * ImportHandler class handles batch import of users into MODX users database
 * and batch assign MODX user groups + role.
 *
 * @package userimport
 */

class ImportHandler
{
    /** @var modX $modx A reference to the modX object */
    public $modx = null;

    /** @var array $config An array of config values */
    public $config = [];

    /** @var string $importKey A unique string to identify a group of imported users (uik+timestamp) */
    public $importKey = '';

    /** @var resource $fileHandle A valid file pointer to a file successfully opened */
    public $fileHandle = null;

    /** @var int $lineLength Must be greater than the longest line (in characters) to be found in the CSV file */
    public $lineLength = 0;

    /** @var string $delimiter The field delimiter (one character only) */
    public $delimiter = '';

    /** @var string $enclosure The field enclosure character (one character only) */
    public $enclosure = '';

    /** @var string $escape The escape character (one character only). Defaults to backslash. */
    public $escape = '';

    /** @var boolean $hasHeader If the first row includes field names */
    public $hasHeader = '';

    /** @var array $header The first row (field names) */
    public $header = [];

    /** @var int $batchSize Number of users to be imported in one batch */
    public $batchSize = 0;

    /** @var array $userFields The internal MODX user field names (from modUser and modProfile) */
    public $userFields = [
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
    ];

    /** @var array $extendedFields The extended user-field names (modProfile) */
    public $extendedFields = [];

    /**
     * Constructor for ImportHandler object.
     *
     * @access public
     * @param modX &$modx A reference to the modX object
     */
    public function __construct(modX &$modx, array $config = [])
    {
        $this->modx = &$modx;
        $this->modx->lexicon->load('user', 'userimport:default');
        $this->importKey = uniqid('uik', true);
        $this->config = array_merge([
            'use_multibyte' => (bool)$this->modx->getOption('use_multibyte', null, false),
            'encoding' => $this->modx->getOption('modx_charset', null, 'UTF-8'),
        ], $config);
    }

    /**
     * Destructor for ImportHandler object.
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->closeFile();
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
    public function init(
        string $filePath,
        bool $hasHeader = true,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\',
        int $lineLength = 4096
    ) {
        if ($this->openFile($filePath) == false) {
            return false;
        }

        $this->delimiter  = $delimiter;
        $this->enclosure  = $enclosure;
        $this->escape     = $escape;
        $this->lineLength = $lineLength;
        $this->hasHeader  = $hasHeader;

        $initCheck = true;

        // Delimiter check
        $detectDelimiter = $this->detectDelimiter();
        if ($detectDelimiter == 'mixed') {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                $this->modx->lexicon('userimport.import_users_log_delimiter_not_detected')
            );
            $initCheck = false;
        } elseif ($this->delimiter !== $detectDelimiter) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_wrong_delimiter_detected') . $detectDelimiter
            );
            $initCheck = false;
        }

        // Enclosure check
        $detectEnclosure = $this->detectEnclosure();
        if ($detectEnclosure == 'mixed') {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                $this->modx->lexicon('userimport.import_users_log_enclosure_not_detected')
            );
            $initCheck = false;
        } elseif ($this->enclosure !== $detectEnclosure) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_wrong_enclosure_detected') . $detectEnclosure
            );
            $initCheck = false;
        }
        return $initCheck;
    }

    /**
     * Try to detect the delimiter character of CSV file, by reading the first row.
     *
     * @access private
     * @return mixed $delimiter | false
     */
    private function detectDelimiter()
    {
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
     * @return mixed $enclosure | false
     */
    private function detectEnclosure()
    {
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
    public function getImportKey()
    {
        return $this->importKey;
    }

    /**
     * Open a file.
     *
     * @access private
     * @param string $filePath
     * @return mixed file handle || false
     */
    private function openFile($filePath)
    {
        $this->fileHandle = @fopen($filePath, 'r');
        return $this->fileHandle;
    }

    /**
     * Close a file.
     *
     * @access private
     * @return void
     */
    private function closeFile()
    {
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
    private function getHeader()
    {
        // Position of file pointer to first line
        rewind($this->fileHandle);
        $this->header = fgetcsv(
            $this->fileHandle,
            $this->lineLength,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );
        // Fields that aren't predefined in $this->userFields, are treated as extended user-fields
        $this->extendedFields = array_diff($this->header, $this->userFields);
        if (!empty($this->extendedFields)) {
            $this->modx->log(
                modX::LOG_LEVEL_INFO,
                $this->modx->lexicon('userimport.import_users_log_extended_detected') .
                implode(',', $this->extendedFields)
            );
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
     * @param bool $setImportmarker Write import-markers to extended fields?
     * @param bool $notifyUsers Notify imported users via email?
     * @param string $mailSubject The subject of the notification mail
     * @param string $mailBody The body of the notification mail
     * @return mixed int $importCount || false
     */
    public function importUsers(
        int $batchSize = 0,
        array $groups = [],
        int $role = 0,
        bool $autoUsername = false,
        bool $setImportmarker = true,
        bool $notifyUsers = false,
        string $mailSubject = '',
        string $mailBody = ''
    ) {
        $this->batchSize = $batchSize;
        if ($this->hasHeader) {
            $this->getHeader();
        }

        $newUsers = $this->getImportUsers();
        if (!$newUsers) {
            return false;
        }

        $this->modx->invokeEvent('onBeforeUserImport', ['groups' => $groups]);

        // Main import loop
        $importCount = 0;
        foreach ($newUsers as $rowNumber => $newUser) {
            if (
                $this->saveUser(
                    $rowNumber,
                    $newUser,
                    $groups,
                    $role,
                    $autoUsername,
                    $setImportmarker,
                    $notifyUsers,
                    $mailSubject,
                    $mailBody
                )
            ) {
                $importCount++;
            }
        }
        $this->modx->invokeEvent('onAfterUserImport', ['groups' => $groups]);
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
    private function getImportUsers()
    {
        $importUsers = [];

        if ($this->batchSize > 0) {
            $lineCount = 0;
        } else {
            $lineCount = -1; // loop limit is ignored
        }
        while ($lineCount < $this->batchSize) {
            // Get next row from CSV file
            $row = fgetcsv($this->fileHandle, $this->lineLength, $this->delimiter, $this->enclosure, $this->escape);
            if (!$row) {
                break;
            }

            // With header row (field names available)
            if ($this->header) {
                $user = $this->combineArrays($this->header, $row);
                if ($user) {
                    $importUsers[] = $user;
                }
            // Without header row (no field names available)
            } else {
                $importUsers[] = $this->combineArraysSeq($this->userFields, $row);
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
    private function combineArrays(array $fields, array $values)
    {
        // Sanity check of row
        if (count($fields) != count($values)) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                $this->modx->lexicon('userimport.import_users_log_diff_fields_values_count') . print_r($values, true)
            );
            return false;
        }

        $combined = array_combine($fields, $values);

        // Get elements from $combined which doesnt exist in $this->userFields
        $extended = [];
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
    private function combineArraysSeq(array $fields, array $values)
    {
        $fieldscount = count($fields);
        $valuescount = count($values);

        // More fields than values
        if ($fieldscount > $valuescount) {
            // How many fields are we missing at the end of the $values array?
            $more = $fieldscount - $valuescount;
            // Add empty strings to ensure arrays $fields and $values have same number of elements
            for ($i = 0; $i < $more; $i++) {
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
     * @param array $fieldvalues The field values for the new MODX user
     *        ($fieldvalues[0] = email, $fieldvalues[1] = fullname)
     * @param array $groups The MODX User Group IDs for the new MODX user
     * @param int $role The MODX User Role ID for the new MODX user
     * @param bool $autoUsername Automatically use email address as username?
     * @param bool $setImportmarker Write import-markers to extended fields?
     * @param bool $notifyUsers Notify imported users via email?
     * @param string $mailSubject The subject of the notification mail
     * @param string $mailBody The body of the notification mail
     * @return boolean
     */
    private function saveUser(
        $rowNumber,
        $fieldvalues,
        $groups,
        $role,
        $autoUsername,
        $setImportmarker,
        $notifyUsers,
        $mailSubject,
        $mailBody
    ) {
        // (array key 0 = row number 1)
        $rowNumber = $rowNumber + 1;

        // email -> required!
        if (empty($fieldvalues['email'])) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_ns_email')
            );
            return false;
        }
        if (!$this->validEmail($fieldvalues['email'])) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_email_invalid') . $fieldvalues['email']
            );
            return false;
        }
        if ($this->emailExists($fieldvalues['email'])) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_email_ae') . $fieldvalues['email']
            );
            return false;
        }
        if (strlen($fieldvalues['email']) > 100) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_email_max_len')
            );
            return false;
        }

        // username -> required!
        if ($autoUsername) {
            $fieldvalues['username'] = $fieldvalues['email'];
        }
        if (empty($fieldvalues['username'])) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_ns_username')
            );
            return false;
        }
        if ($this->usernameExists($fieldvalues['username'])) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_username_ae') . $fieldvalues['username']
            );
            return false;
        }
        if (strlen($fieldvalues['username']) > 100) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_username_max_len')
            );
            return false;
        }

        // fullname
        if (!empty($fieldvalues['fullname']) && strlen($fieldvalues['fullname']) > 100) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_fullname_max_len')
            );
            return false;
        }

        // phone
        if (!empty($fieldvalues['phone']) && strlen($fieldvalues['phone']) > 100) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_phone_max_len')
            );
            return false;
        }

        // mobilephone
        if (!empty($fieldvalues['mobilephone']) && strlen($fieldvalues['mobilephone']) > 100) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_mobilephone_max_len')
            );
            return false;
        }

        // dob -> needs check!
        if (!empty($fieldvalues['dob']) || $fieldvalues['dob'] == '0') {
            // dob can be provided as UNIX timestamp or any valid php date format
            // https://www.php.net/manual/en/datetime.formats.php
            // -> but always saved and handled as UNIX timestamp!
            if (!$this->validTimestamp($fieldvalues['dob']) && !$this->validBirthDate($fieldvalues['dob'])) {
                $this->modx->log(
                    modX::LOG_LEVEL_WARN,
                    '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                    $this->modx->lexicon('userimport.import_users_log_err_dob_invalid') . $fieldvalues['dob']
                );
                return false;
            }
            // if date format convert to timestamp
            if ($this->validBirthDate($fieldvalues['dob'])) {
                $fieldvalues['dob'] = strtotime($fieldvalues['dob']);
            }
        }

        // gender -> needs check!
        if (!empty($fieldvalues['gender']) && !$this->validGender($fieldvalues['gender'])) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_gender_invalid') . $fieldvalues['gender']
            );
            return false;
        }

        // address
        if (!empty($fieldvalues['address']) && strlen($fieldvalues['address']) > 65535) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_address_max_len')
            );
            return false;
        }

        // country
        if (!empty($fieldvalues['country']) && strlen($fieldvalues['country']) > 255) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_country_max_len')
            );
            return false;
        }

        // city
        if (!empty($fieldvalues['city']) && strlen($fieldvalues['city']) > 255) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_city_max_len')
            );
            return false;
        }

        // state
        if (!empty($fieldvalues['state']) && strlen($fieldvalues['state']) > 25) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_state_max_len')
            );
            return false;
        }

        // zip
        if (!empty($fieldvalues['zip']) && strlen($fieldvalues['zip']) > 25) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_zip_max_len')
            );
            return false;
        }

        // fax
        if (!empty($fieldvalues['fax']) && strlen($fieldvalues['fax']) > 100) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_fax_max_len')
            );
            return false;
        }

        // photo
        if (!empty($fieldvalues['photo']) && strlen($fieldvalues['photo']) > 255) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_photo_max_len')
            );
            return false;
        }

        // comment
        if (!empty($fieldvalues['comment']) && strlen($fieldvalues['comment']) > 65535) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_comment_max_len')
            );
            return false;
        }

        // website
        if (!empty($fieldvalues['website']) && strlen($fieldvalues['website']) > 255) {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_website_max_len')
            );
            return false;
        }

        // extended (can be array or json string!)
        if (!empty($fieldvalues['extended']) && is_string($fieldvalues['extended'])) {
            // Try to convert json to array (returns NULL if not)
            $extendedArray = $this->jsonToArray($fieldvalues['extended']);
            // Check if conversion was successfull
            if (!is_array($extendedArray)) {
                $this->modx->log(
                    modX::LOG_LEVEL_WARN,
                    '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                    $this->modx->lexicon('userimport.import_users_log_err_extended_invalid_json') .
                    $fieldvalues['extended']
                );
                return false;
            }
            $fieldvalues['extended'] = $extendedArray;
        }

        $userSaved = false;

        // New modUser
        $user = $this->modx->newObject(modUser::class);

        // Use provided password or auto-generate one
        $password = $this->setPassword($user, $fieldvalues, $rowNumber);

        // Add modUser -> required fields
        $user->set('username', $fieldvalues['username']);
        $user->set('password', $password);
        $user->set('active', 1);
        $user->set('blocked', 0);

        // Add modUserProfile -> required fields
        $userProfile = $this->modx->newObject(modUserProfile::class);

        // Add import info to extended profile field (if option is activated)
        $importInfo = [];
        if ($setImportmarker) {
            $importInfo = array(
                'UserImport' => array(
                    'Date' => date('Y-m-d H:i:s'),
                    'Key' => $this->importKey,
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
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_err_user_failed') . $fieldvalues['username'] .
                ' (' . $fieldvalues['email'] . ')'
            );
        } else {
            $this->modx->log(
                modX::LOG_LEVEL_INFO,
                '-> ' . $this->modx->lexicon('userimport.import_users_row') . $rowNumber . ' ' .
                $this->modx->lexicon('userimport.import_users_log_imported_user') . $fieldvalues['username'] .
                ' (' . $fieldvalues['email'] . ')'
            );
            // Send a notification email if enabled
            if ($notifyUsers) {
                // Returns an array (sent, error_info)
                $notificationStatus = $this->sendNotificationEmail(
                    $user,
                    $userProfile,
                    $password,
                    $mailSubject,
                    $mailBody
                );
                if ($notificationStatus['sent'] == true) {
                    $this->modx->log(modX::LOG_LEVEL_INFO, '&nbsp;&nbsp;&nbsp;Notification mail sent.');
                } else {
                    $this->modx->log(
                        modX::LOG_LEVEL_ERROR,
                        '&nbsp;&nbsp;&nbsp;Could not send notification mail: ' . $notificationStatus['erro_info']
                    );
                }
            }
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
    private function setPassword(modUser &$user, $fieldvalues, $rowNumber)
    {
        $generatedPasswordLength = (int)$this->modx->getOption('password_generated_length', null, 8);
        if (
            !empty($fieldvalues['password']) &&
            ($providedPassword = $this->validateProvidedPassword($fieldvalues['password'], $rowNumber))
        ) {
            $password = $providedPassword;
        } else {
            $password = $user->generatePassword($generatedPasswordLength);
        }
        return $password;
    }

    /**
     * Check that the password provided in the import file is suitable for use.
     *
     * @access private
     * @param string $password
     * @param int $rowNumber
     * @return bool|string Returns false if not valid
     */
    private function validateProvidedPassword($password, $rowNumber)
    {
        $minPasswordLength = (int)$this->modx->getOption('password_min_length', null, 8);
        if (strlen($password) < $minPasswordLength) {
            $this->modx->log(
                modX::LOG_LEVEL_INFO,
                '&nbsp;&nbsp;&nbsp;*)' .
                $this->modx->lexicon('userimport.import_users_log_password_autogenerated') . ' ' .
                $this->modx->lexicon('userimport.import_users_log_password_len') . $minPasswordLength
            );
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
    public function usernameExists(string $username)
    {
        $user = $this->modx->getObject(modUser::class, array('username' => $username));
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
    public function emailExists(string $email)
    {
        $userProfile = $this->modx->getObject(modUserProfile::class, array('email' => $email));
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
    public function csvMimeType(string $mimetype)
    {
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
    public function validEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE);
    }

    /**
     * Checks if we have a valid UNIX timestamp for MySQL sigend int(10) fields.
     *
     * @access public
     * @param string $timestamp The timestamp to check.
     * @return boolean
     */
    public function validTimestamp($timestamp)
    {
        $check = (is_int($timestamp) or is_float($timestamp))
            ? $timestamp
            : (string) (int) $timestamp;

        return ($check === $timestamp)
            and ((int) $timestamp <= 2147483647)
            and ((int) $timestamp >= -2147483648);
    }

    /**
     * Checks if we have a valid birth date.
     *
     * @access public
     * @param string $date The date string to check.
     * @return boolean
     */
    public function validBirthDate($date)
    {
        $validBirthDate = false;
        $parsed = date_parse($date);
        if (
            $parsed['error_count'] == 0 &&
            $parsed['warning_count'] == 0 &&
            !empty($parsed['year']) &&
            !empty($parsed['month']) &&
            !empty($parsed['day'])
        ) {
            if (checkdate($parsed['month'], $parsed['day'], $parsed['year'])) {
                $validBirthDate = $this->validTimestamp(strtotime($date));
            }
        }
        return $validBirthDate;
    }

    /**
     * Checks if we have a valid gender.
     * (integer with value 1 = male, 2 = female, 3 = other)
     *
     * @access public
     * @param string $gender The value to check
     * @return boolean
     */
    public function validGender($gender)
    {
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
     * @return mixed array | NULL
     */
    public function jsonToArray($json)
    {
        return json_decode($json, true);
    }

    /**
     * Send a notification email to the imported user based on the specified information and templates.
     *
     * @access public
     * @param modUser $user The user object
     * @param modUserProfile $profile The user profile object
     * @param string $password The users password (cleartext!)
     * @param string $mailSubject The subject of the notification mail
     * @param string $mailBody The body of the notification mail
     * @return boolean
     */
    public function sendNotificationEmail(
        modUser $user,
        modUserProfile $profile,
        string $password,
        string $mailSubject,
        string $mailBody
    ) {
        // Set confirmation email properties

        // Flatten extended fields:
        // extended.field1
        // extended.container1.field2
        // ...
        $extended = $profile->get('extended') ? $profile->get('extended') : [];
        if (!empty($extended)) {
            $extended = $this->flattenExtended($extended, 'extended.');
        }
        $emailProperties = array_merge(
            $profile->toArray(),
            $user->toArray(),
            $extended
        );
        $emailProperties = $this->cleanupKeys($emailProperties);

        // Now re-add the password field with password in cleartext (so it will be available as placeholder)
        $emailProperties['password'] = $password;

        // Parse mailbody and process MODX user/profile placeholders
        $chunk = $this->modx->newObject(modChunk::class);
        $chunk->setCacheable(false);
        $output = $chunk->process($emailProperties, $mailBody);
        $emailProperties['mailbody'] = $output;
        $emailProperties['mailsubject'] = $mailSubject;

        $bodyText = Html2Text::convert($emailProperties['mailbody'], [
            'ignore_errors' => true,
        ]);

        // Send email!
        $mail = $this->modx->services->get('mail');
        $mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $mail->set(modMail::MAIL_SENDER, $this->modx->getOption('emailsender'));
        $mail->set(modMail::MAIL_SUBJECT, $emailProperties['mailsubject']);
        $mail->set(modMail::MAIL_BODY, $emailProperties['mailbody']);
        $mail->set(modMail::MAIL_BODY_TEXT, $bodyText);
        $mail->address('to', $emailProperties['email'], $emailProperties['email']);
        $mail->address('reply-to', $this->modx->getOption('emailsender'));
        $mail->setHTML(true);

        $sent = $mail->send();
        $sendStatus = [];
        $sendStatus['sent'] = $sent;
        $sendStatus['erro_info'] = $mail->mailer->ErrorInfo;
        $mail->reset();

        return $sendStatus;
    }

    /**
     * Manipulate/add/remove fields from array.
     *
     * @access private
     * @param array $properties
     * @return array $properties
     */
    private function cleanupKeys(array $properties = [])
    {
        unset(
            $properties['password'],    // security!
            $properties['cachepwd'],    // security!
            $properties['salt'],        // security!
            $properties['internalKey'], // not needed (id of profile is overwritten by id of user table)
            $properties['sessionid'],   // security!
            $properties['extended']     // not needed as its already flattened
        );
        return $properties;
    }

    /**
     * Helper function to recursively flatten an array.
     *
     * @access private
     * @param array $array The array to be flattened.
     * @param string $prefix The prefix for each new array key.
     * @return array $result The flattened and prefixed array.
     */
    private function flattenExtended($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + $this->flattenExtended($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
