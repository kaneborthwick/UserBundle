<?php

declare (strict_types = 1);

namespace UserBundle;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

class ConfigProvider {
	public function __invoke() {
		$aggregator = new ConfigAggregator([
			new PhpFileProvider(__DIR__ . '/config/{,*.}config.php'),
		]);
		return $aggregator->getMergedConfig();
	}
}
