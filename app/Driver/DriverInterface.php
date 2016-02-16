<?php

namespace Mantainance\Driver;

use Mantainance\Driver\DriverInterface;

interface DriverInterface {

	public function applySettings($pathToConfig, $pathToMantainancePage, $name);
}