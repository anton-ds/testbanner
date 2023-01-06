<?php
namespace AdvManager\Database;

abstract class Model
{
	private Engine\IEngine $engine;

	/**
	 * Model instance constructor.
	 */
	private function __construct()
	{
		$this->engine = Connection::getEngine();
	}

	/**
	 * Inserts data to specific table. Returns true on success.
	 *
	 * @param array $fields Data to insert to table.
	 * @return bool
	 */
	public static function insert(array $fields): bool
	{
		$instance = new static();

		return $instance->engine->insertToTable(
			$instance->getTableName(),
			$fields,
		);
	}

	/**
	 * Sets columns to update-statement. Returns instance for next manipulations or execute.
	 *
	 * @param array $updateFields
	 * @return self
	 */
	public static function update(array $updateFields): self
	{
		$instance = new static();

		$instance->engine->updateInTable(
			$instance->getTableName(),
			$updateFields,
		);

		return $instance;
	}

	/**
	 * Sets columns to select-statement. Returns instance for next manipulations or execute.
	 *
	 * @param array $selectFields
	 * @return self
	 */
	public static function select(array $selectFields): self
	{
		$instance = new static();

		$instance->engine->selectFromTable(
			$instance->getTableName(),
			$selectFields,
		);

		return $instance;
	}

	/**
	 * Sets data for 'where' part in _current_ statement.
	 * Returns instance for next manipulations or execute.
	 *
	 * @param array $whereFields Data for filtering.
	 * @return self
	 */
	public function where(array $whereFields): self
	{
		$this->engine->setWhereStatement(
			$whereFields,
		);

		return $this;
	}

	/**
	 * Sets limit count to current statement.
	 * Returns instance for next manipulations or execute.
	 *
	 * @param int $limit Limit count.
	 * @return $this
	 */
	public function limit(int $limit): self
	{
		$this->engine->setLimit($limit);

		return $this;
	}

	/**
	 * Returns last execution result.
	 *
	 * @return array
	 */
	public function executeWithResult(): array
	{
		if ($this->engine->execute()) {
			return $this->engine->getLastResult();
		}

		return [];
	}

	/**
	 * Just executes current statement without returns any result, except boolean.
	 *
	 * @return bool
	 */
	public function execute(): bool
	{
		return $this->engine->execute();
	}

	/**
	 * Returns table name of Model.
	 *
	 * @return string
	 */
	abstract public function getTableName(): string;
}
