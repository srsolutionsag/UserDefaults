<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Course;

use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{

    private function __construct(
        private Facade $courses
    )
    {

    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs->courseService));
    }


    public function findAll(): array
    {
        return $this->courses->findAll();
    }
}