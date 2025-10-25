<?php
require_once __DIR__ . '/../../../config/db.php';

// Validate request
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request");
}

$id = $_GET['id'];

// Fetch the fattener record
$stmt = $pdo->prepare("SELECT * FROM fattener_records WHERE fattener_id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$record) {
    die("Record not found.");
}

// Optional: Fetch related batch info
$batch_stmt = $pdo->prepare("SELECT * FROM batch_records WHERE batch_no = ?");
$batch_stmt->execute([$record['batch_no']]);
$batch = $batch_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fattener Profile - <?= htmlspecialchars($record['ear_tag_no']) ?> | HogLog</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin:0; }

/* Sidebar */
.sidebar {
    height: 100vh; width: 240px; position: fixed; left: 0; top: 0;
    background: #212529; padding-top: 25px; color: white;
}
.sidebar a { display: block; padding: 12px 20px; text-decoration: none; color: #adb5bd; margin-bottom:5px; }
.sidebar a i { margin-right:8px; }
.sidebar a:hover, .sidebar a.active { background: #0d6efd; color:#fff; }

/* Topbar */
.topbar {
    margin-left: 240px; height: 60px; background: #fff;
    border-bottom: 1px solid #dee2e6; display:flex; align-items:center; justify-content:space-between; padding:0 25px;
}

/* Content */
.content { margin-left: 260px; padding: 25px; }

/* Card */
.card { border-radius: 10px; margin-bottom:20px; }
.card-header { background-color:#38c0f0; color:#fff; font-weight:600; }

/* Buttons */
.btn-edit { background-color:#ffc107; color:#fff; border:none; }
.btn-delete { background-color:#dc3545; color:#fff; border:none; }
.btn-back { background-color:#6c757d; color:#fff; border:none; }
.btn-edit:hover, .btn-delete:hover, .btn-back:hover { opacity:0.85; }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4"><i class="fa-solid fa-piggy-bank"></i> HogLog</h4>
    <?php if ($batch): ?>
        <a href="/hoglog_piggery/modules/batches/profile/view_batch.php?id=<?= $batch['batch_id'] ?>"><i class="fa-solid fa-list"></i> Batch Details</a>
    <?php endif; ?>
    <a href="/hoglog_piggery/modules/fattener/profile/add_profile.php<?= $batch ? '?id=' . $batch['batch_id'] : '' ?>"><i class="fa-solid fa-user-plus"></i> Add Fattener</a>
    <a href="/hoglog_piggery/modules/fattener/profile/list_profile.php" class="active"><i class="fa-solid fa-users"></i> Fattener Profiles</a>
    <a href="/hoglog_piggery/modules/batches/feed/list_feed.php"><i class="fa-solid fa-wheat-awn"></i> Feed Consumption</a>
    <a href="/hoglog_piggery/modules/expenses/list_expenses.php"><i class="fa-solid fa-money-bill"></i> Expenses</a>
    <a href="/hoglog_piggery/modules/growth_summary.php"><i class="fa-solid fa-chart-simple"></i> Growth Summary</a>
</div>

<!-- Topbar -->
<div class="topbar">
    <h5 class="m-0"><i class="fa-solid fa-user"></i> Fattener Profile</h5>
    <span class="text-secondary">HogLog Smart Piggery System</span>
</div>

<!-- Content -->
<div class="content">

    <!-- Selected Fattener Profile Card -->
    <div class="card p-3 shadow-sm">
        <div class="card-header">Fattener Profile - <?= htmlspecialchars($record['ear_tag_no']) ?></div>
        <div class="card-body">
            <table class="table table-borderless">
                <tbody>
                    <tr><th>Ear Tag No</th><td><?= htmlspecialchars($record['ear_tag_no']) ?></td></tr>
                    <tr><th>Batch No</th><td><?= htmlspecialchars($record['batch_no']) ?></td></tr>
                    <tr><th>Sex</th><td><?= htmlspecialchars($record['sex']) ?></td></tr>
                    <tr><th>Breed Line</th><td><?= htmlspecialchars($record['breed_line']) ?></td></tr>
                    <tr><th>Birth Date</th><td><?= htmlspecialchars($record['birth_date']) ?></td></tr>
                    <tr><th>Weaning Date</th><td><?= htmlspecialchars($record['weaning_date']) ?></td></tr>
                    <tr><th>Weaning Weight</th><td><?= htmlspecialchars($record['weaning_weight']) ?> kg</td></tr>
                    <tr><th>Status</th><td><?= htmlspecialchars($record['status']) ?></td></tr>
                    <tr><th>Notes</th><td><?= nl2br(htmlspecialchars($record['notes'])) ?></td></tr>
                </tbody>
            </table>

            <div class="text-end mt-3">
                <a href="edit_profile.php?id=<?= $record['fattener_id'] ?>" class="btn btn-edit me-2">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                <a href="delete_profile.php?id=<?= $record['fattener_id'] ?>" class="btn btn-delete me-2"
                   onclick="return confirm('Are you sure you want to delete this record?')">
                    <i class="fa-solid fa-trash"></i> Delete
                </a>
                <a href="list_profile.php<?= $batch ? '?id=' . $batch['batch_id'] : '' ?>" class="btn btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

</div>
</body>
</html>
