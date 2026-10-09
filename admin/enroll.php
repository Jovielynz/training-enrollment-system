
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/Student.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$pageTitle = 'Enroll Student';
$basePath = '../';

$repo = new EnrollmentRepository($db);
$students = (new Student($db))->all();
$classes = (new ClassSection($db))->allWithCourse();

$message = '';
$messageType = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);

    try {
        if (!$student_id || !$class_id) {
            throw new RuntimeException('Select a valid student and class.');
        }

        if (!(new Student($db))->find($student_id)) {
            throw new RuntimeException('Student not found.');
        }

        $repo->enroll($student_id, $class_id);

        $message = 'Student enrolled successfully.';
        $messageType = 'success';
        $classes = (new ClassSection($db))->allWithCourse();
    } catch (Throwable $e) {
        $message = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Enrollment failed. No partial changes were saved.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Enroll Existing Student</h1>

<?php if ($message !== ''): ?>
<div class="message <?= $messageType ?>">
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="card">
<form method="POST">
    <label for="student_id">Student</label>
    <select name="student_id" id="student_id" required>
        <option value="">Select student</option>
        <?php foreach ($students as $student): ?>
        <option value="<?= (int) $student['student_id'] ?>">
            <?= htmlspecialchars($student['full_name']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label for="class_id">Class</label>
    <select name="class_id" id="class_id" required>
        <option value="">Select class</option>
        <?php foreach ($classes as $class): ?>
        <option value="<?= (int) $class['class_id'] ?>"
            <?= (int) $class['slots'] < 1 ? 'disabled' : '' ?>>
            <?= htmlspecialchars(
                $class['course_name'] . ' - ' .
                $class['class_code'] . ' (' .
                $class['slots'] . ' slots)'
            ) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Enroll Student</button>
</form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
