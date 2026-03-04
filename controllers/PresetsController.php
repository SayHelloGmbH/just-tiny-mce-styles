<?php

namespace jtmce\controllers;

use jtmce\core\Controller;
use jtmce\models\Preset;

class PresetsController extends Controller
{

	/**
	 * Init all wp-actions
	 */
	public function __construct()
	{
		parent::__construct();
		add_action('admin_menu', [$this, 'initRoutes']);
	}

	/**
	 * Init routes for settings page
	 */
	public function initRoutes()
	{
		$page_title = __('Presets', \JustTinyMceStyles::TEXTDOMAIN);
		add_submenu_page(null, $page_title, $page_title, 'manage_options', 'jtmce_presets', [$this, 'actionIndex']);
	}

	/**
	 * Render settings page
	 */
	public function actionIndex()
	{
		$model = new Preset();
		// process POST with nonce & capability checks
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (! isset($_POST['_wpnonce']) || ! wp_verify_nonce($_POST['_wpnonce'], 'just-nonce')) {
				wp_die(__('Invalid request.'), '', 403);
			}
			if (! current_user_can('manage_options')) {
				wp_die(__('Permission denied.'), '', 403);
			}
			$model->load($_POST) && $model->import();
		}

		// load template
		return $this->render('presets/index', [
			'tab' => 'presets',
			'model' => $model,
		]);
	}
}
