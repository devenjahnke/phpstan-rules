<?php

declare(strict_types=1);

namespace DevenJahnke\PHPStan\Rules\Tests;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * @implements Rule<ClassMethod>
 */
final class MethodNameSnakeCaseRule implements Rule
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
		private string $testCaseClass = 'Tests\\TestCase'
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // Ensure that the scope is a class.
        if (! $scope->isInClass()) {
            return [];
        }

		$scopeReflection = $scope->getClassReflection();

		// Ensure that the class is concrete.
		if ($scopeReflection->isAbstract()) {
			return [];
		}

        // Ensure that the method's class extends the `TestCase` class.
        if (! $scopeReflection->isSubclassOfClass(
			class: $this->reflectionProvider
				->getClass($this->testCaseClass)
		)) {
            return [];
        }

        // Ensure that the method is public.
        if (! $node->isPublic()) {
            return [];
        }

        // Ensure that the method name is snake cased.
        if (preg_match('/^[a-z][a-z0-9_]*$/', $name = $node->name->toString()) === 1) {
            return [];
        }

        return [
            RuleErrorBuilder::message("Test method [{$name}] must be snake cased.")
                ->identifier('tests.methodNameSnakeCase')
                ->build(),
        ];
    }
}
