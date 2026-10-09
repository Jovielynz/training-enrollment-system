
<?php

class EnrollmentRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function recordStudent($full_name, $email, $phone, $class_id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                'SELECT slots FROM classes
                 WHERE class_id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $class_id]);
            $class = $stmt->fetch();

            if (!$class || (int) $class['slots'] <= 0) {
                throw new RuntimeException('No slots available.');
            }

            $stmt = $this->db->prepare(
                'INSERT INTO students (full_name, email, phone)
                 VALUES (:name, :email, :phone)'
            );
            $stmt->execute([
                'name' => $full_name,
                'email' => $email !== '' ? $email : null,
                'phone' => $phone !== '' ? $phone : null
            ]);

            $student_id = $this->db->lastInsertId();

            $stmt = $this->db->prepare(
                "INSERT INTO enrollments (student_id, class_id, status)
                 VALUES (:student, :class, 'active')"
            );
            $stmt->execute([
                'student' => $student_id,
                'class' => $class_id
            ]);

            $stmt = $this->db->prepare(
                'UPDATE classes SET slots = slots - 1
                 WHERE class_id = :id AND slots > 0'
            );
            $stmt->execute(['id' => $class_id]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('No slots available.');
            }

            $this->db->commit();

            return (int) $student_id;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function enroll($student_id, $class_id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                'SELECT slots FROM classes
                 WHERE class_id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $class_id]);
            $class = $stmt->fetch();

            if (!$class || (int) $class['slots'] <= 0) {
                throw new RuntimeException('No slots available.');
            }

            $stmt = $this->db->prepare(
                "SELECT enrollment_id FROM enrollments
                 WHERE student_id = :student
                 AND class_id = :class
                 AND status = 'active'"
            );
            $stmt->execute([
                'student' => $student_id,
                'class' => $class_id
            ]);

            if ($stmt->fetch()) {
                throw new RuntimeException('Student is already enrolled in this class.');
            }

            $stmt = $this->db->prepare(
                'INSERT INTO enrollments (student_id, class_id, status)
                 VALUES (:student, :class, :status)'
            );
            $stmt->execute([
                'student' => $student_id,
                'class' => $class_id,
                'status' => 'active'
            ]);

            $stmt = $this->db->prepare(
                'UPDATE classes SET slots = slots - 1
                 WHERE class_id = :id AND slots > 0'
            );
            $stmt->execute(['id' => $class_id]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('No slots available.');
            }

            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function cancel($enrollment_id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                'SELECT class_id FROM enrollments
                 WHERE enrollment_id = :id
                 AND status = :status FOR UPDATE'
            );
            $stmt->execute([
                'id' => $enrollment_id,
                'status' => 'active'
            ]);

            $enrollment = $stmt->fetch();

            if (!$enrollment) {
                throw new RuntimeException('Active enrollment not found.');
            }

            $stmt = $this->db->prepare(
                "UPDATE enrollments SET status = 'cancelled'
                 WHERE enrollment_id = :id AND status = 'active'"
            );
            $stmt->execute(['id' => $enrollment_id]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Unable to cancel enrollment.');
            }

            $stmt = $this->db->prepare(
                'UPDATE classes SET slots = slots + 1
                 WHERE class_id = :id'
            );
            $stmt->execute(['id' => $enrollment['class_id']]);

            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function allWithDetails()
    {
        return $this->db->getRows(
            'SELECT e.enrollment_id, e.enrollment_date, e.status,
                    s.student_id, s.full_name, s.email, s.phone,
                    c.class_id, c.class_code, c.schedule, c.instructor, c.slots,
                    co.course_code, co.course_name
             FROM enrollments e
             INNER JOIN students s ON e.student_id = s.student_id
             INNER JOIN classes c ON e.class_id = c.class_id
             INNER JOIN courses co ON c.course_id = co.course_id
             ORDER BY e.enrollment_date DESC'
        );
    }
}
