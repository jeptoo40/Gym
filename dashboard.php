

<?php
session_start();

// If not logged in, redirect back to login page
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user_name']; // we stored this at login.php after successful login
$userRole = "Member"; // later you can make this dynamic from DB
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard | Finess Fitness</title>

  <!-- Icons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>

  <!-- Chart.js (for the weekly performance graph) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    :root{
      --bg:#0b0e13;
      --panel:#121722;
      --muted:#8da3b8;
      --text:#eaf2ff;
      --accent:#39e6dd;
      --accent-2:#2ac3bd;
      --danger:#ff5a5f;
      --warning:#ffb020;
      --success:#22c55e;
      --card:#161c2a;
      --border:rgba(255,255,255,.06);
      --shadow:0 10px 25px rgba(0,0,0,.25);
      --radius:16px;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Ubuntu,"Helvetica Neue",Arial,sans-serif;
      color:var(--text);
      background:linear-gradient(180deg,#0a0d12 0%, #0c111b 100%);
      display:flex;
    }

    /* Layout */
    .layout{
      display:grid;
      grid-template-columns: 260px 1fr;
      width:100%;
      min-height:100vh;
    }
    @media (max-width: 980px){
      .layout{
        grid-template-columns: 80px 1fr;
      }
      .sidebar .label{display:none}
      .sidebar .dropdown > button .chev{display:none}
      .sidebar .brand .name{display:none}
      .profile .meta{display:none}
    }
    @media (max-width: 680px){
      .layout{
        grid-template-columns: 1fr;
      }
      .sidebar{
        position:fixed; inset:0 auto 0 0; width:280px; transform:translateX(-100%);
        transition:.25s ease; z-index:9999;
      }
      .sidebar.open{transform:none}
      .mobile-topbar{display:flex}
    }

    /* Sidebar */
    .sidebar{
      background:rgba(18,23,34,.85);
      backdrop-filter: blur(10px);
      border-right:1px solid var(--border);
      padding:20px 14px;
      display:flex; flex-direction:column; gap:14px;
    }
    .brand{
      display:flex; align-items:center; gap:10px; padding:6px 10px; border-radius:12px;
    }
    .avatar{
      width:40px; height:40px; border-radius:50%;
      background:linear-gradient(135deg, var(--accent), var(--accent-2));
      display:grid; place-items:center; color:#02161a; font-weight:800;
      box-shadow: var(--shadow);
    }
    .brand .name{
      font-weight:800; letter-spacing:.3px;
    }
    .brand .name span{ color:var(--accent); }

    nav{display:flex; flex-direction:column; gap:6px; margin-top:4px}
    .nav-item, .dropdown > button{
      display:flex; align-items:center; gap:12px;
      padding:12px 12px; border-radius:12px;
      color:var(--muted); background:transparent; border:0; width:100%;
      cursor:pointer; transition:.2s ease;
    }
    .nav-item:hover, .dropdown > button:hover{ background:rgba(255,255,255,.04); color:var(--text); }
    .nav-item.active{ background:linear-gradient(180deg, rgba(57,230,221,.18), rgba(57,230,221,.08)); color:var(--text); border:1px solid rgba(57,230,221,.35); }

    .bx{font-size:20px}
    .label{font-weight:600}
    .spacer{height:8px}

    /* Dropdown */
    .dropdown{display:flex; flex-direction:column; gap:6px}
    .dropdown > button{justify-content:space-between}
    .dropdown .left{display:flex; align-items:center; gap:12px}
    .dropdown .menu{
      display:none; flex-direction:column; gap:6px; padding-left:40px;
    }
    .dropdown.open .menu{display:flex}
    .dropdown .menu a{
      color:var(--muted); text-decoration:none; padding:8px 10px; border-radius:10px;
    }
    .dropdown .menu a:hover{ background:rgba(255,255,255,.05); color:var(--text); }

    /* Profile block */
    .profile{
      margin-top:auto; padding:12px; border:1px solid var(--border); border-radius:14px;
      background:rgba(255,255,255,.03);
      display:flex; align-items:center; gap:10px;
    }
    .profile .pic{
      width:44px; height:44px; border-radius:50%;
      background:url('https://images.unsplash.com/photo-1598970434795-0c54fe7c0642?q=80&w=160&auto=format&fit=crop') center/cover no-repeat;
      border:2px solid rgba(255,255,255,.12);
    }
    .profile .name{ font-weight:700; }
    .profile .role{ color:var(--muted); font-size:12px; margin-top:-2px}

    /* Mobile top bar */
    .mobile-topbar{
      display:none; align-items:center; justify-content:space-between;
      padding:14px 16px; border-bottom:1px solid var(--border); background:rgba(18,23,34,.85);
      position:sticky; top:0; z-index:50;
    }
    .hamburger{
      width:42px; height:42px; display:grid; place-items:center;
      border-radius:12px; border:1px solid var(--border); background:#0f1420; color:var(--text); cursor:pointer;
    }

    /* Main */
    .main{
      padding:24px min(32px, 4vw);
      display:flex; flex-direction:column; gap:22px;
    }
    .welcome{
      background:linear-gradient(180deg, rgba(57,230,221,.18), rgba(57,230,221,.04));
      border:1px solid rgba(57,230,221,.35);
      border-radius:var(--radius);
      padding:18px 18px;
      display:flex; align-items:center; justify-content:space-between; gap:16px;
      box-shadow: var(--shadow);
    }
    .welcome .title{font-size:20px; font-weight:800}
    .welcome .subtitle{color:var(--muted); margin-top:4px; font-size:14px}
    .actions{display:flex; gap:10px; flex-wrap:wrap}
    .btn{
      display:inline-flex; align-items:center; gap:8px;
      padding:10px 14px; border-radius:12px; border:1px solid var(--border);
      background:var(--panel); color:var(--text); text-decoration:none; cursor:pointer;
      box-shadow: var(--shadow);
    }
    .btn.primary{ background:linear-gradient(180deg, var(--accent), var(--accent-2)); color:#052b2a; border-color:transparent; font-weight:800}
    .btn:hover{filter:brightness(1.05)}

    /* Cards grid */
    .grid{
      display:grid; gap:16px;
      grid-template-columns: repeat(12, 1fr);
    }
    .card{
      background:var(--card); border:1px solid var(--border); border-radius:14px; padding:16px;
      box-shadow: var(--shadow);
    }
    .kpi{
      grid-column: span 4;
      display:flex; align-items:center; justify-content:space-between;
    }
    .kpi .meta{color:var(--muted); font-size:13px}
    .kpi .value{font-size:28px; font-weight:800; margin-top:6px}
    .kpi .spark{font-size:12px; color:var(--success)}
    @media (max-width: 980px){ .kpi{grid-column: span 6} }
    @media (max-width: 680px){ .kpi{grid-column: span 12} }

    .stats{
      grid-column: span 6;
      display:grid; grid-template-columns:1fr 1fr; gap:12px;
    }
    .stat{
      background:rgba(255,255,255,.03); border:1px solid var(--border); border-radius:12px; padding:14px;
    }
    .stat .label{color:var(--muted); font-size:12px}
    .stat .num{font-size:22px; font-weight:800; margin-top:4px}

    .chart-card{ grid-column: span 6; }
    .chart-wrap{height:260px}

    .quick{
      grid-column: span 12;
      display:flex; gap:12px; flex-wrap:wrap;
    }
    .quick .btn{padding:12px 16px}






/* Dropdown container */
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #333;
  min-width: 160px;
  z-index: 1;
  border-radius: 8px;
  overflow: hidden;
}

.dropdown-content a {
  color: white;
  padding: 10px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {
  background-color: #444;
}

/* Show dropdown on hover */
.dropdown:hover .dropdown-content {
  display: block;
}





    

    .nav-item {
  text-decoration: none;   
  color: inherit;         
  display: flex;         
  align-items: center;
  gap: 8px;               
}

.nav-item.logout {
  color: red; 
}

  </style>
</head>
<body>
  <div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="brand">
        <div class="avatar">FF</div>
        <div class="name">Finess <span>Fitness</span></div>
      </div>

      <nav>
        <button class="nav-item active"><i class='bx bx-home'></i><span class="label">Home</span></button>
        <button class="nav-item"><i class='bx bx-dumbbell'></i><span class="label">Your Workout Plan</span></button>
        
     <!-- Example: user_sidebar.php -->
<nav class="sidebar">
  <button class="nav-item" onclick="window.location.href='nutrition_user.php'">
    <i class='bx bx-bowl-hot'></i>
    <span class="label">Nutrition</span>
  </button>
</nav>


        <div class="dropdown" id="dd-gym">
          <button type="button">
            <span class="left"><i class='bx bx-building-house'></i><span class="label">Gym Types</span></span>
            <i class='bx bx-chevron-down chev'></i>
          </button>
          <div class="menu">
            <a href="#">Weight Gain</a>
            <a href="#">Sauna</a>
            <a href="#">Strength Training</a>
            <a href="#">Fat Loss</a>
            <a href="#">Cardio</a>
            <a href="#">Crossfit Gyms</a>
          </div>
        </div>

        <div class="dropdown" id="dd-res">
          <button type="button">
            <span class="left"><i class='bx bx-book-open'></i><span class="label">Resources</span></span>
            <i class='bx bx-chevron-down chev'></i>

          </button>
          <div class="menu">
            <a href="#" >Our Customers</a>
             <a href ="#">Gym software guide</a>
             <a href ="#">Trust center</a>
             <a href ="#">Terms of service</a>
             <a href ="#">Privacy policy</a>
             <a href ="#">Documentation</a>


          <div class="menu">
            <a href="#">Pricing</a>
            <a class="btn" id="bookSessionBtn"><i class='bx bx-calendar'></i>Book Session</a>
          </div>
        </div>

        <div class="spacer"></div>

<!-- Settings -->
<a href="#" class="nav-item">
  <i class='bx bx-cog'></i>
  <span class="label">Settings</span>
</a>

<!-- Logout -->
<a href="logout.php" class="nav-item logout">
  <i class='bx bx-log-out'></i>
  <span class="label">Logout</span>
</a>
</nav>
      <div class="profile">
        <div class="pic"></div>
        <div class="meta">
        <div class="name"><?php echo htmlspecialchars($userName); ?></div>

          <div class="role">Member</div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <main class="main">
      <!-- Mobile top bar -->
      <div class="mobile-topbar">
        <button class="hamburger" id="openSidebar"><i class='bx bx-menu'></i></button>
        <div style="font-weight:800">Dashboard</div>
        <div style="width:42px"></div>
      </div>

      <!-- Welcome -->
      <section class="welcome">
  <div>
    <div class="title">Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</div>
    <div class="subtitle">Here’s your snapshot for this week.</div>
  </div>
  <div class="actions">
  <button class="btn primary" id="startWorkoutBtn"><i class='bx bx-play-circle'></i>Start Workout</button>
  <button class="btn" id="logProgressBtn"><i class='bx bx-edit'></i>Log Progress</button>
 
  <a class="btn" id="bookSessionBtn1"><i class='bx bx-calendar'></i>Book Session</a>



</div>

</section>

<!-- Include the modals here -->
<?php include('modals.php'); ?> <!-- This file contains logProgressModal & bookSessionModal -->

<script>
  // Open modals on button click
  document.getElementById('logProgressBtn').addEventListener('click', () => {
    document.getElementById('logProgressModal').style.display = 'block';
  });

  document.getElementById('bookSessionBtn').addEventListener('click', () => {
    document.getElementById('bookSessionModal').style.display = 'block';
  });


  document.getElementById('bookSessionBtn1').addEventListener('click', () => {
  document.getElementById('bookSessionModal').style.display = 'block';
});

 
  function closeModal(id){
    document.getElementById(id).style.display = 'none';
  }
</script>


      <!-- KPIs -->
      <section class="grid">
        <div class="card kpi">
          <div>
            <div class="meta">Workouts To Complete</div>
            <div class="value">12</div>
            <div class="spark">+3 this week</div>
          </div>
          <i class='bx bx-dumbbell'></i>
        </div>

        <div class="card kpi">
          <div>
            <div class="meta">Calories Burned</div>
            <div class="value">4,850</div>
            <div class="spark">+9% vs last week</div>
          </div>
          <i class='bx bx-fire'></i>
        </div>

        <div class="card kpi">
  <div>
    <div class="meta">Hours Trained</div>
    <div class="value" id="hoursTrained">
      <?php echo $_SESSION['total_time'] ?? '0h 0m'; ?>
    </div>
    <div class="spark">Target: 8h</div>

    <!-- Training Timer UI -->
    <div style="margin-top:10px;">
      <div id="sessionTimer">0h 0m 0s</div>
      <button type="button" id="startBtn" onclick="startTraining()">Start Training</button>
      <button type="button" id="stopBtn" onclick="stopTraining()" disabled>Stop Training</button>
    </div>
  </div>
  <i class='bx bx-time-five'></i>
</div>







<div class="card stats">
  <div class="stat">
    <div class="label">Steps To Take</div>
    <div class="num">8,932</div>
  </div>
  <div class="stat">
    <div class="label">Active Minutes Today</div>
    <div class="num" id="activeMinutes">0 Today</div>
  </div>
  <div class="stat">
    <div class="label">Take Water</div>
    <div class="num">2.7Ltrs a Day</div>
  </div>
  <div class="stat">
    <div class="label">Enough Sleep</div>
    <div class="num">7h 12m</div>
  </div>
</div>


        <!-- Chart -->
        <div class="card chart-card">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px">
            <div style="font-weight:800">Weekly Performance</div>
            <div style="color:var(--muted); font-size:12px">Workouts • Calories • Minutes</div>
          </div>
          <div class="chart-wrap">
            <canvas id="weeklyChart"></canvas>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick">
          <a class="btn primary"><i class='bx bx-run'></i> Quick Start</a>
          <a class="btn"><i class='bx bx-plus-circle'></i> Add Workout</a>
          <a class="btn"><i class='bx bx-bowl-hot'></i> Log Meal</a>
          <a class="btn"><i class='bx bx-heart'></i> Add Goal</a>
          <a class="btn"><i class='bx bx-download'></i> Export Report</a>
        </div>
      </section>
    </main>
  </div>
  <script>
let timerInterval;
let seconds = 0;

function startTraining() {
  document.getElementById("startBtn").disabled = true;
  document.getElementById("stopBtn").disabled = false;
  seconds = 0;

  timerInterval = setInterval(() => {
    seconds++;
    let hrs = Math.floor(seconds / 3600);
    let mins = Math.floor((seconds % 3600) / 60);
    let secs = seconds % 60;
    document.getElementById("sessionTimer").textContent = 
      `${hrs}h ${mins}m ${secs}s`;
  }, 1000);
}
function stopTraining() {
  clearInterval(timerInterval); 
  document.getElementById("startBtn").disabled = false;
  document.getElementById("stopBtn").disabled = true;

  fetch("log_training.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "seconds=" + seconds
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById("hoursTrained").textContent = data.total_time; 
    document.getElementById("activeMinutes").textContent = data.active_minutes; 
  });
}

</script>






















</script>



  <script>
    // Sidebar dropdown toggles
    const ddGym = document.getElementById('dd-gym');
    const ddRes = document.getElementById('dd-res');
    ddGym.querySelector('button').addEventListener('click', () => ddGym.classList.toggle('open'));
    ddRes.querySelector('button').addEventListener('click', () => ddRes.classList.toggle('open'));

    // Mobile sidebar
    const sidebar = document.getElementById('sidebar');
    const openSidebarBtn = document.getElementById('openSidebar');
    if(openSidebarBtn){
      openSidebarBtn.addEventListener('click', ()=> sidebar.classList.toggle('open'));
      // close when clicking outside on mobile
      document.addEventListener('click', (e)=>{
        if(window.innerWidth <= 680){
          const within = sidebar.contains(e.target) || openSidebarBtn.contains(e.target);
          if(!within) sidebar.classList.remove('open');
        }
      });
    }

    // Chart.js demo data
    const ctx = document.getElementById('weeklyChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
          datasets: [
            {
              label: 'Workouts',
              data: [1, 0, 2, 1, 1, 2, 1],
              tension:.35
            },
            {
              label: 'Calories (k)',
              data: [0.6, 0.5, 0.9, 0.7, 0.8, 1.0, 0.7],
              tension:.35
            },
            {
              label: 'Active Min',
              data: [30, 22, 48, 36, 40, 55, 34],
              tension:.35
            }
          ]
        },
        options: {
          responsive:true,
          maintainAspectRatio:false,
          plugins:{
            legend:{labels:{color:'#eaf2ff'}},
          },
          scales:{
            x:{ticks:{color:'#8da3b8'}, grid:{color:'rgba(255,255,255,.06)'}},
            y:{ticks:{color:'#8da3b8'}, grid:{color:'rgba(255,255,255,.06)'}}
          }
        }
      });
    }
  </script>
</body>
</html>
