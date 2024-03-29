# Changelog for UserImport
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [2.0.0-beta2] - 2023-03-03
### Added
- Import as GoodNews subscribers if GoodNews add-on is installed
- Assign GoodNews groups and categories on import

## [2.0.0-beta1] - 2023-02-21
### Added
- Added new setting mail_format (HTML or Plain text)
- User notification mails are now generated as multipart mails (HTML + plain-text)

### Changed
- Fully refactored for MODX3 (UserImport 2.x only!)
- Moved translations to Crowdin

### Fixed
- [#11] Fixes missing mailsubject and mailbody when import process starts
- Removed deprecated use of strftime (PHP8+)

## [1.1.1-pl] - 2020-05-19
### Added
- Added new checks for import file

### Fixed
- [#12] Fixed deprecated usage of ereg() in validEmail method

## [1.1.0-pl] - 2017-12-07
### Added
- [#5] Added feature to notify new imported users via email
- Added feature to save import params as presets (system settings)
- Added pre check for correct delimiter characters
- Added pre check for correct enclosure characters

## [1.0.0-pl] - 2016-02-24
### Added
- Added sanity check for for equal fields - values count in CSV data
- [#7] Added feature to import passwords via CSV - thanks @prioritypie

## [1.0.0-rc1] - 2016-02-13
### Added
- Extended fields support via Json strings added
- [#4] extented fields support
- Added missing profile fields to import (except extended field!)

### Changed
- Imported users now at least gets assigned role "member" when joining user groups
- Make writing of import-infos optional via import options
- Import-infos are now written to extended profile field

### Fixed
- Fixed some "undefined" messages and simplified profile array handling
- Fixed a problem when console window is closed and reopened without page reload

## [1.0.0-beta3] - 2015-03-04
### Added
- Added French translation (thanks @shabang!)
- Added onBeforeUserImport and onAfterUserImport events (thanks @shabang!)

## [1.0.0-beta2] - 2014-11-17
### Fixed
- Fixed a problem with fgetcsv() used on PHP versions prior 5.3.0

## [1.0.0-beta1] - 2014-08-12
### Added
- First public beta release
