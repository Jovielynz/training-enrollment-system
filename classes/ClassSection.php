
<?php

class ClassSection
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function allWithCourse()
    {
        return $this->db->getRows(
            'SELECT c.*, co.course_name, co.course_code
             FROM classes c
             INNER JOIN courses co ON c.course_id = co.course_id
             ORDER BY co.course_name, c.class_code'
        );
    }

    public function create($course_id, $code, $schedule, $instructor, $slots)
    {
        return $this->db->insert('classes', [
            'course_id' => $course_id,
            'class_code' => $code,
            'schedule' => $schedule,
            'instructor' => $instructor,
            'slots' => $slots
        ]);
    }

    public function getSlots($class_id)
    {
        $row = $this->db->getRow(
            'SELECT slots FROM classes WHERE class_id = :id',
            ['id' => $class_id]
        );

        return $row ? (int) $row['slots'] : null;
    }
}
