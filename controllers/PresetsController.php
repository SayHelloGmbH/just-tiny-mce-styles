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
		add_action('admin_menu', [ $this, 'initRoutes' ]);
	}

	/**
	 * Init routes for settings page
	 */
	public function initRoutes()
	{
		$page_title = __('Presets', \JustTinyMceStyles::TEXTDOMAIN);
		add_submenu_page(null, $page_title, $page_title, 'manage_options', 'jtmce_presets', [ $this, 'actionIndex' ]);
	}

	/**
	 * Render settings page
	 */
	public function actionIndex()
	{
		$model = new Preset();
		$model->load($_POST) && $model->import();

		// load template
		return $this->render('presets/index', [
			'tab' => 'presets',
			'model' => $model,
		]);
	}
}
