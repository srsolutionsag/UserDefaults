<?php

namespace srag\Plugins\UserDefaults\Domain\Model;

class AssignmentProcess implements Entity
{
    private function __construct(public ValueObjects\AssignmentProcessId $id, public ValueObjects\IsActive $isActive, public ValueObjects\OnCreate $onCreate, public ValueObjects\OnUpdate $onUpdate, public ValueObjects\OnApplyManually $onApplyManually)
    {

    }


    public static function new(int $id, bool $isActive, bool $onCreate, bool $onUpdate, bool $onApplyManually): AssignmentProcess
    {
        return new self(ValueObjects\AssignmentProcessId::new($id), ValueObjects\IsActive::new($isActive), ValueObjects\OnCreate::new($onCreate), ValueObjects\OnUpdate::new($onUpdate), ValueObjects\OnApplyManually::new($onApplyManually));
    }
}