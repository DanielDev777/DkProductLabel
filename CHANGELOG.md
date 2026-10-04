# Changelog

All notable changes to this plugin are documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project uses [Semantic Versioning](https://semver.org/).

## [0.4.0] - 2026-10-04

### Added
- Administration module under Catalogues to list, create and edit labels
- "Labels" tab on the product detail page to assign and remove labels
- Administration snippets (en-GB, de-DE)

## [0.3.0] - 2026-10-04

### Added
- Labels are shown on the product box (listing, search) and on the product detail page
- Only active labels within their validity period are loaded, sorted by priority
- SCSS component for the labels
- Storefront snippets (en-GB, de-DE)
- Unit tests for the storefront subscriber

## [0.2.0] - 2026-10-04

### Added
- `product_label` entity with translated name, color, priority, active flag and validity period
- ManyToMany association between labels and products, available on products as `productLabels`
- Server-side validation that label colors are hex values (`#RRGGBB`)
- Plugin tables are removed on uninstall unless "keep user data" is selected
- Integration test for writing and reading labels through the DAL

### Changed
- Line endings are enforced as LF via `.gitattributes`

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
