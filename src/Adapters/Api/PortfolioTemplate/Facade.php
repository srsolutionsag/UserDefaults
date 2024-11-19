<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate;

use srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate\Responses\PortfolioTemplate;
use srag\Plugins\UserDefaults\Domain\Ports\PortfolioTemplateService;

class Facade
{
    private function __construct(private readonly PortfolioTemplateService $portfolioService)
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
            $courseResponses[] = PortfolioTemplate::formDomain($course);
        }
        return $courseResponses;
    }
}
