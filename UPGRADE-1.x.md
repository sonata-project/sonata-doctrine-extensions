UPGRADE 1.x
===========

### `Sonata\Doctrine\Mapper\DoctrineCollector`

Deprecated passing other type than integer as argument 2 for `addInheritanceType()`.

### `Sonata\Doctrine\Mapper\ORM\DoctrineORMMapper`

Deprecated passing other type than array as argument 3 for `addAssociation()`.
Deprecated passing other type than array as argument 2 for `addDiscriminatorColumn()`.
Deprecated passing other type than array as argument 3 for `addOverride()`.

### `Sonata\Doctrine\Bridge\Symfony\Bundle\SonataDoctrineBundle`

Deprecated `Sonata\Doctrine\Bridge\Symfony\Bundle\SonataDoctrineBundle`. Use `Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle`
instead.

### `Sonata\Doctrine\Types\JsonType` has been deprecated

`doctrine/dbal` has a native implementation, `Doctrine\DBAL\Types\JsonType`, that
should be used instead.

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes.
You can't extend them anymore, because they are only loaded when running internal tests.
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
