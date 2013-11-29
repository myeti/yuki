<?php

namespace grabber;

/**
 * MangaProvider Interface
 * Makes sure to have the right methods
 */
interface MangaProvider
{

	/**
	 * Get manga list
	 * @return array $id => $name
	 */
	public function getMangas();

	/**
	 * Get manga details
	 * @param  string $mangaId
	 * @return \stdClass
	 */
	public function getManga($mangaId);

	/**
	 * Get chatper list
	 * @param  string $mangaId
	 * @return array $id => $name
	 */
	public function getChapters($mangaId);

	/**
	 * Get pages url
	 * @param  string $mangaId
	 * @param  string $chapterId
	 * @return array $number => $url
	 */
	public function getPages($mangaId, $chapterId);

}