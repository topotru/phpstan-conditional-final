<?php

/*
 * LLC "HBPro"
 * Yuri Kurbatov <y.kurbatov@leaderteh.ru>
 * Date: 13.05.2026
 * Time: 16:50
 */

declare(strict_types=1);

namespace Topotru\ConditionalFinal\PHPStan\Tests;

use Override;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Topotru\ConditionalFinal\PHPStan\ConditionalFinalRule;
use Topotru\ConditionalFinal\PHPStan\Tests\Fixtures\ProxyRequired;
use Topotru\ConditionalFinal\PHPStan\Tests\Fixtures\AnotherForbidden;

/**
 * @extends RuleTestCase<ConditionalFinalRule>
 */
final class ConditionalFinalRuleTest extends RuleTestCase
{
    #[Override]
    protected function getRule(): Rule
    {
        return new ConditionalFinalRule(
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
                    'Class Topotru\ConditionalFinal\PHPStan\Tests\Fixtures\ForgotFinalClass should be final or abstract.',
                    17,
                ],
                [
                    'Class Topotru\ConditionalFinal\PHPStan\Tests\Fixtures\BadFinalClass is marked by attribute "Topotru\ConditionalFinal\PHPStan\Tests\Fixtures\ProxyRequired" and cannot be final.',
                    30,
                ],
                [
                    'Class Topotru\ConditionalFinal\PHPStan\Tests\Fixtures\AnotherBadFinalClass is marked by attribute "Topotru\ConditionalFinal\PHPStan\Tests\Fixtures\AnotherForbidden" and cannot be final.',
                    34,
                ],
            ]
        );
    }
}
