<?php
namespace AdvManager\Models;

class Visitor extends \AdvManager\Database\Model
{
	/**
	 * @inheritDoc
	 */
	public function getTableName(): string
	{
		return 'visitors';
	}
}
