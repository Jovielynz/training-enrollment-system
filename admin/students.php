
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$pageTitle = 'Record Student';
$basePath = '../';

$repo = new EnrollmentRepository($db);
$classModel = new ClassSection($db);
$classList = $classModel->allWithCourse();

$message = '';
$messageType = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);

    try {
        if ($full_name === '' || mb_strlen($full_name) > 100) {
            throw new RuntimeException('Enter a valid student name.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Enter a valid email address.');
        }

        if ($phone !== '' && mb_strlen($phone) > 30) {
            throw new RuntimeException('Phone number is too long.');
        }

        if (!$class_id || $class_id < 1) {
            throw new RuntimeException('Please select a class.');
        }

        $repo->recordStudent($full_name, $email, $phone, $class_id);

        $message = 'Student recorded and enrolled successfully.';
        $messageType = 'success';
        $classList = $classModel->allWithCourse();
    } catch (Throwable $e) {
        $message = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Unable to record the student. The operation was rolled back.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Record Student</h1>

<?php if ($message !== ''): ?>
    <div class="message <?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="card">
<form method="POST">
    <label for="full_name">Full Name</label>
    <input id="full_name" name="full_name" maxlength="100" required>

    <label for="email">Email</label>
    <input id="email" type="email" name="email" maxlength="100">

    <label for="phone">Phone</label>
    <input id="phone" name="phone" maxlength="30">

    <label for="class_id">Training Class</label>
    <select id="class_id" name="class_id" required>
        <option value="">Select a class</option>
        <?php foreach ($classList as $class): ?>
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

    <button type="submit">Record and Enroll</button>
</form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
