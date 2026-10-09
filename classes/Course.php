
<?php

class Course
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all()
    {
        return $this->db->getRows(
            'SELECT * FROM courses ORDER BY course_name ASC'
        );
    }

    public function find($id)
    {
        return $this->db->getRow(
            'SELECT * FROM courses WHERE course_id = :id',
            ['id' => $id]
        );
    }

    public function create($code, $name, $description)
    {
        return $this->db->insert('courses', [   
            'course_code' => $code,
            'course_name' => $name,
            'description' => $description
        ]);
    }

    public function update($id, $code, $name, $description)
    {
        $stmt = $this->db->prepare(
            'UPDATE courses
             SET course_code = :code,
                 course_name = :name,
                 description = :description
             WHERE course_id = :id'
        );

        return $stmt->execute([
            'code' => $code,
            'name' => $name,
            'description' => $description,
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare(
            'DELETE FROM courses WHERE course_id = :id'
        );

        return $stmt->execute(['id' => $id]);
    }
}
