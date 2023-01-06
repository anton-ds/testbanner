<?php

use AdvManager\File;
use AdvManager\Config;
use AdvManager\Visitor;

require_once __DIR__.'/vendor/autoload.php';

if ($_SERVER['HTTP_REFERER'] ?? null) {
	(new Visitor(
		$_SERVER['REMOTE_ADDR'],
		$_SERVER['HTTP_USER_AGENT'],
		$_SERVER['HTTP_REFERER']
	))->register();
}

$file = File::getRandomFromDir(
	__DIR__,
	Config::get('file', 'banners.path'),
);

header("Location: {$file}", true, 307);
