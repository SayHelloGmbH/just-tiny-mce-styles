<?php

namespace jtmce\controllers;

use jtmce\core\Controller;
use jtmce\models\Settings;

class SettingsController extends Controller
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
		$page_title = __('Settings', \JustTinyMceStyles::TEXTDOMAIN);
		add_submenu_page(null, $page_title, $page_title, 'manage_options', 'jtmce_settings', [$this, 'actionIndex']);
	}

	/**
	 * Render settings page
	 */
	public function actionIndex()
	{
		$model = new Settings();
		$model->loadDefaults([
			'features' => ['selector', 'classes', 'editor_css'],
		]);
		$model->load($_POST) && $model->save();

		// load template
		return $this->render('settings/index', [
			'tab' => 'settings',
			'model' => $model,
		]);
	}
}
