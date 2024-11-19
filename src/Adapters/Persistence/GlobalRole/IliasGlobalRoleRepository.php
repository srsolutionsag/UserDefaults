<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\GlobalRole;

use srag\Plugins\UserDefaults\Domain\Ports\Repository;
use srag\Plugins\UserDefaults\Domain\Model\Course;

class IliasGlobalRoleRepository implements Repository
{
    private function __construct(private readonly \ilRbacReview $rbacReview, private readonly \ilTree $ilTree)
    {
    }

    public static function new(\ilRbacReview $rbacReview, \ilTree $ilTree): self
    {
        return new self($rbacReview, $ilTree);
    }

    /**
     * @return Course[]
     */
    public function findAll(): array
    {
        $globalRoles = [];
        foreach ($this->rbacReview->getRolesByFilter($this->rbacReview::FILTER_ALL_GLOBAL) as $global_role) {
            if ($this->ilTree->isDeleted($global_role['parent'])) {
                continue;
            }
            if ($global_role["obj_id"] === 2) {
                continue;
            }
            if ($global_role["obj_id"] === 14) {
                continue;
            }
            $globalRoles[] = IliasGlobalRoleAdapter::new($global_role["obj_id"], $global_role["title"])->toDomain();
        }

        return $globalRoles;
    }
}
