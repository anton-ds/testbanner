<?php
namespace AdvManager\Database\Engine;

interface IEngine
{
	/**
	 * Returns true if connection to Engine was successful.
	 *
	 * @return bool
	 */
	public function isSuccess(): bool;

	/**
	 * Inserts data to specific table. Returns true on success.
	 *
	 * @param string $tableName Table name.
	 * @param array $fields Data to insert (columnName => value).
	 * @return bool
	 */
	public function insertToTable(string $tableName, array $fields): bool;

	/**
	 * Sets columns to update-statement. Returns instance for next manipulations or execute.
	 *
	 * @param string $tableName Table name.
	 * @param array $updateFields Data to update.
	 * @return self
	 */
	public function updateInTable(string $tableName, array $updateFields): self;

	/**
	 * Sets columns to select-statement. Returns instance for next manipulations or execute.
	 *
	 * @param string $tableName Table name.
	 * @param array $selectFields Columns for getting.
	 * @return self
	 */
	public function selectFromTable(string $tableName, array $selectFields): self;

	/**
	 * Sets data for 'where' part in _current_ statement.
	 * Returns instance for next manipulations or execute.
	 *
	 * @param array $whereFields Data for filtering.
	 * @return self
	 */
	public function setWhereStatement(array $whereFields): self;

	/**
	 * Sets limit to current statement. Returns instance for next manipulations or execute.
	 *
	 * @param int $limit Limit count.
	 * @return self
	 */
	public function setLimit(int $limit): self;

	/**
	 * Executes current statement, return true on success.
	 *
	 * @return bool
	 */
	public function execute(): bool;

	/**
	 * Returns last execution result.
	 *
	 * @return array
	 */
	public function getLastResult(): array;
}
