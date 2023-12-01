<?php

namespace srag\Plugins\UserDefaults\Domain\Model;

class GlobalRole implements Entity
{

    private function __construct(public ValueObjects\ObjId $objId, public ValueObjects\Title $title)
    {

    }


    public static function new(int $objId, string $title): GlobalRole
    {
        return new self(ValueObjects\ObjId::new($objId), ValueObjects\Title::new($title));
    }
}