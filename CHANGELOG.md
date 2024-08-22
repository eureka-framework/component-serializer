# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

```yaml
## [tag] - YYYY-MM-DD
[tag]: https://github.com/eureka-framework/component-serializer/compare/2.1.0...master
### Changed
- Change 1
### Added
- Added 1
### Removed
- Remove 1
```

----

## [2.1.0] - 2024-08-22
[2.1.0]: https://github.com/eureka-framework/component-serializer/compare/2.0.0...2.1.0
### Changed
- Now support PHP 8.3 & PHP 8.4
- Fix some code style
- Use `\` for functions from global namespace
- Replace PHPCS by php-cs-fixer
- Move unit test to subdirectory `unit/`
- Dev dependencies update
- Update CI workflow
- Update Makefile & Readme

## [2.0.0] - 2023-03-20
[2.0.0]: https://github.com/eureka-framework/component-serializer/compare/1.0.0...2.0.0
### Changed
- Update phpstan to level max
- Now require PHP 8.1 as minimum 
- Better type hint
- Some minor refactoring

## [1.0.0] - 2023-03-15
### Added
- Add README.md
- Add (un)serializer json service
- Add JsonSerializableTrait helper
- Add AbstractCollection VO helper class
- Add tests
- Setup CI
