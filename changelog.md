# Changelog

All notable changes to this plugin are documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project uses [Semantic Versioning](https://semver.org/).

## [0.1.1] - 2026-10-02

### Added
- Docker Compose setup based on dockware/shopware 6.7.14.2
- `bin/setup.sh` to install the plugin and build assets inside the container
- PHPStan (level max), PHP-CS-Fixer (PER-CS 2.0) and PHPUnit 11 configuration

### Changed
- Plugin metadata in `composer.json` (name, labels, description)
- Code style aligned with PER-CS 2.0

### Removed
- Unused lifecycle methods and the sample plugin configuration from the scaffold

## [0.1.0] - 2026-10-01

### Added
- Initial plugin scaffold generated with `bin/console plugin:create`