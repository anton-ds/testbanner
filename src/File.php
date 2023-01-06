<?php
namespace AdvManager;

class File
{
	/**
	 * Returns relative path to file from specific dir.
	 *
	 * @param string $absolutePath Absolute path to document root.
	 * @param string $dirPath Relative dir path.
	 * @return string|null
	 */
	public static function getRandomFromDir(string $absolutePath, string $dirPath): ?string
	{
		$files = glob($absolutePath . $dirPath . '*.*');
		if (empty($files)) {
			return null;
		}

		return substr($files[array_rand($files)], mb_strlen($absolutePath));
	}
}
