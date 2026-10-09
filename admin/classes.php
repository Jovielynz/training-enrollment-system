
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$pageTitle = 'Classes';
$basePath = '../';

$courseModel = new Course($db);
$classModel = new ClassSection($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = (int) ($_POST['course_id'] ?? 0);
    $code = trim($_POST['class_code'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $slots = filter_input(INPUT_POST, 'slots', FILTER_VALIDATE_INT);

    try {
        if (!$courseModel->find($course_id) || $code === '' ||
            mb_strlen($code) > 20 || $slots === false ||
            $slots === null || $slots < 0) {
            throw new RuntimeException('Enter valid class information.');
        }

        $classModel->create($course_id, $code, $schedule, $instructor, $slots);
        $message = 'Class created successfully.';
    } catch (Throwable $e) {
        $message = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Unable to create class. Check for duplicate class codes.';
    }
}

$courseList = $courseModel->all();
$classList = $classModel->allWithCourse();

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Class Management</h1>

<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card">
<h2>Add Class</h2>
<form method="POST">
    <label>Course</label>
    <select name="course_id" required>
        <option value="">Select course</option>
        <?php foreach ($courseList as $course): ?>
        <option value="<?= (int) $course['course_id'] ?>">
            <?= htmlspecialchars($course['course_name']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Class Code</label>
    <input name="class_code" maxlength="20" required>

    <label>Schedule</label>
    <input name="schedule" maxlength="100">

    <label>Instructor</label>
    <input name="instructor" maxlength="100">

    <label>Available Slots</label>
    <input type="number" name="slots" min="0" required>

    <button type="submit">Add Class</button>
</form>
</div>

<h2>Class List</h2>
<div class="table-wrap">
<table>
<tr><th>Course</th><th>Class Code</th><th>Schedule</th><th>Instructor</th><th>Slots</th></tr>
<?php foreach ($classList as $class): ?>
<tr>
<td><?= htmlspecialchars($class['course_name']) ?></td>
<td><?= htmlspecialchars($class['class_code']) ?></td>
<td><?= htmlspecialchars($class['schedule'] ?? '') ?></td>
<td><?= htmlspecialchars($class['instructor'] ?? '') ?></td>
<td><?= (int) $class['slots'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
