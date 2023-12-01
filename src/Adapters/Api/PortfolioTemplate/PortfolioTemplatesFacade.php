<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\PortfolioTemplate;

use srag\Plugins\UserDefaults\Domain\Ports\PortfolioTemplateService;

class PortfolioTemplatesFacade
{
    private function __construct(private PortfolioTemplateService $portfolioService)
    {

    }

    public static function new(PortfolioTemplateService $portfolioService): PortfolioTemplatesFacade
    {
        return new self($portfolioService);
    }

    /**
     * @return PortfolioTemplateResponse[]
     */
    public function get(): array
    {
        $courses = $this->portfolioService->findAll();
        $courseResponses = [];

        foreach ($courses as $course) {
            $courseResponses[] = PortfolioTemplateResponse::formDomain($course);
        }
        return $courseResponses;
    }
}