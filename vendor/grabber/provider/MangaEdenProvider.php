<?php

namespace grabber\provider;

use grabber\MangaProvider;

/**
 * MangaEdenProvider Class
 * Provide MangaEden API
 */
class MangaEdenProvider implements MangaProvider
{

	/** @var string */
	protected $api = 'https://www.mangaeden.com/api';

	/** @var string */
	protected $cdn = 'http://cdn.mangaeden.com/mangasimg';

	/**
	 * Get manga list
	 * @return array $id => $name
	 */
	public function getMangas()
	{
		// query
		$data = file_get_contents($this->api . '/list/0/');
		$data = json_decode($data);

		// clean list
		$list = [];
		foreach($data->manga as $manga) {
			$list[$manga->i] = $manga->t;
		}

		return $list;
	}

	/**
	 * Get manga details
	 * @param  string $mangaId
	 * @return \stdClass
	 */
	public function getManga($mangaId)
	{
		// query
		$data = file_get_contents($this->api . '/manga/' . $mangaId . '/');
		$data = json_decode($data);

		return $data;
	}

	/**
	 * Get chatper list
	 * @param  string $mangaId
	 * @return array $id => $name
	 */
	public function getChapters($mangaId)
	{
		// query
		$data = file_get_contents($this->api . '/manga/' . $mangaId . '/');
		$data = json_decode($data);

		// clean list
		$list = [];
		foreach($data->chapters as $chapter) {
			$list[$chapter[3]] = ($chapter[0] == $chapter[2]) ? $chapter[0] : $chapter[0] . ' - ' . $chapter[2];
		}

		return $list;
	}

	/**
	 * Get pages url
	 * @param  string $mangaId
	 * @param  string $chapterId
	 * @return array $number => $url
	 */
	public function getPages($mangaId, $chapterId)
	{
		// query
		$data = file_get_contents($this->api . '/chapter/' . $chapterId . '/');
		$data = json_decode($data);

		// clean list
		$list = [];
		foreach($data->images as $page) {
			$list[$page[0]] = $this->cdn . '/' . $page[1];
		}

		return $list;
	}

}