<?php

namespace srag\ActiveRecordConfig\UserDefaults\Config;

use srag\DIC\UserDefaults\DICTrait;

/**
 * Class AbstractFactory
 *
 * @package srag\ActiveRecordConfig\UserDefaults\Config
 */
abstract class AbstractFactory
{

    use DICTrait;

    /**
     * AbstractFactory constructor
     */
    protected function __construct()
    {

    }


    /**
     * @return Config
     */
    public function newInstance() : Config
    {
        $config = new Config();

        return $config;
    }
}
