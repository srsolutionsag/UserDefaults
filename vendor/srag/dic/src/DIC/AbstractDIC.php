<?php

namespace srag\DIC\UserDefaults\DIC;

use ILIAS\DI\Container;
use srag\DIC\UserDefaults\Database\DatabaseDetector;
use srag\DIC\UserDefaults\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\UserDefaults\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
