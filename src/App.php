<?php
namespace AdvManager;

class App
{
	/**
	 * Outputs message to user and die.
	 *
	 * @param \Exception|string $message Message to output.
	 * @return void
	 */
	public static function outputMessageAndDie(\Exception|string $message): void
	{
		if ($message instanceof \Exception) {
			echo $message->getMessage();
		} else {
			echo $message;
		}
		exit(1);
	}
}
