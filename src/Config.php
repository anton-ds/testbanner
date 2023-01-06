<?php
namespace AdvManager;

class Config
{
	/**
	 * Returns config value by nested keys.
	 *
	 * @param array $config Config array from category.
	 * @param string $key Config code (nested keys separate by '.').
	 * @return mixed
	 */
	private static function getValue(array $config, string $key): mixed
	{
		if (strpos($key, '.')) {

			[$key, $keyRemain] = explode('.', $key, 2);

			if (!array_key_exists($key, $config)) {
				$config[$key] = [];
			}

			return self::getValue($config[$key], $keyRemain);

		} else {
			return $config[$key];
		}
	}

	/**
	 * Returns config value by category and code.
	 *
	 * @param string $category Config category (/config).
	 * @param string $code Config code (nested keys separate by '.').
	 * @return mixed
	 */
	public static function get(string $category, string $code): mixed
	{
		if (preg_match('/^[a-z0-9_-]+$/', $category)) {

			$configFile = __DIR__ . '/../config/' . $category . '.php';

			if (file_exists($configFile)) {
				$config = include $configFile;
				return self::getValue($config, $code);
			}
		}

		return null;
	}
}
