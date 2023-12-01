<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class IliasModuleId
{
    private function __construct(public ObjId $objId, public RefId $refId)
    {

    }

    public static function new(ObjId $objId, RefId $refId): self
    {
        return new self($objId, $refId);
    }
}