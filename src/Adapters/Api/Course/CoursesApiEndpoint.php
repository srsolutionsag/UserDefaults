<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Course;

use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class CoursesApiEndpoint
{

    private function __construct(
        private CoursesFacade $courses
    )
    {

    }

    public static function new(Configs $configs): CoursesApiEndpoint
    {
        return new self(CoursesFacade::new($configs->courseService));
    }


    public function get(): array
    {
        return $this->courses->get();
    }
}