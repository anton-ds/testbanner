<?php
namespace AdvManager\Database\Engine;

use AdvManager\App;
use AdvManager\Config;

class Mysqli implements IEngine
{
	private \mysqli|null $connection = null;
	private \mysqli_stmt|null $statement = null;

	private array $queryParams = [];
	private array $queryParts = [];
	private array $resultData = [];

	/**
	 * Mysqli instance constructor.
	 */
	public function __construct()
	{
		$config = self::getConfig();

		try {
			$this->connection = new \mysqli(
				$config['host'],
				$config['user'],
				$config['pass'],
				$config['name']
			);
		}
		catch (\Exception $e) {
			return;
		}
	}

	/**
	 * Returns database configuration.
	 *
	 * @return array
	 */
	private function getConfig(): array
	{
		return array_merge(
			['host' => null, 'user' => null, 'pass' => null, 'name' => null],
			Config::get('database', 'connections.mysqli') ?? [],
		);
	}

	/**
	 * Sets additional params for future binding.
	 *
	 * @param array $params Additional params
	 * @return void
	 */
	private function addQueryParams(array $params): void
	{
		$this->queryParams += $params;
	}

	/**
	 * Binds data for statement.
	 *
	 * @return void
	 */
	private function bindParams(): void
	{
		if (!$this->statement) {
			return;
		}
		if (!$this->queryParams) {
			return;
		}

		$types = '';

		foreach ($this->queryParams as $field) {
			if (is_int($field)) {
				$types .= 'i';
			} elseif (is_float($field)) {
				$types .= 'd';
			} else {
				$types .= 's';
			}
		}

		$this->statement->bind_param($types, ...array_values($this->queryParams));
	}

	/**
	 * Adds part of query for full statement.
	 *
	 * @param string $queryPart Query part.
	 * @return void
	 */
	private function addQueryPart(string $queryPart): void
	{
		$this->queryParts[] = $queryPart;
	}

	/**
	 * Prepares statement from query parts.
	 *
	 * @return void
	 */
	private function prepareStatement(): void
	{
		try {
			$this->statement = $this->connection->prepare(
				implode(' ', $this->queryParts)
			);
		} catch (\Exception $e) {
			App::outputMessageAndDie($e);
		}
	}

	/**
	 * Sets last execution result.
	 *
	 * @return void
	 */
	private function setResult(): void
	{
		if (!$this->statement) {
			return;
		}

		$this->resultData = [];
		$result = $this->statement->get_result();

		if (!$result) {
			return;
		}

		foreach ($result as $item) {
			$this->resultData[] = $item;
		}
	}

	/**
	 * Closes current statement and erase instance variables.
	 *
	 * @return void
	 */
	private function closeStatement(): void
	{
		if (!$this->statement) {
			return;
		}

		$this->statement->close();

		$this->statement = null;
		$this->queryParams = [];
		$this->queryParts = [];
	}

	/**
	 * @inheritDoc
	 */
	public function isSuccess(): bool
	{
		return $this->connection && !$this->connection->error;
	}

	/**
	 * @inheritDoc
	 */
	public function insertToTable(string $tableName, array $fields): bool
	{
		$columns = implode(',', array_keys($fields));
		$questionMarks = implode(',', array_fill(0, count($fields), '?'));

		$this->addQueryPart("
			INSERT INTO {$tableName} ({$columns})
			VALUES ({$questionMarks})
		");

		$this->addQueryParams($fields);

		return $this->execute();
	}

	/**
	 * @inheritDoc
	 */
	public function updateInTable(string $tableName, array $updateFields): self
	{
		if (!empty($this->queryParts)) {
			App::outputMessageAndDie('Update-statement must be first in query chain.');
		}

		$this->addQueryPart("UPDATE {$tableName} SET");

		$first = true;

		foreach ($updateFields as $key => $value) {
			$this->addQueryPart((!$first ? ', ' : '') . "{$key}=?");
			if ($first) {
				$first = false;
			}
		}

		$this->addQueryParams($updateFields);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function selectFromTable(string $tableName, array $selectFields): self
	{
		if (!empty($this->queryParts)) {
			App::outputMessageAndDie('Select-statement must be first in query chain.');
		}

		$columns = implode(',', array_values($selectFields));

		$this->addQueryPart("SELECT {$columns} FROM {$tableName}");

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function setWhereStatement(array $whereFields): self
	{
		if (empty($this->queryParts)) {
			App::outputMessageAndDie('Before use where-statement uou must use select-statement or update-statement.');
		}

		$this->addQueryPart('WHERE 1=1');

		foreach ($whereFields as $key => $value) {
			$this->addQueryPart(" AND {$key}=?");
		}

		$this->addQueryParams($whereFields);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function setLimit(int $limit): self
	{
		$this->addQueryPart("LIMIT {$limit}");
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(): bool
	{
		if (!$this->statement) {
			$this->prepareStatement();
			$this->bindParams();
		}

		try {
			$result = $this->statement->execute();
			$this->setResult();
			$this->closeStatement();

			return $result;
		}
		catch (\Exception $e) {
			App::outputMessageAndDie($e);
			return false;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getLastResult(): array
	{
		return $this->resultData;
	}
}
