<?php

namespace DevenJahnke\PHPStan\Tests\Rules\App\DisallowDirectCarbonUsageRule;

use DevenJahnke\PHPStan\Rules\App\DisallowDirectCarbonUsageRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class Test extends RuleTestCase
{
	protected function getRule(): Rule
	{
		return new DisallowDirectCarbonUsageRule();
	}

	public function test_rule(): void
	{
		$this->analyse(
			files: [
				__DIR__ . '/data/test-cases.php'
			],
			expectedErrors: [
				[
					'Direct use of "Carbon" is disallowed; use the `Date` facade instead, e.g. `Date::now()`.',
					16, // the line number where the enum case is declared
				],
				[
					'Direct use of "Carbon" is disallowed; use the `Date` facade instead, e.g. `Date::now()`.',
					17, // the line number where the enum case is declared
				],
				[
					'Direct use of "CarbonImmutable" is disallowed; use the `Date` facade instead, e.g. `Date::now()`.',
					19, // the line number where the enum case is declared
				],
				[
					'Direct use of "CarbonImmutable" is disallowed; use the `Date` facade instead, e.g. `Date::now()`.',
					20, // the line number where the enum case is declared
				]
			],
		);
	}
}
