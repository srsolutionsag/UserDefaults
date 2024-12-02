<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\PortfolioTemplate;

use srag\Plugins\UserDefaults\Domain\Ports\Repository;
use srag\Plugins\UserDefaults\Domain\Model\Course;

class IliasPortfolioTemplateRepository implements Repository
{
    private function __construct(private readonly \ilDBInterface $db)
    {
    }

    public static function new(\ilDBInterface $db): self
    {
        return new self($db);
    }

    /**
     * @return Course[]
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
