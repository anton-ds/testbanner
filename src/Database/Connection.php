<?php
namespace AdvManager\Database;

class Connection
{
	private static Engine\IEngine|null $connection = null;

	/**
	 * Connection instance constructor.
	 */
	private function __construct()
	{
		//@todo: create Factory to select Engine
		self::$connection = new Engine\Mysqli();

		if (!self::$connection->isSuccess()) {
			\AdvManager\App::outputMessageAndDie(
				'Error connection to Database!'
			);
		}
	}

	/**
	 * Creates instance of connection and returns it.
	 *
	 * @return Engine\IEngine
	 */
	public static function getEngine(): Engine\IEngine
	{
		if (!self::$connection) {
			new self();
		}

		return self::$connection;
	}
}
