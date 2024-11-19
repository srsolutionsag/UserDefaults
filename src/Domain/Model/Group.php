<?php

namespace srag\Plugins\UserDefaults\Domain\Model;

use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\IliasModuleId;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\Title;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\ObjId;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\RefId;

class Group implements Entity
{
    private function __construct(public IliasModuleId $id, public Title $title)
    {
    }

    public static function new(int $objId, int $refId, string $title): Group
    {
        return new self(IliasModuleId::new(ObjId::new($objId), RefId::new($refId)), Title::new($title));
    }
}
