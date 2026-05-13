<?php

/*
 * LLC "HBPro"
 * Yuri Kurbatov <y.kurbatov@leaderteh.ru>
 * Date: 13.05.2026
 * Time: 16:36
 */

declare(strict_types=1);

namespace Topotru\ConditionalFinal\PHPStan;

use Override;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionClass;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionEnum;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

use function ltrim;
use function sprintf;

/**
 * @implements Rule<Class_>
 */
final readonly class ConditionalFinalRule implements Rule
{
    /**
     * @param array<string> $forbiddenFinalAttributes
     */
    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private array $forbiddenFinalAttributes = []
    ) {
    }

    #[Override]
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @return list<IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    #[Override]
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var Class_ $node */
        if (null === $node->name || $node->isAbstract()) {
            return [];
        }

        $className = null !== $node->namespacedName ? $node->namespacedName->toString() : '';

        if (!$this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        $attribute = $this->getForbiddenAttribute($classReflection->getNativeReflection());

        $isFinal = $node->isFinal();

        if (null !== $attribute && $isFinal) {
            return $this->buildEntityShouldNotBeFinal($className, $attribute);
        }

        if (null === $attribute && !$isFinal) {
            return $this->buildClassShouldBeFinal($className);
        }

        return [];
    }

    /**
     * @return list<IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    private function buildClassShouldBeFinal(string $className): array
    {
        return [
            RuleErrorBuilder::message(
                sprintf('Class %s should be final or abstract.', $className)
            )
                ->identifier('conditionalFinal.classShouldBeFinal')
                ->build(),
        ];
    }

    /**
     * @return list<IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    private function buildEntityShouldNotBeFinal(string $className, string $attribute): array
    {
        return [
            RuleErrorBuilder::message(
                sprintf('Class %s is marked by attribute "%s" and cannot be final.', $className, $attribute)
            )
                ->identifier('conditionalFinal.entityNotFinal')
                ->build(),
        ];
    }

    private function getForbiddenAttribute(ReflectionClass|ReflectionEnum $nativeReflection): ?string
    {
        foreach ($this->forbiddenFinalAttributes as $forbiddenAttr) {
            $normalizedForbidden = ltrim($forbiddenAttr, '\\');

            foreach ($nativeReflection->getAttributes() as $attribute) {
                if (ltrim($attribute->getName(), '\\') === $normalizedForbidden) {
                    return $normalizedForbidden;
                }
            }
        }

        return null;
    }
}
