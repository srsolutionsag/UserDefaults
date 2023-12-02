<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\PortfolioTemplate;

use srag\Plugins\UserDefaults\Domain;

class IliasPortfolioTemplateRepository implements Domain\Ports\Repository
{

    private function __construct(private \ilDBInterface $db)
    {

    }

    public static function new(\ilDBInterface $db): self
    {
        return new self($db);
    }

    /**
     * @return Domain\Model\Course[]
     */
    public function findAll(): array
    {
        $query = "SELECT obj.obj_id, obj.title
				  FROM object_data AS obj
			      WHERE obj.type = 'prtt'
			      ORDER BY obj.title";

        $result = $this->db->query($query);
        $courses = [];
        $rows = $this->db->fetchAll($result);

        foreach ($rows as $row) {
            $courses[] = IliasPortfolioAdapter::new($row["obj_id"], $row["title"])->toDomain();
        }
        return $courses;
    }
}