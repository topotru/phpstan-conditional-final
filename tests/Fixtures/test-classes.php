<?php

declare(strict_types=1);

namespace Topotru\ConditionalFinal\PHPStan\Tests\Fixtures;

use Attribute;

// Объявляем фейковые тестовые атрибуты прямо здесь
#[Attribute(Attribute::TARGET_CLASS)]
final class ProxyRequired {}

#[Attribute(Attribute::TARGET_CLASS)]
final class AnotherForbidden {}

// Ошибка (строка 15): Обычный класс обязан быть final или abstract
class ForgotFinalClass {}

// ОК: Абстрактный класс
abstract class SampleAbstract {}

// ОК: Обычный класс с final
final class SampleFinal {}

// ОК: Класс с атрибутом, мешающим final (не-final — это норма)
#[ProxyRequired]
class GoodFlexibleClass {}

// Ошибка (строка 27): Класс с атрибутом, но его случайно зафиналили
#[ProxyRequired]
final class BadFinalClass {}

// Ошибка (строка 31): Класс со вторым запрещенным атрибутом случайно зафиналили
#[AnotherForbidden]
final class AnotherBadFinalClass {}
