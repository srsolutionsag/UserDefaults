<?php

namespace srag\DIC\UserDefaults\DIC;

use ILIAS\DI\Container;
use srag\DIC\UserDefaults\Database\DatabaseDetector;
use srag\DIC\UserDefaults\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\UserDefaults\DIC
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDIC implements DICInterface {

	/**
	 * @var Container
	 */
	protected $dic;


	/**
	 * @inheritDoc
	 */
	public function __construct(Container &$dic) {
		$this->dic = &$dic;
	}


	/**
	 * @inheritdoc
	 */
	public function database(): DatabaseInterface {
		return DatabaseDetector::getInstance($this->databaseCore());
	}
}
