<?php

namespace Modules\Frontend;

class Module
{

	public function registerAutoloaders()
	{

		$loader = new \Phalcon\Loader();

		$loader->registerNamespaces(array(
			'Modules\Frontend\Controllers' => __DIR__ . '/controllers/',
			'Modules\Frontend\Models'      => __DIR__ . '/models/',
		));

		$loader->register();
	}

	public function registerServices($di)
	{

		/**
		 * Read configuration
		 */
		$config = include __DIR__ . "/config/config.php";

		$di['dispatcher'] = function () {
			$dispatcher = new \Phalcon\Mvc\Dispatcher();
			$dispatcher->setDefaultNamespace("Modules\Frontend\Controllers");

			return $dispatcher;
		};

		/**
		 * Setting up the view component
		 */
		$di['view'] = function () {

			$view = new \Phalcon\Mvc\View();

			$view->registerEngines(array(
				'.volt'  => function ($view, $di) {

					$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

					$volt->setOptions(array(
						'compiledPath'      => __DIR__ . '/cache/',
						'compiledSeparator' => '_',
						'compileAlways'     => TRUE // close it
					));

					//Add Functions
					$volt->getCompiler()->addFunction('strtotime', 'strtotime');

					return $volt;
				},
				'.phtml' => 'Phalcon\Mvc\View\Engine\Php'
			));

			$view->setViewsDir(__DIR__ . '/views/');
			$view->setLayoutsDir('../../common/layouts/');
			$view->setTemplateAfter('main');

			return $view;
		};

		/**
		 * Database connection is created based in the parameters defined in the configuration file
		 */
		$di['db'] = function () use ($config) {
			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
				"host"     => $config->database->host,
				"username" => $config->database->username,
				"password" => $config->database->password,
				"dbname"   => $config->database->name
			));
		};

	}

}