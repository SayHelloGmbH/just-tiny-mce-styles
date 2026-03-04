<?php

namespace jtmce\core;

use jtmce\models\Settings;

/**
 * Data layer to store data inside active theme
 */
class FilesDataLayer extends DataLayer
{

	/**
	 * Method to find style formats from data storage
	 *
	 * @param boolean $refresh  ignore cache
	 * @return array
	 */
	public function getFormats($refresh = false)
	{
		if (!is_null($this->_formats) & !$refresh) {
			return $this->_formats;
		}

		$this->_formats = [];
		$file = $this->getFilePath();
		$this->_formats = self::readFormatsFile($file);

		return $this->_formats;
	}

	/**
	 * Read json file with plugin settings and convert to php array
	 *
	 * @param string $file  file path to be read
	 * @return array|mixed|object
	 */
	public static function readFormatsFile($file)
	{
		if (file_exists($file)) {
			$content = file_get_contents($file);

			$data = json_decode($content, true);
			$formats = (gettype($data) == 'string') ? json_decode($data, true) : $data;
			return $formats;
		}

		return [];
	}

	/**
	 * Method to save all settings into the storage collector
	 *
	 * @return boolean
	 */
	public function save()
	{
		$file = $this->getFilePath();

		if (defined('JSON_PRETTY_PRINT')) {
			$data = json_encode($this->_formats, JSON_PRETTY_PRINT);
		} else {
			$data = jtmce_format_json(json_encode($this->_formats));
		}
		$dir = dirname($file);

		// trying to create dir
		if ((!is_dir($dir) && !wp_mkdir_p($dir)) || !is_writable($dir)) {
			return false;
		}

		if (!empty($dir)) {
			if ($fp = fopen($file, 'w')) {
				fwrite($fp, $data . "\r\n");
				fclose($fp);
				jtmce_set_chmod($file);
				return true;
			}
		}
		return false;
	}

	/**
	 * Prepare file path to write to
	 * Takes value from parameter or settings
	 *
	 * @param null|string $rel_file
	 * @return string
	 */
	public static function getFilePath($rel_file = null)
	{
		if (is_null($rel_file)) {
			$rel_file = Settings::getDataSourceThemeFile();
		}

		$base_dir = wp_normalize_path(get_stylesheet_directory());

		// normalize incoming relative file and collapse path segments to prevent traversal
		$rel = ltrim(str_replace('\\', '/', $rel_file), '/');
		$parts = [];
		foreach (explode('/', $rel) as $segment) {
			if ($segment === '' || $segment === '.') continue;
			if ($segment === '..') {
				array_pop($parts);
				continue;
			}
			$parts[] = $segment;
		}
		$clean_rel = implode('/', $parts);

		$file_path = wp_normalize_path($base_dir . '/' . $clean_rel);
		$file_path = apply_filters('jtmce_config_file_path', $file_path);

		// final safety: ensure path is inside the theme directory
		$norm_base = rtrim($base_dir, '/') . '/';
		if (strpos($file_path, $norm_base) !== 0) {
			// fallback to a safe default inside theme
			$file_path = $base_dir . '/editor-formats.json';
		}

		return $file_path;
	}
}
