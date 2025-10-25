<?php
session_start();

// ✅ Automatically find db.php in the project root
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/hoglog_piggery/config/db.php';
if (!file_exists($rootPath)) {
    die("Database configuration file not found at: " . htmlspecialchars($rootPath));
}
require_once $rootPath;

// ✅ Check login session
if (!isset($_SESSION['farm_id'])) {
  header("Location: /hoglog_piggery/farm_login.php");
  exit;
}

$farm_name = $_SESSION['farm_name'] ?? 'HogLog Farm';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>HogLog — Expenses Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root{
  --blue:#1565c0;--bg:#f5f7fb;--card:#fff;--text:#1f2937;--muted:#6b7280;--border:#eef2f7;
  --exp-sow:#7c3aed;--exp-fat:#10b981;--exp-pig:#f59e0b;
  --exp-water:#38bdf8;--exp-electric:#f87171;--exp-admin:#6b7280;
  --exp-farmvet:#ec4899;--exp-labor:#0ea5e9;
}
*{box-sizing:border-box}
body{margin:0;padding:0;background:var(--bg);font-family:Poppins,sans-serif;color:var(--text)}

/* Layout */
.topbar{height:64px;background:var(--card);display:flex;align-items:center;justify-content:space-between;
padding:0 16px;border-bottom:1px solid var(--border)}
.brand{display:flex;align-items:center;gap:10px;color:var(--blue);font-weight:700}
.layout{display:flex;min-height:calc(100vh - 64px)}
.sidebar{width:260px;background:#fff;border-right:1px solid var(--border);padding:12px 10px}
.nav{display:flex;flex-direction:column}
.nav-item{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:12px;
text-decoration:none;color:#111827;margin:2px 4px}
.nav-item:hover{background:#e3f2fd}
.nav-item.active{background:var(--blue);color:#fff}
.nav-label{color:var(--muted);font-size:12px;text-transform:uppercase;margin:10px 10px 6px}
.content{flex:1;padding:18px}

/* Report Cards */
.reports-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}
.report-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:16px;
box-shadow:0 2px 4px rgba(0,0,0,.03);display:flex;flex-direction:column;gap:10px;min-height:160px;position:relative;overflow:hidden}
.report-title{font-weight:700;display:flex;align-items:center;gap:8px}
.report-kpi{font-size:26px;font-weight:700}
.report-sub{color:var(--muted);font-size:13px}
.spark{width:100%;height:70px;display:flex;align-items:flex-end;justify-content:space-between;gap:3px}
.bar{flex:1;border-radius:4px 4px 0 0;transition:height .3s ease}
.icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff}
</style>
</head>
<body>

<header class="topbar">
  <div class="brand"><i class="bi bi-wallet2"></i> <span>HogLog | Expenses Dashboard</span></div>
</header>

<div class="layout">
  <aside class="sidebar">
    <div class="nav-label">Expenses</div>
    <a href="#" class="nav-item active"><i class="bi bi-speedometer2"></i>Dashboard</a>
    <a href="#" class="nav-item"><i class="bi bi-piggy-bank"></i>Sow Expenses</a>
    <a href="#" class="nav-item"><i class="bi bi-boxes"></i>Fattener Expenses</a>
    <a href="#" class="nav-item"><i class="bi bi-heart-pulse"></i>Piglet Expenses</a>
    <a href="#" class="nav-item"><i class="bi bi-droplet"></i>Water Bill</a>
    <a href="#" class="nav-item"><i class="bi bi-lightning-charge"></i>Electric Bill</a>
    <a href="#" class="nav-item"><i class="bi bi-building"></i>Administrative</a>
    <a href="#" class="nav-item"><i class="bi bi-capsule"></i>Farmvet</a>
    <a href="#" class="nav-item"><i class="bi bi-person-workspace"></i>Labor</a>
      <!-- ✅ Updated link -->
    <a href="/hoglog_piggery/modules/users/user_dashboard.php" class="text-warning">
        ⬅️ Back to User Dashboard
    </a>
  </aside>

  <main class="content">
    <h1>Dashboard Overview</h1>
    <div class="reports-grid" id="expReports">
      <div class="report-card" style="--clr:var(--exp-sow)">
        <div class="report-title"><span class="icon" style="background:var(--exp-sow)"><i class="bi bi-piggy-bank"></i></span> Sow Expenses</div>
        <div class="report-kpi" id="sowExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-sow"></div>
      </div>

      <div class="report-card" style="--clr:var(--exp-fat)">
        <div class="report-title"><span class="icon" style="background:var(--exp-fat)"><i class="bi bi-boxes"></i></span> Fattener Expenses</div>
        <div class="report-kpi" id="fatExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-fat"></div>
      </div>

      <div class="report-card" style="--clr:var(--exp-pig)">
        <div class="report-title"><span class="icon" style="background:var(--exp-pig)"><i class="bi bi-heart-pulse"></i></span> Piglet Expenses</div>
        <div class="report-kpi" id="pigExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-pig"></div>
      </div>

      <div class="report-card" style="--clr:var(--exp-water)">
        <div class="report-title"><span class="icon" style="background:var(--exp-water)"><i class="bi bi-droplet"></i></span> Water Bill</div>
        <div class="report-kpi" id="waterExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-water"></div>
      </div>

      <div class="report-card" style="--clr:var(--exp-electric)">
        <div class="report-title"><span class="icon" style="background:var(--exp-electric)"><i class="bi bi-lightning-charge"></i></span> Electric Bill</div>
        <div class="report-kpi" id="electricExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-electric"></div>
      </div>

      <div class="report-card" style="--clr:var(--exp-admin)">
        <div class="report-title"><span class="icon" style="background:var(--exp-admin)"><i class="bi bi-building"></i></span> Administrative</div>
        <div class="report-kpi" id="adminExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-admin"></div>
      </div>

      <div class="report-card" style="--clr:var(--exp-farmvet)">
        <div class="report-title"><span class="icon" style="background:var(--exp-farmvet)"><i class="bi bi-capsule"></i></span> Farmvet</div>
        <div class="report-kpi" id="vetExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-vet"></div>
      </div>

      <div class="report-card" style="--clr:var(--exp-labor)">
        <div class="report-title"><span class="icon" style="background:var(--exp-labor)"><i class="bi bi-person-workspace"></i></span> Labor</div>
        <div class="report-kpi" id="laborExp">₱ —</div>
        <div class="report-sub">This month</div>
        <div class="spark" id="spark-labor"></div>
      </div>
    </div>
  </main>
</div>

<script>
(() => {
  const rnd = (a,b)=>Math.round(a + Math.random()*(b-a));
  const expenses = {
    sow:rnd(10000,30000),
    fat:rnd(20000,60000),
    pig:rnd(8000,25000),
    water:rnd(2000,7000),
    electric:rnd(4000,12000),
    admin:rnd(5000,15000),
    vet:rnd(3000,10000),
    labor:rnd(10000,25000)
  };

  Object.keys(expenses).forEach(k=>{
    // show total
    document.getElementById(k+'Exp').textContent = '₱ ' + expenses[k].toLocaleString();

    // make bars
    const container = document.getElementById('spark-'+k);
    const color = getComputedStyle(document.documentElement).getPropertyValue('--exp-'+k);
    for(let i=0;i<3;i++){
      const bar = document.createElement('div');
      bar.className='bar';
      bar.style.background=color;
      bar.style.height = rnd(20,70) + '%';
      container.appendChild(bar);
    }
  });
})();
</script>

</body>
</html>
