<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

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
<title>HogLog — User Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
  --blue:#1565c0;
  --blue-100:#e3f2fd;
  --bg:#f5f7fb;
  --card:#ffffff;
  --text:#1f2937;
  --muted:#6b7280;
  --danger:#ef4444;
  --border:#eef2f7;

  /* Section theme colors */
  --farm-blue:#1565c0;
  --sow-purple:#7c3aed;
  --sow-pink:#ec4899;
  --sow-gray:#9ca3af;
  --fat-green:#10b981;
  --pig-orange:#f59e0b;
  --feed-gold:#d97706;
  --exp-red:#ef4444;
}
*{box-sizing:border-box}
html,body{margin:0;padding:0;background:var(--bg);color:var(--text);font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}

/* ===== Topbar ===== */
.topbar{
  height:64px;background:var(--card);display:flex;align-items:center;justify-content:space-between;
  padding:0 16px;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:20
}
.brand{display:flex;align-items:center;gap:10px;color:var(--blue);font-weight:700}

/* ===== Layout ===== */
.layout{display:flex;min-height:calc(100vh - 64px)}
.sidebar{
  width:260px;transition:width .25s ease;background:#fff;border-right:1px solid var(--border);
  position:sticky;top:64px;height:calc(100vh - 64px);padding:12px 10px;overflow-y:auto
}
.sidebar[data-collapsed="true"]{ width:76px; }

/* ===== Nav ===== */
.nav{display:flex;flex-direction:column;height:100%}
.nav-label{
  color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.06em;margin:10px 10px 6px
}
.nav-item{
  position:relative;
  display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:12px;color:#111827;
  text-decoration:none;margin:2px 4px;border:1px solid transparent
}
.nav-item:hover{background:var(--blue-100)}
.nav-item.active{background:var(--blue);color:#fff}
.nav-item.danger{border-color:#fde2e2;color:var(--danger)}
.nav .spacer{flex:1}

/* Tooltip styling for collapsed mode */
.sidebar[data-collapsed="true"] .nav-item[data-tooltip]:hover::after {
  content: attr(data-tooltip);
  position: absolute;
  left: 70px;
  background: rgba(21,101,192,0.95);
  color: #fff;
  font-size: 12px;
  padding: 4px 8px;
  border-radius: 6px;
  white-space: nowrap;
  transform: translateY(-50%);
  top: 50%;
  opacity: 1;
  pointer-events: none;
  z-index: 100;
}
.sidebar[data-collapsed="true"] .nav-label{display:none}
.sidebar[data-collapsed="true"] .nav-item span{display:none}
.sidebar[data-collapsed="true"] .nav-item{justify-content:center}

/* ===== Content ===== */
.content{flex:1;padding:18px 18px 28px;min-width:0}
.page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.page-head h1{margin:0;font-size:22px}
.muted{color:var(--muted)}

.card{
  background:var(--card);border:1px solid var(--border);border-radius:16px;padding:16px;
  box-shadow:0 2px 4px rgba(0,0,0,.03)
}

/* ===== Imaginary Reports ===== */
.reports-grid{
  display:grid;
  grid-template-columns: repeat(12, 1fr);
  gap:14px;
  width:100%;
}
.report-card{
  grid-column: span 6;
  background:var(--card);
  border:1px solid var(--border);
  border-radius:16px;
  padding:14px;
  box-shadow:0 2px 4px rgba(0,0,0,.03);
  display:flex;flex-direction:column;gap:10px;
  min-height:170px;
}
@media (max-width: 1000px){.report-card{grid-column: span 12;}}
.report-head{display:flex;align-items:center;justify-content:space-between}
.report-title{font-weight:700;display:flex;align-items:center;gap:8px}
.report-kpi{font-size:28px;font-weight:700;line-height:1.1}
.report-sub{color:var(--muted);font-size:12px}
.report-row{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.pill{
  background:#f3f4f6;border:1px solid #e5e7eb;border-radius:999px;
  padding:4px 8px;font-size:12px;color:#374151
}
.spark{
  width:100%;height:72px;border:1px solid var(--border);border-radius:12px;
  display:flex;align-items:center;justify-content:center;padding:6px
}
.spark svg{width:100%;height:100%}
.icon{
  width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;
  background:var(--blue-100);color:var(--blue);border:1px solid #dbeafe
}

/* ===== 📝 Floating Notepad ===== */
#notepadToggle {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #ccc;
  color: #333;
  border: none;
  border-radius: 50%;
  width: 58px;
  height: 58px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  cursor: pointer;
  box-shadow: 0 3px 8px rgba(0,0,0,0.2);
  transition: all 0.25s ease;
  z-index: 999;
}
#notepadToggle:hover { background: #bbb; }
#notepadToggle::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 70px;
  background: rgba(0,0,0,0.8);
  color: white;
  font-size: 12px;
  padding: 4px 8px;
  border-radius: 6px;
  opacity: 0;
  transition: opacity 0.2s;
  pointer-events: none;
}
#notepadToggle:hover::after { opacity: 1; }
#noteBadge {
  position: absolute;
  top: 12px;
  right: 14px;
  background: red;
  color: white;
  border-radius: 50%;
  font-size: 10px;
  width: 16px;
  height: 16px;
  display: none;
  align-items: center;
  justify-content: center;
}
#notepad {
  position: fixed;
  bottom: 90px;
  right: 24px;
  width: 350px;
  height: 260px;
  background: #f0f0f0;
  border: 1px solid var(--border);
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  padding: 12px;
  display: none;
  flex-direction: column;
  animation: slideUp 0.3s ease;
  z-index: 998;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
#notepad textarea {
  flex: 1;
  resize: none;
  border: none;
  padding: 8px;
  border-radius: 8px;
  outline: none;
  font-family: Poppins, sans-serif;
  font-size: 14px;
  background: white;
  color: #333;
}
#clearNotes {
  margin-top: 8px;
  background: var(--danger);
  border: none;
  color: white;
  padding: 6px 10px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  align-self: flex-end;
}
#clearNotes:hover { background: #c53030; }
</style>
</head>
<body>

<!-- 🧭 Topbar -->
<header class="topbar">
  <div class="brand">
    <i class="bi bi-hurricane"></i>
    <span>HogLog | Smart Piggery Management</span>
  </div>
</header>

<div class="layout">
  <!-- 🧱 Sidebar -->
  <aside class="sidebar" data-collapsed="false" id="sidebar">
  <nav class="nav">
    <a class="nav-item active" data-tooltip="Dashboard" id="dashboardBtn" href="javascript:void(0)">
      <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <div class="nav-label">Operations</div>
    <a class="nav-item" data-tooltip="Sow Dashboard" href="/hoglog_piggery/modules/sow/sow_dashboard.php">
      <i class="bi bi-piggy-bank"></i><span>Sow Dashboard</span>
    </a>
    <a class="nav-item" data-tooltip="Fatteners Dashboard" href="/hoglog_piggery/modules/batches/dashboard/batch_fattener.php">
      <i class="bi bi-boxes"></i><span>Fatteners Dashboard</span>
    </a>
    <a class="nav-item" data-tooltip="Piglets Dashboard" href="/hoglog_piggery/modules/piglets/piglets_dashboard.php">
      <i class="bi bi-heart-pulse"></i><span>Piglets Dashboard</span>
    </a>
    <!-- ✅ Updated link -->
    <a class="nav-item" data-tooltip="Expenses Dashboard" href="http://localhost/hoglog_piggery/modules/batches/expenses/expenses_dashboard.php">
      <i class="bi bi-cash-stack"></i><span>Expenses Dashboard</span>
    </a>
    <a class="nav-item" data-tooltip="Feed Dashboard" href="/hoglog_piggery/modules/farm_wide_feed/feed_dashboard.php">
      <i class="bi bi-bag-check"></i><span>Feed Dashboard</span>
    </a>
    <div class="spacer"></div>
    <a class="nav-item danger" data-tooltip="Logout" href="/hoglog_piggery/modules/users/user_logout.php">
      <i class="bi bi-box-arrow-right"></i><span>Logout</span>
    </a>
    
  </nav>
</aside>


  <!-- 📊 Main Area -->
  <main class="content">
    <div class="page-head">
      <h1>Dashboard Overview</h1>
    </div>

    <!-- 🧩 Imaginary Reports -->
    <section class="card overview-slot" style="display:block">
      <div class="reports-grid" id="imaginaryReports">
        <!-- FARM OVERVIEW (Blue bars) -->
        <div class="report-card" id="card-farm">
          <div class="report-head">
            <div class="report-title"><span class="icon"><i class="bi bi-graph-up"></i></span> Farm Overview</div>
            <div class="report-sub">Population mix</div>
          </div>
          <div class="report-kpi" id="kpi-farm">—</div>
          <div class="report-row">
            <span class="pill">Sows: <b id="kpi-farm-sows">—</b></span>
            <span class="pill">Fatteners: <b id="kpi-farm-fat">—</b></span>
            <span class="pill">Piglets: <b id="kpi-farm-pig">—</b></span>
          </div>
          <div class="spark">
            <svg viewBox="0 0 100 30" id="svg-farm">
              <!-- 3 bars -->
              <rect x="18" y="30" width="12" height="0" fill="var(--farm-blue)"></rect>
              <rect x="44" y="30" width="12" height="0" fill="var(--farm-blue)"></rect>
              <rect x="70" y="30" width="12" height="0" fill="var(--farm-blue)"></rect>
            </svg>
          </div>
        </div>

        <!-- SOW REPORT (Stage bars: Gilt gray, Gestating purple, Lactating pink) -->
        <div class="report-card" id="card-sow">
          <div class="report-head">
            <div class="report-title"><span class="icon"><i class="bi bi-piggy-bank"></i></span> Sow Report</div>
            <div class="report-sub">Stages</div>
          </div>
          <div class="report-kpi"><span id="kpi-sow-total">—</span> Active Sows</div>
          <div class="report-row">
            <span class="pill">Gilt: <b id="kpi-gilt">—</b></span>
            <span class="pill">Gestating: <b id="kpi-gest">—</b></span>
            <span class="pill">Lactating: <b id="kpi-lact">—</b></span>
          </div>
          <div class="spark">
            <svg viewBox="0 0 100 30" id="svg-sow">
              <rect x="18" y="30" width="12" height="0" fill="var(--sow-gray)"></rect>
              <rect x="44" y="30" width="12" height="0" fill="var(--sow-purple)"></rect>
              <rect x="70" y="30" width="12" height="0" fill="var(--sow-pink)"></rect>
            </svg>
          </div>
        </div>

        <!-- FATTENERS (Green smooth curve) -->
        <div class="report-card" id="card-fat">
          <div class="report-head">
            <div class="report-title"><span class="icon"><i class="bi bi-boxes"></i></span> Fatteners</div>
            <div class="report-sub">Growth curve</div>
          </div>
          <div class="report-kpi"><span id="kpi-fat-avg">—</span> kg avg</div>
          <div class="report-row">
            <span class="pill">Total: <b id="kpi-fat-count">—</b></span>
            <span class="pill">FCR: <b id="kpi-fat-fcr">—</b></span>
          </div>
          <div class="spark">
            <svg viewBox="0 0 100 30" id="svg-fat">
              <path d="" fill="none" stroke="var(--fat-green)" stroke-width="2"></path>
            </svg>
          </div>
        </div>

        <!-- PIGLETS (Orange pulsing dots) -->
        <div class="report-card" id="card-piglets">
          <div class="report-head">
            <div class="report-title"><span class="icon"><i class="bi bi-heart-pulse"></i></span> Piglets</div>
            <div class="report-sub">Nursery vitality</div>
          </div>
          <div class="report-kpi"><span id="kpi-pig-surv">—</span> survival</div>
          <div class="report-row">
            <span class="pill">Total: <b id="kpi-pig-count">—</b></span>
            <span class="pill">Litters: <b id="kpi-pig-lit">—</b></span>
          </div>
          <div class="spark">
            <svg viewBox="0 0 100 30" id="svg-pig">
              <circle cx="20" cy="15" r="2" fill="var(--pig-orange)"></circle>
              <circle cx="45" cy="15" r="2" fill="var(--pig-orange)"></circle>
              <circle cx="70" cy="15" r="2" fill="var(--pig-orange)"></circle>
            </svg>
          </div>
        </div>

        <!-- FEED INVENTORY (Gold horizontal fill) -->
        <div class="report-card" id="card-feed">
          <div class="report-head">
            <div class="report-title"><span class="icon"><i class="bi bi-bag-check"></i></span> Feed Inventory</div>
            <div class="report-sub">Stock & usage</div>
          </div>
          <div class="report-kpi"><span id="kpi-feed-stock">—</span> kg in stock</div>
          <div class="report-row">
            <span class="pill">Used (7d): <b id="kpi-feed-used">—</b> kg</span>
            <span class="pill">Avg/day: <b id="kpi-feed-avg">—</b> kg</span>
          </div>
          <div class="spark">
            <svg viewBox="0 0 100 30" id="svg-feed">
              <rect x="5" y="8" width="90" height="14" rx="7" ry="7" fill="#fff" stroke="var(--border)"></rect>
              <rect x="5" y="8" width="0"  height="14" rx="7" ry="7" fill="var(--feed-gold)"></rect>
            </svg>
          </div>
        </div>

        <!-- EXPENSES (Red donut) -->
        <div class="report-card" id="card-exp">
          <div class="report-head">
            <div class="report-title"><span class="icon"><i class="bi bi-cash-stack"></i></span> Expenses</div>
            <div class="report-sub">This month</div>
          </div>
          <div class="report-kpi">₱ <span id="kpi-exp-total">—</span></div>
          <div class="report-row">
            <span class="pill">Feed: <b id="kpi-exp-feed">—</b>%</span>
            <span class="pill">Vet: <b id="kpi-exp-vet">—</b>%</span>
            <span class="pill">Other: <b id="kpi-exp-other">—</b>%</span>
          </div>
          <div class="spark">
            <svg viewBox="0 0 100 30" id="svg-exp">
              <!-- Donut base (circle path) -->
              <g transform="translate(50,15)">
                <circle r="12" stroke="#eee" stroke-width="8" fill="none"></circle>
                <circle id="arc-feed" r="12" stroke="var(--exp-red)" stroke-width="8" fill="none" stroke-linecap="round" transform="rotate(-90)"></circle>
                <circle id="arc-vet"  r="12" stroke="#ef9a9a"      stroke-width="8" fill="none" stroke-linecap="round" transform="rotate(-90)"></circle>
                <circle id="arc-oth"  r="12" stroke="#fca5a5"      stroke-width="8" fill="none" stroke-linecap="round" transform="rotate(-90)"></circle>
              </g>
            </svg>
          </div>
        </div>

      </div>
    </section>
  </main>
</div>

<!-- 📝 Floating Notepad -->
<button id="notepadToggle" data-tooltip="Leave a note">
  <i class="bi bi-journal-text"></i><div id="noteBadge">1</div>
</button>
<div id="notepad">
  <textarea id="noteContent" placeholder="Write your note here..."></textarea>
  <button id="clearNotes">🗑️ Clear Notes</button>
</div>

<script>
// Sidebar collapse toggle
(() => {
  const s = document.getElementById('sidebar'), b = document.getElementById('dashboardBtn');
  if(localStorage.getItem('hoglog_sidebar_collapsed')==='true')s.dataset.collapsed='true';
  b.onclick=()=>{const c=s.dataset.collapsed==='true';s.dataset.collapsed=!c;localStorage.setItem('hoglog_sidebar_collapsed',!c)};
})();

// Notepad logic
(() => {
  const t=document.getElementById('notepadToggle'),n=document.getElementById('notepad'),c=document.getElementById('noteContent'),
  clr=document.getElementById('clearNotes'),b=document.getElementById('noteBadge');
  const note=localStorage.getItem('hoglog_note')||'';c.value=note;if(note.trim())b.style.display='flex';
  t.onclick=()=>{n.style.display=n.style.display==='flex'?'none':'flex'};
  c.oninput=()=>{localStorage.setItem('hoglog_note',c.value);b.style.display=c.value.trim()?'flex':'none'};
  clr.onclick=()=>{c.value='';localStorage.removeItem('hoglog_note');b.style.display='none'};
})();

// ===== Imaginary Reports: color-coded & animated =====
(() => {
  // Random helpers
  const rnd=(a,b)=>Math.round(a + Math.random()*(b-a));
  const rfp=(a,b)=> (a + Math.random()*(b-a)); // float

  // Seed data
  const sows = rnd(40,70), fat = rnd(180,300), pig = rnd(240,420);
  const gilt=rnd(6,15), gest=rnd(12,24), lact=rnd(8,18);
  const totalSow = gilt+gest+lact;

  const fatCount = fat, fatAvg = rfp(60,105).toFixed(1), fcr = (rfp(2.0,2.8)).toFixed(2);
  const pigCount = pig, pigLit = rnd(20,40), pigSurv = (rfp(90,98)).toFixed(1)+'%';

  const feedStock = rnd(800,2300), feedUsed7 = rnd(600,1400), feedAvg = rnd(160,260);
  const expTotal = rnd(30000,120000);
  const expFeed = rnd(45,60), expVet = rnd(10,20), expOther = 100 - (expFeed+expVet);

  // KPIs
  document.getElementById('kpi-farm').textContent = (sows+fat+pig);
  document.getElementById('kpi-farm-sows').textContent = sows;
  document.getElementById('kpi-farm-fat').textContent = fat;
  document.getElementById('kpi-farm-pig').textContent = pig;

  document.getElementById('kpi-sow-total').textContent = totalSow;
  document.getElementById('kpi-gilt').textContent = gilt;
  document.getElementById('kpi-gest').textContent = gest;
  document.getElementById('kpi-lact').textContent = lact;

  document.getElementById('kpi-fat-avg').textContent = fatAvg;
  document.getElementById('kpi-fat-count').textContent = fatCount;
  document.getElementById('kpi-fat-fcr').textContent = fcr;

  document.getElementById('kpi-pig-surv').textContent = pigSurv;
  document.getElementById('kpi-pig-count').textContent = pigCount;
  document.getElementById('kpi-pig-lit').textContent = pigLit;

  document.getElementById('kpi-feed-stock').textContent = feedStock;
  document.getElementById('kpi-feed-used').textContent = feedUsed7;
  document.getElementById('kpi-feed-avg').textContent = feedAvg;

  document.getElementById('kpi-exp-total').textContent = expTotal.toLocaleString();
  document.getElementById('kpi-exp-feed').textContent = expFeed;
  document.getElementById('kpi-exp-vet').textContent = expVet;
  document.getElementById('kpi-exp-other').textContent = expOther;

  // ===== FARM OVERVIEW: animate 3 blue bars =====
  (function farmBars(){
    const svg = document.getElementById('svg-farm');
    const rects = svg.querySelectorAll('rect');
    const vals = [sows, fat, pig];
    const max = Math.max(...vals);
    const h = v => 24 * (v/max); // 0..24 height

    rects.forEach((r, i) => {
      const target = h(vals[i]);
      let cur = 0;
      const yBase = 28; // bottom baseline
      function step(){
        cur += (target - cur) * 0.15;
        r.setAttribute('y', yBase - cur);
        r.setAttribute('height', cur);
        if (Math.abs(target - cur) > 0.5) requestAnimationFrame(step);
        else { r.setAttribute('y', yBase - target); r.setAttribute('height', target); }
      }
      requestAnimationFrame(step);
    });
  })();

  // ===== SOW REPORT: animate stage bars (gray/purple/pink) =====
  (function sowBars(){
    const svg = document.getElementById('svg-sow');
    const rects = svg.querySelectorAll('rect');
    const vals = [gilt, gest, lact];
    const max = Math.max(...vals);
    const h = v => 24 * (v/max);
    rects.forEach((r, i) => {
      const target = h(vals[i]);
      let cur = 0; const yb = 28;
      function step(){
        cur += (target - cur) * 0.18;
        r.setAttribute('y', yb - cur);
        r.setAttribute('height', cur);
        if (Math.abs(target - cur) > 0.5) requestAnimationFrame(step);
        else { r.setAttribute('y', yb - target); r.setAttribute('height', target); }
      }
      requestAnimationFrame(step);
    });
  })();

  // ===== FATTENERS: smooth green line (animated sine drift) =====
  (function fatLine(){
    const path = document.querySelector('#svg-fat path');
    function gen(t){
      let d = '';
      for (let i=0;i<10;i++){
        const x = i*(100/9);
        const y = 15 + Math.sin(t/700 + i*0.6)*6 + Math.cos(t/900 + i)*2;
        d += (i===0 ? 'M' : 'L') + x + ' ' + y;
      }
      return d;
    }
    function loop(ts){
      path.setAttribute('d', gen(ts));
      requestAnimationFrame(loop);
    }
    requestAnimationFrame(loop);
  })();

  // ===== PIGLETS: pulsing orange dots =====
  (function pigDots(){
    const svg = document.getElementById('svg-pig');
    const dots = svg.querySelectorAll('circle');
    let t = 0;
    function loop(){
      t += 0.08;
      dots.forEach((c, i) => {
        const r = 2 + Math.abs(Math.sin(t + i*0.8))*2;
        c.setAttribute('r', r.toFixed(2));
      });
      requestAnimationFrame(loop);
    }
    requestAnimationFrame(loop);
  })();

  // ===== FEED: horizontal fill (stock level) =====
  (function feedFill(){
    const svg = document.getElementById('svg-feed');
    const fill = svg.querySelectorAll('rect')[1];
    const pct = Math.max(0.05, Math.min(1, feedStock/2500)); // normalize
    const targetW = 90 * pct;
    let cur = 0;
    function step(){
      cur += (targetW - cur) * 0.1;
      fill.setAttribute('width', cur);
      if (Math.abs(targetW - cur) > 0.5) requestAnimationFrame(step);
      else fill.setAttribute('width', targetW);
    }
    requestAnimationFrame(step);
  })();

  // ===== EXPENSES: animated donut distribution =====
  (function expensesDonut(){
    const C = 2*Math.PI*12; // circumference
    const feedArc = document.getElementById('arc-feed');
    const vetArc  = document.getElementById('arc-vet');
    const othArc  = document.getElementById('arc-oth');

    const feedLen = C * (expFeed/100);
    const vetLen  = C * (expVet/100);
    const othLen  = C * (expOther/100);

    // Set dash arrays
    feedArc.setAttribute('stroke-dasharray', `${feedLen} ${C-feedLen}`);
    vetArc.setAttribute('stroke-dasharray',  `${vetLen} ${C-vetLen}`);
    othArc.setAttribute('stroke-dasharray',  `${othLen} ${C-othLen}`);

    // Animate offsets so arcs appear in sequence
    let off = C;
    function animateArc(el, len, cb){
      let cur = C;
      function step(){
        cur += (off - cur) * 0.12;
        el.setAttribute('stroke-dashoffset', cur);
        if (Math.abs(off - cur) > 0.8) requestAnimationFrame(step);
        else { el.setAttribute('stroke-dashoffset', off); cb && cb(); }
      }
      requestAnimationFrame(step);
      off -= len;
    }
    animateArc(feedArc, feedLen, () => animateArc(vetArc, vetLen, () => animateArc(othArc, othLen)));
  })();
})();
</script>

</body>
</html>
