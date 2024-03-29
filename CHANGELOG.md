# Changelog

All notable changes to this project will be documented in this file.

## 27-01-2024
### Changed
- Fix UUID create on create subscription page
- Only allow user to access Create Subscription page once the "Client ID" and "Api Key" as need entered

## 29-12-2023
### Added
- Add Multiple Card Readers button to setup page
- Add support for multiple card readers, to allow a command to be run per reader

### Changed
- API changes to allow for multiple card readers

## 16-12-2023
### Changed
- Fixed buildMessage in api
- Add commands/ZettleTotals.sh to gitignore and edit fpp_uninstall so the config file does not get removed at plugin uninstall

## 15-12-2023
### Changed
- Remove scripts/fpp_update.sh to try and fix the plugin update problem

## 13-12-2023
### Changed
- Allow the effect to be Activated or not. Allowing you to turn off the effect temp
- Change the api log entry's to more readable text
- Make changes to default config allowing for effect activate

## 07-12-2023
### Fixed
- Key check on setup up, making sure you have entered valid keys
- Change date and time format of log entry's

## 04-12-2023
### Changed
- Remove ui  password from destination url
- Add key check to setup page

## 14-10-2023
### Changed
- Rename command to include plugin name

## 17-09-2023
### Fixed
- Fix publish api to fpp-zettle.co.uk

## 11-09-2023
### Added
- Add optin to store donation amount on remote server

### Changed
- Add "Things To Note" to help file

## 01-09-2023
### Changed
- Style changes to status page and other pages
- Other little changes to pages
- Fix typo/add to help

## 25-07-2023
### Added
- Adding Pushover.net to get notifications when a donation is made
- Add help for pushover setup
