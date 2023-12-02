<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate;

use srag\Plugins\UserDefaults\Domain\Ports\PortfolioTemplateService;

class Facade
{
    private function __construct(private PortfolioTemplateService $portfolioService)
    {

    }

    public static function new(PortfolioTemplateService $portfolioService): Facade
    {
        return new self($portfolioService);
    }

    /**
     * @return Responses\PortfolioTemplate[]
     */
    public function findAll(): array
    {
        $courses = $this->portfolioService->findAll();
        $courseResponses = [];

        foreach ($courses as $course) {
            $courseResponses[] = Responses\PortfolioTemplate::formDomain($course);
        }
        return $courseResponses;
    }
}