<?php

declare(strict_types=1);

namespace Topotru\PHPStan\ConditionalFinal\Tests;

use Override;
use PHPStan\DependencyInjection\MissingServiceException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule as PHPStanRule;
use PHPStan\Testing\RuleTestCase;
use Topotru\PHPStan\ConditionalFinal\Rule;
use Topotru\PHPStan\ConditionalFinal\Tests\Fixtures\AnotherForbidden;
use Topotru\PHPStan\ConditionalFinal\Tests\Fixtures\ProxyRequired;

/**
 * @extends RuleTestCase<Rule>
 */
final class RuleTest extends RuleTestCase
{
    /**
     * @throws MissingServiceException
     */
    #[Override]
    protected function getRule(): PHPStanRule
    {
        return new Rule(
            self::getContainer()->getByType(ReflectionProvider::class),
            [
                ProxyRequired::class,
                AnotherForbidden::class,
            ]
        );
    }

    public function testRuleAppliesCorrectly(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/test-classes.php'],
            [
                [
                    'Class Topotru\PHPStan\ConditionalFinal\Tests\Fixtures\ForgotFinalClass should be final or abstract.',
                    17,
                ],
                [
                    'Class Topotru\PHPStan\ConditionalFinal\Tests\Fixtures\BadFinalClass is marked by attribute "Topotru\PHPStan\ConditionalFinal\Tests\Fixtures\ProxyRequired" and cannot be final.',
                    30,
                ],
                [
                    'Class Topotru\PHPStan\ConditionalFinal\Tests\Fixtures\AnotherBadFinalClass is marked by attribute "Topotru\PHPStan\ConditionalFinal\Tests\Fixtures\AnotherForbidden" and cannot be final.',
                    34,
                ],
            ]
        );
    }
}
