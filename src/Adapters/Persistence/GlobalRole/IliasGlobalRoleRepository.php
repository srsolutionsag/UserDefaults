<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\GlobalRole;

use srag\Plugins\UserDefaults\Domain;

class IliasGlobalRoleRepository implements Domain\Ports\Repository
{

    private function __construct(private \ilRbacReview $rbacReview, private \ilTree $ilTree)
    {

    }

    public static function new(\ilRbacReview $rbacReview, \ilTree $ilTree): self
    {
        return new self($rbacReview, $ilTree);
    }

    /**
     * @return Domain\Model\Course[]
     */
    public function findAll(): array
    {
        $globalRoles = [];
        foreach ($this->rbacReview->getRolesByFilter($this->rbacReview::FILTER_ALL_GLOBAL) as $global_role) {
            if ($this->ilTree->isDeleted($global_role['parent']) || $global_role["obj_id"] === 2 ||  $global_role["obj_id"] === 14) {
                continue;
            }
            $globalRoles[] = IliasGlobalRoleAdapter::new($global_role["obj_id"], $global_role["title"])->toDomain();
        }

        return $globalRoles;
    }
}