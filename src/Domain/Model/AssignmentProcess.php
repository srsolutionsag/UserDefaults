<?php

namespace srag\Plugins\UserDefaults\Domain\Model;

use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\AssignmentProcessId;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\IsActive;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\OnCreate;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\OnUpdate;
use srag\Plugins\UserDefaults\Domain\Model\ValueObjects\OnApplyManually;

class AssignmentProcess implements Entity
{
    private function __construct(
        public AssignmentProcessId $id,
        public IsActive $isActive,
        public OnCreate $onCreate,
        public OnUpdate $onUpdate,
        public OnApplyManually $onApplyManually
    ) {
    }

    public static function new(
        int $id,
        bool $isActive,
        bool $onCreate,
        bool $onUpdate,
        bool $onApplyManually
    ): AssignmentProcess {
        return new self(
            AssignmentProcessId::new($id),
            IsActive::new($isActive),
            OnCreate::new($onCreate),
            OnUpdate::new($onUpdate),
            OnApplyManually::new($onApplyManually)
        );
    }
}
