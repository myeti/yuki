<?php

namespace grabber;


/**
 * MangaGrabber Class
 * Get data and grab manga's chapters
 */
class MangaGrabber implements MangaProvider
{

	/** @var MangaProvider */
	protected $provider;

	/** @var string */
	protected $root;

	/** @var array */
	protected $events = [];

	/**
	 * Setup API
	 * @param MangaProvider $provider
	 * @param string $root
	 */
	public function __construct(MangaProvider $provider, $root = DIRECTORY_SEPARATOR)
	{
		$this->provider = $provider;
		$this->root = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		chmod($this->root, 0777);
	}

	/**
	 * Get manga list
	 * @return array $id => $name
	 */
	public function getMangas()
	{
		$this->fire('get.mangas.start');

		$data = $this->provider->getMangas();

		$this->fire('get.mangas.end', ['mangas' => $data]);

		return $data;
	}

	/**
	 * Get manga details
	 * @param  string $mangaId
	 * @return \stdClass
	 */
	public function getManga($mangaId)
	{
		$this->fire('get.manga.start', ['mangaId' => $mangaId]);

		$data = $this->provider->getManga($mangaId);

		$this->fire('get.manga.end', ['manga' => $data]);

		return $data;
	}

	/**
	 * Get chatper list
	 * @param  string $mangaId
	 * @return array $id => $name
	 */
	public function getChapters($mangaId)
	{
		$this->fire('get.chapters.start', ['mangaId' => $mangaId]);

		$data = $this->provider->getChapters($mangaId);

		$this->fire('get.chapters.end', ['chapters' => $data]);

		return $data;
	}

	/**
	 * Get pages url
	 * @param  string $mangaId
	 * @param  string $chapterId
	 * @return array $number => $url
	 */
	public function getPages($mangaId, $chapterId)
	{
		$this->fire('get.pages.start', ['mangaId' => $mangaId, 'chapterId' => $chapterId]);

		$data = $this->provider->getPages($mangaId, $chapterId);

		$this->fire('get.pages.end', ['pages' => $data]);

		return $data;
	}

	/**
	 * Grab all or many chapters
	 * @param  string $mangaId
	 * @param  mixed $chapterIds
	 */
	public function grab($mangaId, array $chapterIds = [])
	{
		$this->fire('grab.start', ['mangaId' => $mangaId, 'chapterId' => $chapterIds]);

		// get manga name
		$details = $this->getManga($mangaId);
		$this->root .= $details->title;

		// all chapters
		if(empty($chapterIds)) {
			$chapterIds = $this->getChapters($mangaId);
		}

		// stats
		$total = $done = 0;
		$totalChapters = count($chapterIds);

		$this->fire('grab.index.start', ['details' => $details, 'chapters' => $chapterIds]);

		// make download list
		$list = [];
		foreach($chapterIds as $chapter => $name) {

			// extend path
			$path = $this->root . DIRECTORY_SEPARATOR . $name;

			// get pages
			$pages = $this->getPages($mangaId, $chapter);
			$total += count($pages);

			// inner loop !
			foreach($pages as $i => $url) {

				// push
				$ext = pathinfo($url, PATHINFO_EXTENSION);
				$list[$url] = $path . DIRECTORY_SEPARATOR . $i . '.' . $ext;

			}

		}

		$this->fire('grab.index.end', ['list' => $list, 'totalChapters' => $totalChapters, 'totalPages' => $total]);

		// download pages
		foreach($list as $url => $to) {

			// do download
			if(!file_exists($to)) {
				$this->download($url, $to);
			}

			// refresh stats
			$done++;
			$this->fire('grab.download', ['title' => $details->title, 'total' => $total, 'done' => $done]);

		}

		$this->fire('grab.end');
	}

	/**
	 * Download distant image to manga dir
	 * @param  string $url
	 * @param  string $to
	 * @return bool
	 */
	public function download($url, $to)
	{
		// create dir
		$path = dirname($to);
		if(!is_dir($path)) {
			mkdir($path, 0777, true);
		}

		// download
		$c = curl_init($url);
	    curl_setopt($c, CURLOPT_HEADER, 0);
	    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($c, CURLOPT_BINARYTRANSFER,1);
	    $content = curl_exec($c);
	    curl_close($c);

	    // write
	    return file_put_contents($to, $content);
	}

	/**
	 * Listen inner event
	 * @param  string $event
	 * @param  Closure $callback
	 */
	public function on($event, \Closure $callback)
	{
		$this->events[$event] = $callback;
	}

	/**
	 * Trigger event
	 * @param  string $event
	 * @param  array $args
	 */
	public function fire($event, array $args = [])
	{
		if(isset($this->events[$event])) {
			call_user_func($this->events[$event], $args);
		}
	}

}