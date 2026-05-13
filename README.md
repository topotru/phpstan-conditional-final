# Conditional Final for PHPStan

Smart `final`/`abstract` class enforcement with attributes-based exclusions for PHPStan.

## Installation

```bash
composer require --dev topotru/conditional-final-phpstan
```

## Usage

By default, the extension requires all classes to be `final` or `abstract` and has an empty exclusion list.

### Integration with Doctrine ORM

If your project uses Doctrine, include the preconfigured preset in your `phpstan.neon`:

```yaml
includes:
    - vendor/topotru/conditional-final-phpstan/doctrine.neon
```

This preset automatically protects `#[Entity]` and `#[MappedSuperclass]` classes from being marked as `final`.

### Custom Configurations

You can add any custom proxy or framework attributes to the exclusion list manually:

```yaml
parameters:
    conditionalFinal:
        forbiddenFinalAttributes:
            - App\Attributes\CustomProxy
            - ApiPlatform\Metadata\ApiResource
```

## License
MIT
