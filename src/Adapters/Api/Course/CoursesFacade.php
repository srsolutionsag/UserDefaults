<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Course;

use srag\Plugins\UserDefaults\Domain\Ports\CourseService;

class CoursesFacade
{
    private function __construct(private CourseService $courses)
    {

    }

    public static function new(CourseService $courses): CoursesFacade
    {
        return new self($courses);
    }

    /**
     * @return CourseResponse[]
     */
    public function get(): array
    {
        $courses = $this->courses->findAll();
        $courseResponses = [];

        foreach ($courses as $course) {
            $courseResponses[] = CourseResponse::fromDomain($course);
        }
        return $courseResponses;
    }
}