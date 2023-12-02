<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\LocalRole;

use srag\Plugins\UserDefaults\Domain;

class IliasLocalRoleRepository implements Domain\Ports\Repository
{

    private function __construct(private \ilRbacReview $rbacReview, private \ilTree $ilTree)
    {

    }

    public static function new(\ilRbacReview $rbacReview, \ilTree $ilTree): self
    {
        return new self($rbacReview, $ilTree);
    }

    /**
     * @return Domain\Model\LocalRole[]
     */
    public function findAll(): array
    {
        $localRoles = [];
        foreach ($this->rbacReview->getRolesByFilter($this->rbacReview::FILTER_NOT_INTERNAL) as $global_role) {
            if ($this->ilTree->isDeleted($global_role['parent'])) {
                continue;
            }
            $localRoles[] = IliasLocalRoleAdapter::new($global_role["obj_id"], $global_role["title"])->toDomain();
        }

        return $localRoles;
    }
}