<?php
require_once __DIR__ . '/../../../config/db.php';

// Validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request");
}

$id = $_GET['id'];

// Fetch record
$stmt = $pdo->prepare("SELECT * FROM fattener_records WHERE fattener_id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$record) {
    die("Record not found.");
}

// Fetch batch info
$batch_stmt = $pdo->prepare("SELECT * FROM batch_records WHERE batch_no = ?");
$batch_stmt->execute([$record['batch_no']]);
$batch = $batch_stmt->fetch(PDO::FETCH_ASSOC);

// Update record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ear_tag_no = $_POST['ear_tag_no'];
    $batch_no = $_POST['batch_no'];
    $sex = $_POST['sex'];
    $breed_line = $_POST['breed_line'];
    $birth_date = $_POST['birth_date'];
    $weaning_date = $_POST['weaning_date'];
    $weaning_weight = $_POST['weaning_weight'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    $update = $pdo->prepare("UPDATE fattener_records SET 
        ear_tag_no=?, batch_no=?, sex=?, breed_line=?, birth_date=?, weaning_date=?, 
        weaning_weight=?, status=?, notes=? WHERE fattener_id=?");
    $update->execute([$ear_tag_no, $batch_no, $sex, $breed_line, $birth_date, $weaning_date, 
                      $weaning_weight, $status, $notes, $id]);

    header("Location: view_profile.php?id=" . $id . "&updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Fattener - <?= htmlspecialchars($record['ear_tag_no']) ?> | HogLog</title>
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
.card { border-radius: 10px; margin-bottom:20px; box-shadow: 0 3px 8px rgba(0,0,0,0.05); }
.card-header { background-color:#38c0f0; color:#fff; font-weight:600; }

/* Buttons */
.btn-save { background-color:#0d6efd; color:#fff; border:none; }
.btn-back { background-color:#6c757d; color:#fff; border:none; }
.btn-save:hover, .btn-back:hover { opacity:0.85; }

.form-label { font-weight:600; color:#212529; }
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
    <h5 class="m-0"><i class="fa-solid fa-pen-to-square"></i> Edit Fattener Profile</h5>
    <span class="text-secondary">HogLog Smart Piggery System</span>
</div>

<!-- Content -->
<div class="content">

    <div class="card p-4 shadow-sm">
        <div class="card-header">Edit Fattener - <?= htmlspecialchars($record['ear_tag_no']) ?></div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ear Tag No</label>
                        <input type="text" name="ear_tag_no" class="form-control" value="<?= htmlspecialchars($record['ear_tag_no']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Batch No</label>
                        <input type="text" name="batch_no" class="form-control" value="<?= htmlspecialchars($record['batch_no']) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Sex</label>
                        <select name="sex" class="form-select" required>
                            <option value="Male" <?= $record['sex']==='Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $record['sex']==='Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Breed Line</label>
                        <input type="text" name="breed_line" class="form-control" value="<?= htmlspecialchars($record['breed_line']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Active" <?= $record['status']==='Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Market Ready" <?= $record['status']==='Market Ready' ? 'selected' : '' ?>>Market Ready</option>
                            <option value="Sold" <?= $record['status']==='Sold' ? 'selected' : '' ?>>Sold</option>
                            <option value="Dead" <?= $record['status']==='Dead' ? 'selected' : '' ?>>Dead</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Birth Date</label>
                        <input type="date" name="birth_date" class="form-control" value="<?= htmlspecialchars($record['birth_date']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Weaning Date</label>
                        <input type="date" name="weaning_date" class="form-control" value="<?= htmlspecialchars($record['weaning_date']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Weaning Weight (kg)</label>
                    <input type="number" step="0.01" name="weaning_weight" class="form-control" value="<?= htmlspecialchars($record['weaning_weight']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($record['notes']) ?></textarea>
                </div>

                <div class="text-end">
                    <a href="view_profile.php?id=<?= $record['fattener_id'] ?>" class="btn btn-back me-2">
                        <i class="fa-solid fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</body>
</html>
