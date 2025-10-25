<?php 
require_once __DIR__ . '/../../../config/db.php';

if (!isset($_GET['id'])) die('Invalid request');
$id = $_GET['id'];

// Fetch batch details
$stmt = $pdo->prepare("SELECT * FROM batch_records WHERE batch_id=?");
$stmt->execute([$id]);
$b = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$b) die('Batch not found');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Batch Details - <?= htmlspecialchars($b['batch_no']) ?></title>

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
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center mb-4"><i class="fa-solid fa-piggy-bank"></i> HogLog</h4>

    <a href="#" class="active"><i class="fa-solid fa-list"></i> Batch Details</a>
    <a href="/hoglog_piggery/modules/fattener/profile/add_profile.php?id=<?= $b['batch_id'] ?>"><i class="fa-solid fa-user-plus"></i> Add Fattener</a>
    <a href="/hoglog_piggery/modules/fattener/profile/list_profile.php?id=<?= $b['batch_id'] ?>"><i class="fa-solid fa-users"></i> Fattener List</a>
    
    <a href="/hoglog_piggery/modules/batches/feed/list_feed.php"><i class="fa-solid fa-wheat-awn"></i> Feed Consumption</a>
    <a href="/hoglog_piggery/modules/expenses/list_expenses.php"><i class="fa-solid fa-money-bill"></i> Expenses</a>
    <a href="/hoglog_piggery/modules/growth_summary.php"><i class="fa-solid fa-chart-simple"></i> Growth Summary</a>
    <a href="/hoglog_piggery/modules/batches/profile/list_batches.php"><i class="fa-solid fa-arrow-left"></i> Back to Batch List</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
    <h5 class="m-0"><i class="fa-solid fa-eye"></i> Batch Details</h5>
    <span class="text-secondary">HogLog Smart Piggery System</span>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="card p-3 shadow-sm">
        <h4 class="fw-bold mb-3">Batch Details - <?= htmlspecialchars($b['batch_no']) ?></h4>
        <table class="table table-hover align-middle">
            <tbody>
                <tr><th>Building</th><td><?= htmlspecialchars($b['building_position']) ?></td></tr>
                <tr><th>Total Pigs</th><td><?= $b['num_pigs_total'] ?> (♂ <?= $b['num_male'] ?> / ♀ <?= $b['num_female'] ?>)</td></tr>
                <tr><th>Breed</th><td><?= htmlspecialchars($b['breed']) ?></td></tr>
                <tr><th>Birth Date</th><td><?= $b['birth_date'] ?></td></tr>
                <tr><th>Average Birth Weight</th><td><?= $b['avg_birth_weight'] ?> kg</td></tr>
                <tr><th>Source Sow</th><td><?= htmlspecialchars($b['source_sow']) ?></td></tr>
                <tr><th>Source Boar</th><td><?= htmlspecialchars($b['source_boar']) ?></td></tr>
                <tr><th>Weaning Date</th><td><?= $b['weaning_date'] ?></td></tr>
                <tr><th>Average Weaning Weight</th><td><?= $b['avg_weaning_weight'] ?> kg</td></tr>
                <tr><th>Expected Market Date</th><td><?= $b['expected_market_date'] ?></td></tr>
                <tr><th>Status</th><td>
                    <span class="badge <?= $b['status'] == 'Active' ? 'bg-success' : 'bg-secondary' ?>">
                        <?= htmlspecialchars($b['status']) ?>
                    </span>
                </td></tr>
                <tr><th>Remarks</th><td><?= nl2br(htmlspecialchars($b['remarks'])) ?></td></tr>
            </tbody>
        </table>

        <div class="mt-3">
            <a href="edit_batch.php?id=<?= $b['batch_id'] ?>" class="btn btn-warning btn-sm me-2">✏ Edit</a>
            <a href="delete_batch.php?id=<?= $b['batch_id'] ?>" class="btn btn-danger btn-sm me-2" onclick="return confirm('Are you sure you want to delete this batch?')">🗑 Delete</a>
        </div>
    </div>

</div>

</body>
</html>
