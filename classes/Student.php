
<?php

class Student
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function find($id)
    {
        return $this->db->getRow(
            'SELECT * FROM students WHERE student_id = :id',
            ['id' => $id]
        );
    }

    public function all()
    {
        return $this->db->getRows(
            'SELECT * FROM students ORDER BY full_name ASC'
        );
    }
}
