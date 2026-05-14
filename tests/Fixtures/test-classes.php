<?php

declare(strict_types=1);

namespace Topotru\PHPStan\ConditionalFinal\Tests\Fixtures;

use Attribute;

// Declare fake test attributes right here
#[Attribute(Attribute::TARGET_CLASS)]
final class ProxyRequired {}

#[Attribute(Attribute::TARGET_CLASS)]
final class AnotherForbidden {}

// Error (line 17): A concrete class must be final or abstract
class ForgotFinalClass {}

// OK: Abstract class
abstract class SampleAbstract {}

// OK: Regular class with final
final class SampleFinal {}

// OK: Class with an attribute preventing final (non-final is normal)
#[ProxyRequired]
class GoodFlexibleClass {}

// Error (line 30): Class with attribute, but it was accidentally finalized
#[ProxyRequired]
final class BadFinalClass {}

// Error (line 34): Class with second forbidden attribute accidentally finalized
#[AnotherForbidden]
final class AnotherBadFinalClass {}
