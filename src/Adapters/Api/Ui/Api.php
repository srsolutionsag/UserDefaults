<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Ui;

use srag\Plugins\UserDefaults\Adapters\Config\Configs;
use srag\Plugins\UserDefaults\Adapters\Ui\InputElements;

class Api
{

    private function __construct(
        private Facade $facade
    )
    {

    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs));
    }

    public function searchInputElementHtml(string $title, string $postvar, string $dataSrc): InputElements\FluxEcoSearchInputElement\InputElement
    {
        return $this->facade->searchInputElement($title, $postvar, $dataSrc);
    }
}