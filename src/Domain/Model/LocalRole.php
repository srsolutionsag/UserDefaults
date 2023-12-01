<?php

namespace srag\Plugins\UserDefaults\Domain\Model;
class LocalRole implements Entity
{

    private function __construct(public ValueObjects\ObjId $objId, public ValueObjects\Title $title)
    {

    }


    public static function new(int $objId, string $title): self
    {
        return new self(ValueObjects\ObjId::new($objId), ValueObjects\Title::new($title));
    }
}