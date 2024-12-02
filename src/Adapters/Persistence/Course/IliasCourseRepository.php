<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\Course;

use srag\Plugins\UserDefaults\Domain\Ports\Repository;
use srag\Plugins\UserDefaults\Domain\Model\Course;

class IliasCourseRepository implements Repository
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
        $query = "SELECT obj.obj_id, obj.title, ref.ref_id
				  FROM object_data AS obj
				  INNER JOIN object_reference AS ref ON obj.obj_id = ref.obj_id
			      WHERE obj.type = 'crs'
				  AND ref.deleted IS NULL
			      ORDER BY obj.title";

        $result = $this->db->query($query);
        $courses = [];
        $rows = $this->db->fetchAll($result);

        foreach ($rows as $row) {
            $courses[] = IliasCourseAdapter::new($row["obj_id"], $row["ref_id"], $row["title"])->toDomain();
        }
        return $courses;
    }
}
