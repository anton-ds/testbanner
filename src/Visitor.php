<?php
namespace AdvManager;

class Visitor
{
	/**
	 * Visitor instance constructor.
	 *
	 * @param string $ip Current visitor's IP.
	 * @param string $userAgent Current visitor's User Agent.
	 * @param string $page Current visitor's visited page.
	 */
	public function __construct(
		private string $ip,
		private string $userAgent,
		private string $page
	) {}

	/**
	 * Returns hash of current visitor.
	 *
	 * @return string
	 */
	private function getHash(): string
	{
		return md5(strtolower(preg_replace('/\s/', '', (
			$this->ip .
			$this->userAgent .
			$this->page
		))));
	}

	/**
	 * Registers current visitor.
	 *
	 * @return bool
	 */
	public function register(): bool
	{
		$hash = $this->getHash();

		$visitor = Models\Visitor::select(['id', 'views_count'])
			->where(['hash' => $hash])
			->limit(1)
			->executeWithResult()
		;

		if (empty($visitor)) {
			return Models\Visitor::insert([
				'ip_address' => $this->ip,
				'user_agent' => $this->userAgent,
				'page_url' => $this->page,
				'hash' => $hash,
			]);
		} else {
			$visitor = array_shift($visitor);
			return Models\Visitor::update(['views_count' => $visitor['views_count']+1])
				->where(['hash' => $hash])
				->limit(1)
				->execute()
			;
		}
	}
}
