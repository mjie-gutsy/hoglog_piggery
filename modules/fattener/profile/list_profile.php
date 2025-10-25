<?php 
require_once __DIR__ . '/../../../config/db.php';

// ✅ Get batch ID (if provided)
$batch_id = $_GET['id'] ?? null;
$batch = null;

// ✅ If batch_id is given, fetch batch info and only show related fatteners
if ($batch_id) {
    $stmt = $pdo->prepare("SELECT * FROM batch_records WHERE batch_id=?");
    $stmt->execute([$batch_id]);
    $batch = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM fattener_records WHERE batch_no=? ORDER BY fattener_id DESC");
    $stmt2->execute([$batch['batch_no']]);
    $fatteners = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If no batch selected, show all records
    $stmt = $pdo->query("SELECT * FROM fattener_records ORDER BY fattener_id DESC");
    $fatteners = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fattener List <?= $batch ? '- ' . htmlspecialchars($batch['batch_no']) : '' ?></title>

<!-- BOOTSTRAP -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body { background: #f8f9fa; font-family: 'Poppins', sans-serif; }

/* Sidebar */
.sidebar {
    height: 100vh;
    width: 240px;
    position: fixed;
    left: 0; top: 0;
    background: #212529;
    padding-top: 25px;
    color: white;
}
.sidebar a {
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    color: #adb5bd;
    font-size: 15px;
    margin-bottom: 5px;
}
.sidebar a i { margin-right: 8px; }
.sidebar a:hover, .sidebar a.active { background: #0d6efd; color: #fff; }

/* Topbar */
.topbar {
    margin-left: 240px;
    height: 60px;
    background: #fff;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 25px;
}

/* Content */
.content { margin-left: 260px; padding: 25px; }
th { background: #0d6efd !important; color: white; }
.btn-sm { padding: 4px 8px; font-size: 13px; }
.card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center mb-4"><i class="fa-solid fa-piggy-bank"></i> HogLog</h4>

    <?php if ($batch): ?>
        <a href="/hoglog_piggery/modules/batches/profile/view_batch.php?id=<?= $batch['batch_id'] ?>"><i class="fa-solid fa-list"></i> Batch Details</a>
    <?php endif; ?>
    <a href="/hoglog_piggery/modules/fattener/profile/add_profile.php<?= $batch ? '?id=' . $batch['batch_id'] : '' ?>"><i class="fa-solid fa-user-plus"></i> Add Fattener</a>
    <a href="#" class="active"><i class="fa-solid fa-users"></i> Fattener List</a>

    <a href="/hoglog_piggery/modules/batches/feed/list_feed.php"><i class="fa-solid fa-wheat-awn"></i> Feed Consumption</a>
    <a href="/hoglog_piggery/modules/expenses/list_expenses.php"><i class="fa-solid fa-money-bill"></i> Expenses</a>
    <a href="/hoglog_piggery/modules/growth_summary.php"><i class="fa-solid fa-chart-simple"></i> Growth Summary</a>
    <a href="/hoglog_piggery/modules/batches/profile/list_batches.php"><i class="fa-solid fa-arrow-left"></i> Back to Batch List</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
    <h5 class="m-0"><i class="fa-solid fa-users"></i> Fattener List <?= $batch ? '- ' . htmlspecialchars($batch['batch_no']) : '' ?></h5>
    <span class="text-secondary">HogLog Smart Piggery System</span>
</div>

<!-- CONTENT -->
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Fattener List <?= $batch ? '- ' . htmlspecialchars($batch['batch_no']) : '' ?></h4>
        <a href="add_profile.php<?= $batch ? '?id=' . $batch['batch_id'] : '' ?>" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Add New Fattener
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><i class="fa-solid fa-check"></i> Record added successfully.</div>
    <?php endif; ?>

    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Ear Tag No</th>
                    <th>Batch No</th>
                    <th>Sex</th>
                    <th>Breed Line</th>
                    <th>Birth Date</th>
                    <th>Weaning Date</th>
                    <th>Weaning Weight</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($fatteners): ?>
                <?php foreach ($fatteners as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ear_tag_no']) ?></td>
                        <td><?= htmlspecialchars($row['batch_no']) ?></td>
                        <td><?= htmlspecialchars($row['sex']) ?></td>
                        <td><?= htmlspecialchars($row['breed_line']) ?></td>
                        <td><?= htmlspecialchars($row['birth_date']) ?></td>
                        <td><?= htmlspecialchars($row['weaning_date']) ?></td>
                        <td><?= htmlspecialchars($row['weaning_weight']) ?> kg</td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="view_profile.php?id=<?= $row['fattener_id'] ?>" class="btn btn-info btn-sm">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-muted">No fattener records found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
