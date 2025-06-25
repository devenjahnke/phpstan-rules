<?php

declare(strict_types=1);

namespace DevenJahnke\PHPStan\Rules\App;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\ClassConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<New_|StaticCall|ClassConstFetch>
 */
final class DisallowDirectCarbonUsageRule implements Rule
{
	/** @var string[] */
	private const DISALLOWED = [
		'Carbon',
		'CarbonImmutable',
		'\Carbon',
		'\CarbonImmutable',
		'Illuminate\\Support\\Carbon',         // in case someone imported full
		'Illuminate\\Support\\CarbonImmutable',
	];

	public function getNodeType(): string
	{
		// We hook into `new` instantiations, static calls, and class-constant fetches
		return Node::class;
	}

	/**
	 * @param New_|StaticCall|ClassConstFetch $node
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		// Helper: extract the class name if this node references one
		$getClassName = function (Node $n): ?string {
			if ($n instanceof New_ && $n->class instanceof Node\Name) {
				return $n->class->toString();
			}
			if ($n instanceof StaticCall && $n->class instanceof Node\Name) {
				return $n->class->toString();
			}
			if ($n instanceof ClassConstFetch && $n->class instanceof Node\Name) {
				return $n->class->toString();
			}
			return null;
		};

		$class = $getClassName($node);
		if ($class === null) {
			return [];
		}

		// Normalize leading backslash
		$classNormalized = ltrim($class, '\\');

		foreach (self::DISALLOWED as $bad) {
			if (strcasecmp(ltrim($bad, '\\'), $classNormalized) === 0) {
				return [
					RuleErrorBuilder::message(sprintf(
						'Direct use of "%s" is disallowed; use the `Date` facade instead, e.g. `Date::now()`.',
						$class
					))
						->identifier('laravel.disallowCarbon')
						->build(),
				];
			}
		}

		return [];
	}
}

