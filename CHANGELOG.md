# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [1.15.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.14.0...1.15.0) - 2021-11-06
### Deprecated
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] Passing null to `DoctrineORMAdapter::getNormalizedIdentifier()` ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] Passing null to `DoctrineORMAdapter::getUrlSafeIdentifier()` ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] Passing null to `DoctrinePHPCRAdapter::getNormalizedIdentifier()` ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] Passing null to `DoctrinePHPCRAdapter::getUrlSafeIdentifier()` ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BaseDocumentManager::__get()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BasePHPCRManager::__get()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BaseEntityManager::__get()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `ManagerInterface::getTableName()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `ManagerInterface::getConnection()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BaseManager::getTableName()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BasePHPCRManager::getTableName()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BasePHPCRManager::getConnection()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BaseEntityManager::getConnection()` method ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#365](https://github.com/sonata-project/sonata-doctrine-extensions/pull/365)] `BaseDocumentManager::getConnection()` method ([@VincentLanglet](https://github.com/VincentLanglet))

## [1.14.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.13.1...1.14.0) - 2021-10-05
### Added
- [[#363](https://github.com/sonata-project/sonata-doctrine-extensions/pull/363)] Added support for Doctrine DBAL 3. ([@jordisala1991](https://github.com/jordisala1991))

## [1.13.1](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.13.0...1.13.1) - 2021-07-20
### Fixed
- [[#327](https://github.com/sonata-project/sonata-doctrine-extensions/pull/327)] `EntityManagerMockFactoryTrait`  mocks all the basic methods of `QueryBuilder` ([@VincentLanglet](https://github.com/VincentLanglet))

## [1.13.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.12.0...1.13.0) - 2021-06-15
### Added
- [[#323](https://github.com/sonata-project/sonata-doctrine-extensions/pull/323)] Added `ClearableManagerInterface` to be able to clear the Manager in an easy way. ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#298](https://github.com/sonata-project/sonata-doctrine-extensions/pull/298)] Dependency on a non-existent service "sonata.doctrine.adapter.doctrine_phpcr" ([@skydiablo](https://github.com/skydiablo))

## [1.12.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.11.0...1.12.0) - 2021-03-11
### Added
- [[#303](https://github.com/sonata-project/sonata-doctrine-extensions/pull/303)] Add type hints to interfaces ([@core23](https://github.com/core23))
- [[#288](https://github.com/sonata-project/sonata-doctrine-extensions/pull/288)] Support for PHP 8.x ([@franmomu](https://github.com/franmomu))

### Fixed
- [[#306](https://github.com/sonata-project/sonata-doctrine-extensions/pull/306)] Missing PHPStan type at `BaseManager::$class` property ([@franmomu](https://github.com/franmomu))

## [1.11.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.10.1...1.11.0) - 2021-01-04
### Added
- [[#284](https://github.com/sonata-project/sonata-doctrine-extensions/pull/284)] Added PHP 8 support ([@VincentLanglet](https://github.com/VincentLanglet))

## [1.10.1](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.10.0...1.10.1) - 2020-10-21
### Fixed
- [[#255](https://github.com/sonata-project/sonata-doctrine-extensions/pull/255)] Compatibility with PHPUnit 9 ([@jordisala1991](https://github.com/jordisala1991))

## [1.10.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.9.1...1.10.0) - 2020-10-19
### Removed
- [[#240](https://github.com/sonata-project/sonata-doctrine-extensions/pull/240)] Remove support for `doctrine/mongodb-odm` <2.0 ([@franmomu](https://github.com/franmomu))

## [1.9.1](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.9.0...1.9.1) - 2020-08-09
### Fixed
- [[#211](https://github.com/sonata-project/sonata-doctrine-extensions/pull/211)] Fixed conflict with class names. ([@franmomu](https://github.com/franmomu))

## [1.9.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.8.0...1.9.0) - 2020-08-08
### Added
- [[#209](https://github.com/sonata-project/sonata-doctrine-extensions/pull/209)]
  Added `Sonata\Exporter\Bridge\Symfony\SonataDoctrineSymfonyBundle` alias in
order to fix Symfony Flex autodiscovery.
([@phansys](https://github.com/phansys))

## [1.8.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.7.0...1.8.0) - 2020-07-20
### Added
- [[#205](https://github.com/sonata-project/sonata-doctrine-extensions/pull/205)]
  Added `Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle`.
([@phansys](https://github.com/phansys))

### Deprecated
- [[#205](https://github.com/sonata-project/sonata-doctrine-extensions/pull/205)]
  Deprecated `Sonata\Doctrine\Bridge\Symfony\Bundle\SonataDoctrineBundle` in
favor of `Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle`.
([@phansys](https://github.com/phansys))

### Fixed
- [[#184](https://github.com/sonata-project/sonata-doctrine-extensions/pull/184)]
  Fix modifying entities (associations, discriminator columns and overrides)
with DoctrineCollector ([@jordisala1991](https://github.com/jordisala1991))

## [1.7.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.6.0...1.7.0) - 2020-07-02
### Added
- [[#203](https://github.com/sonata-project/sonata-doctrine-extensions/pull/203)]
  Added support for `doctrine/persistence:^2.0`.
([@phansys](https://github.com/phansys))

### Fixed
- [[#189](https://github.com/sonata-project/sonata-doctrine-extensions/pull/189)]
  Fixed returning `void` from methods which are intended to return values;
([@phansys](https://github.com/phansys))
- [[#189](https://github.com/sonata-project/sonata-doctrine-extensions/pull/189)]
  Fixed weak check at `ModelManager::getNormalizedIdentifier()`.
([@phansys](https://github.com/phansys))

### Removed
- [[#202](https://github.com/sonata-project/sonata-doctrine-extensions/pull/202)] Removed support for php:7.1. ([@phansys](https://github.com/phansys))

## [1.6.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.5.1...1.6.0) - 2020-03-23
### Added
- Added some explicit methods to `OptionsBuilder`

### Deprecated
- `OptionsBuilder::create` method

### Fixed
- Doctrine deprecation

### Changed
- Bump SF to 4.4

## [1.5.1](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.5.0...1.5.1) - 2019-12-15
### Fixed
- Fix typo in class name
- Restore argument in `EntityManagerMockFactory::create()`

## [1.5.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.4.0...1.5.0) - 2019-12-15
### Added
- Add `EntityManagerMockFactoryTrait`

## [1.4.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.3.1...1.4.0) - 2019-12-02
### Changed
- Remove the final modifier for the `getRepository` method

### Deprecated
- Passing a second argument to `BaseManager::findOneBy`

## [1.3.1](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.3.0...1.3.1) - 2019-09-27
### Fixed
- Using with only PHPCR without ORM

## [1.3.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.2.0...1.3.0) - 2019-04-29

### Added
- Added `DoctrineORMMapper` to manipulate doctrine entity relations

### Deprecated
- Deprecated `Sonata\Doctrine\Model\PageableManagerInterface`

## [1.2.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.1.5...1.2.0) - 2019-03-08

### Fixed
- Adapters are not being injected on the adapter chain.
- `sonata_urlsafeid` twig filter is working again

### Deprecated
- `Sonata\Doctrine\Types\JsonType`, in favor of `Doctrine\DBAL\Types\JsonType`

## [1.1.5](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.1.4...1.1.5) - 2019-01-19

### Fixed
- crash when decoding null value as JSON

## [1.1.4](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.1.3...1.1.4) - 2019-01-16

### Fixed
- invalid `doctrine_phpcr` config filename loading in SonataDoctrineExtension

## [1.1.3](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.1.2...1.1.3) - 2018-12-16
### Fixed
- crash about type hinting issues with AdapterInterface

## [1.1.2](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.1.1...1.1.2) - 2018-11-25
### Removed
- Removed `@mixin`s from classes

## [1.1.1](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.1.0...1.1.1) - 2018-11-21
### Fixed
- Fix class namespace and services loading

## [1.1.0](https://github.com/sonata-project/sonata-doctrine-extensions/compare/1.0.2...1.1.0) - 2018-10-02
### Added

- Added all doctrine stuff from `SonataCoreBundle`

### Removed
- support for old versions of php
