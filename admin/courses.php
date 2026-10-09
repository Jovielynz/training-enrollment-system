
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';

$pageTitle = 'Courses';
$basePath = '../';
$model = new Course($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['course_id'] ?? 0);
    $code = trim($_POST['course_code'] ?? '');
    $name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    try {
        if ($action === 'delete') {
            if ($id < 1) {
                throw new RuntimeException('Invalid course.');
            }

            $model->delete($id);
            $message = 'Course deleted.';
        } else {
            if ($code === '' || $name === '' ||
                mb_strlen($code) > 20 || mb_strlen($name) > 100) {
                throw new RuntimeException('Enter a valid course code and name.');
            }

            if ($action === 'create') {
                $model->create($code, $name, $description);
                $message = 'Course added successfully.';
            } elseif ($action === 'update' && $id > 0) {
                $model->update($id, $code, $name, $description);
                $message = 'Course updated successfully.';
            }
        }
    } catch (Throwable $e) {
        $message = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Operation failed. Check for duplicate course codes or related classes.';
    }
}

$edit = isset($_GET['edit']) ? $model->find((int) $_GET['edit']) : null;
$courses = $model->all();

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Course Management</h1>

<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card">
<h2><?= $edit ? 'Edit Course' : 'Add Course' ?></h2>
<form method="POST">
    <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
    <input type="hidden" name="course_id" value="<?= (int) ($edit['course_id'] ?? 0) ?>">

    <label>Course Code</label>
    <input name="course_code" maxlength="20" required
           value="<?= htmlspecialchars($edit['course_code'] ?? '') ?>">

    <label>Course Name</label>
    <input name="course_name" maxlength="100" required
           value="<?= htmlspecialchars($edit['course_name'] ?? '') ?>">

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>

    <button type="submit"><?= $edit ? 'Update Course' : 'Add Course' ?></button>
</form>
</div>

<h2>Course List</h2>
<div class="table-wrap">
<table>
<tr><th>Code</th><th>Name</th><th>Description</th><th>Actions</th></tr>
<?php foreach ($courses as $course): ?>
<tr>
<td><?= htmlspecialchars($course['course_code']) ?></td>
<td><?= htmlspecialchars($course['course_name']) ?></td>
<td><?= htmlspecialchars($course['description'] ?? '') ?></td>
<td>
<a href="?edit=<?= (int) $course['course_id'] ?>">Edit</a>
<form method="POST" onsubmit="return confirm('Delete this course?');">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="course_id" value="<?= (int) $course['course_id'] ?>">
<button type="submit">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
