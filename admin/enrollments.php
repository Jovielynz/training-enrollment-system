
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$pageTitle = 'Enrollments';
$basePath = '../';

$repo = new EnrollmentRepository($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = filter_input(
        INPUT_POST,
        'enrollment_id',
        FILTER_VALIDATE_INT
    );

    try {
        if (!$enrollment_id || $enrollment_id < 1) {
            throw new RuntimeException('Invalid enrollment.');
        }

        $repo->cancel($enrollment_id);
        $message = 'Enrollment cancelled. One class slot has been restored.';
    } catch (Throwable $e) {
        $message = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Cancellation failed.';
    }
}

$rows = $repo->allWithDetails();

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Enrollments</h1>

<?php if ($message !== ''): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="table-wrap">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Course</th>
    <th>Class</th>
    <th>Schedule</th>
    <th>Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach ($rows as $row): ?>
<tr>
    <td><?= (int) $row['enrollment_id'] ?></td>
    <td><?= htmlspecialchars($row['full_name']) ?></td>
    <td><?= htmlspecialchars($row['course_name']) ?></td>
    <td><?= htmlspecialchars($row['class_code']) ?></td>
    <td><?= htmlspecialchars($row['schedule'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['enrollment_date']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td>
        <?php if ($row['status'] === 'active'): ?>
        <form method="POST" onsubmit="return confirm('Cancel this enrollment?');">
            <input type="hidden" name="enrollment_id"
                   value="<?= (int) $row['enrollment_id'] ?>">
            <button type="submit">Cancel</button>
        </form>
        <?php else: ?>
            Cancelled
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
<?php if (!$rows): ?>
<tr><td colspan="8">No enrollment records found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
