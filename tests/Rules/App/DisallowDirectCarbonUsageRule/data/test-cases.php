<?php

class AllowedDateFacadeUsage
{
	public function run()
	{
		Date::now();
		Date::today();
	}
}

class DisallowedCarbonUsage
{
	public function run()
	{
		new Carbon();
		Carbon::now();

		new CarbonImmutable();
		CarbonImmutable::today();
	}
}
