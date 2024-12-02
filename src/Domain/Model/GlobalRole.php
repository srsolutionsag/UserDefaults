<?php

namespace srag\Plugins\UserDefaults\Domain\Model;

use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\ObjId;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\Title;

class GlobalRole implements Entity
{
    private function __construct(public ObjId $objId, public Title $title)
    {
    }

    public static function new(int $objId, string $title): GlobalRole
    {
        return new self(ObjId::new($objId), Title::new($title));
    }
}
