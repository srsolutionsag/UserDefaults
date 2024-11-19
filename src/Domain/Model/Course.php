<?php

namespace srag\Plugins\UserDefaults\Domain\Model;

class Course implements Entity
{
    private function __construct(public ValueObjects\IliasModuleId $id, public ValueObjects\Title $title)
    {

    }


    public static function new(int $objId, int $refId, string $title): Course
    {
        return new self(ValueObjects\IliasModuleId::new(ValueObjects\ObjId::new($objId), ValueObjects\RefId::new($refId)), ValueObjects\Title::new($title));
    }
}
