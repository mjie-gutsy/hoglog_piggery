<?php 
require_once __DIR__ . '/../../../config/db.php'; // Adjust path to your db.php

// Get batch number from URL
if (!isset($_GET['id'])) die('Invalid request');
$batch_id = $_GET['id'];

// Fetch batch details
$stmt = $pdo->prepare("SELECT * FROM batch_records WHERE batch_id=?");
$stmt->execute([$batch_id]);
$batch = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$batch) die('Batch not found');
$batch_no = $batch['batch_no'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ear_tag_no = $_POST['ear_tag_no'];
    $sex = $_POST['sex'];
    $breed_line = $_POST['breed_line'];
    $birth_date = $_POST['birth_date'];
    $weaning_date = $_POST['weaning_date'];
    $weaning_weight = $_POST['weaning_weight'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("INSERT INTO fattener_records 
        (ear_tag_no, batch_no, sex, breed_line, birth_date, weaning_date, weaning_weight, status, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$ear_tag_no, $batch_no, $sex, $breed_line, $birth_date, $weaning_date, $weaning_weight, $status, $notes]);

    header("Location: list_profile.php?id=$batch_id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Fattener Profile - <?= htmlspecialchars($batch_no) ?></title>

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
.card { padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: #fff; }
.btn-primary { border-radius: 6px; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center mb-4"><i class="fa-solid fa-piggy-bank"></i> HogLog</h4>
    <a href="/hoglog_piggery/modules/batches/profile/view_batch.php?id=<?= $batch_id ?>"><i class="fa-solid fa-list"></i> Batch Details</a>
    <a href="#" class="active"><i class="fa-solid fa-user-plus"></i> Add Fattener</a>
    <a href="/hoglog_piggery/modules/fattener/profile/list_profile.php?id=<?= $batch_id ?>"><i class="fa-solid fa-users"></i> Fattener List</a>
    <a href="/hoglog_piggery/modules/batches/feed/list_feed.php"><i class="fa-solid fa-wheat-awn"></i> Feed Consumption</a>
    <a href="/hoglog_piggery/modules/expenses/list_expenses.php"><i class="fa-solid fa-money-bill"></i> Expenses</a>
    <a href="/hoglog_piggery/modules/growth_summary.php"><i class="fa-solid fa-chart-simple"></i> Growth Summary</a>
    <a href="/hoglog_piggery/modules/batches/profile/list_batches.php"><i class="fa-solid fa-arrow-left"></i> Back to Batch List</a>
</div>

<!-- TOPBAR -->
<div class="topbar">
    <h5 class="m-0"><i class="fa-solid fa-user-plus"></i> Add Fattener - <?= htmlspecialchars($batch_no) ?></h5>
    <span class="text-secondary">HogLog Smart Piggery System</span>
</div>

<!-- CONTENT -->
<div class="content">
    <div class="card">
        <form method="POST">
            <h4 class="mb-4 text-primary"><i class="fa-solid fa-piggy-bank"></i> Add Fattener Profile</h4>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Ear Tag No.</label>
                    <input type="text" name="ear_tag_no" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Batch No.</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($batch_no) ?>" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Sex</label>
                    <select name="sex" class="form-select" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Breed Line</label>
                    <input type="text" name="breed_line" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active">Active</option>
                        <option value="Market Ready">Market Ready</option>
                        <option value="Sold">Sold</option>
                        <option value="Dead">Dead</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Birth Date</label>
                    <input type="date" name="birth_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Weaning Date</label>
                    <input type="date" name="weaning_date" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Weaning Weight (kg)</label>
                <input type="number" step="0.01" name="weaning_weight" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <!-- Changed this button -->
                <button type="submit" class="btn btn-primary">Add Record</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
