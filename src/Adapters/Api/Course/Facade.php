<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Course;

use srag\Plugins\UserDefaults\Domain\Ports\CourseService;

class Facade
{
    private function __construct(private CourseService $courses)
    {

    }

    public static function new(CourseService $courses): Facade
    {
        return new self($courses);
    }

    /**
     * @return Responses\Course[]
     */
    public function findAll(): array
    {
        $courses = $this->courses->findAll();
        $courseResponses = [];

        foreach ($courses as $course) {
            $courseResponses[] = Responses\Course::fromDomain($course);
        }
        return $courseResponses;
    }
}