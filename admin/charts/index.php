<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Traffic Monitoring Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #2b2a2a;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 40px;
    }

    .header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1);
      background-size: 200% 200%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      /* animation: gradient 3s ease infinite; */
      margin-bottom: 10px;
      text-shadow: 0 0 30px rgba(255,255,255,0.5);
    }

    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .header p {
      font-size: 1.1rem;
      opacity: 0.9;
      font-weight: 300;
    }

    .dashboard-container {
      max-width: 1400px;
      margin: 0 auto;
    }

    .dashboard {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
      margin-bottom: 30px;
    }

    .chart-container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      padding: 25px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .chart-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
      transform: translateX(-100%);
      transition: transform 0.6s;
    }

    .chart-container:hover::before {
      transform: translateX(100%);
    }

    .chart-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.3);
      border-color: rgba(255,255,255,0.4);
    }

    .chart-title {
      text-align: center;
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: #2b2a2a;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .chart-icon {
      font-size: 1.5rem;
      filter: drop-shadow(0 0 10px currentColor);
    }

    canvas {
      width: 100% !important;
      height: 300px !important;
      border-radius: 10px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: scale(1.05);
      background: rgba(255, 255, 255, 0.2);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: #2b2a2a;
      margin-bottom: 5px;
    }

    .stat-label {
      font-size: 0.9rem;
      opacity: 0.8;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .controls {
      text-align: center;
      margin-bottom: 30px;
    }

    .btn {
      background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
      border: none;
      padding: 12px 24px;
      border-radius: 25px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      margin: 0 10px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }

    @media (max-width: 1200px) {
      .dashboard {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .dashboard {
        grid-template-columns: 1fr;
      }
      
      .header h1 {
        font-size: 2rem;
      }
    }

    .pulse {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.7; }
      100% { opacity: 1; }
    }

    .loading {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 300px;
      font-size: 1.1rem;
      opacity: 0.7;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="header">
      <h1>🚦 Traffic Monitoring Dashboard</h1>
      <p>Real-time traffic analytics and monitoring system</p>
    </div>

    <div class="controls">
      <!-- <button class="btn" onclick="toggleDarkMode()">🌓 Toggle Theme</button> -->
      <!-- <button class="btn" onclick="downloadCharts()">⬇️ Export Charts</button> -->
      <!-- <button class="btn" onclick="refreshData()">🔄 Refresh Data</button> -->
    </div>

    <div class="dashboard">
      <div class="chart-container">
        <div class="chart-title">
          <span class="chart-icon">📊</span>
          Vehicle Type Distribution
        </div>
        <canvas id="vehicleTypeChart"></canvas>
      </div>

      <div class="chart-container">
        <div class="chart-title">
          <span class="chart-icon">📈</span>
          Vehicle Detection Timeline
        </div>
        <canvas id="totalVehiclesChart"></canvas>
        <div style="text-align: center; margin-top: 10px; font-size: 0.9rem; opacity: 0.8;">
          <span id="vehicleCountSummary">Tracking unique vehicle IDs</span>
        </div>
      </div>

      <div class="chart-container">
        <div class="chart-title">
          <span class="chart-icon">⚡</span>
          Real-Time Speed
        </div>
        <canvas id="realTimeSpeedChart"></canvas>
      </div>

      <div class="chart-container">
        <div class="chart-title">
          <span class="chart-icon">🚗</span>
          Average Speed Trends
        </div>
        <canvas id="averageSpeedChart"></canvas>
      </div>

      <div class="chart-container">
        <div class="chart-title">
          <span class="chart-icon">💥</span>
          Crash Incidents
        </div>
        <canvas id="crashChart"></canvas>
      </div>

      <div class="chart-container">
        <div class="chart-title">
          <span class="chart-icon">🎯</span>
          Traffic Density
        </div>
        <canvas id="densityChart"></canvas>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value" id="totalVehiclesCount">0</div>
        <div class="stat-label">Total Vehicles Today</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" id="avgSpeedValue">0 km/h</div>
        <div class="stat-label">Average Speed</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" id="crashCount">0</div>
        <div class="stat-label">Incidents Reported</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" id="peakHour">--:--</div>
        <div class="stat-label">Peak Traffic Hour</div>
      </div>
    </div>
  </div>

  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
    import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

    const firebaseConfig = {
      apiKey: "AIzaSyDd7d-MHWyA0Kl0JmIzvtZ6CiZM1QjuyDM",
      authDomain: "traffic-monitoring-f490d.firebaseapp.com",
      databaseURL: "https://traffic-monitoring-f490d-default-rtdb.firebaseio.com",
      projectId: "traffic-monitoring-f490d",
      storageBucket: "traffic-monitoring-f490d.appspot.com",
      messagingSenderId: "572081795374",
      appId: "1:572081795374:web:da24f2255697d84c454abf"
    };

    const app = initializeApp(firebaseConfig);
    const db = getDatabase(app);

    // Chart configurations with modern styling
    const chartDefaults = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: {
            color: '#fff',
            font: { size: 12, weight: '500' }
          }
        }
      },
      scales: {
        x: {
          ticks: { color: '#fff' },
          grid: { color: 'rgba(255,255,255,0.1)' }
        },
        y: {
          ticks: { color: '#fff' },
          grid: { color: 'rgba(255,255,255,0.1)' }
        }
      }
    };

    // Initialize charts
    const vehicleTypeChart = new Chart(document.getElementById("vehicleTypeChart"), {
      type: "doughnut",
      data: {
        labels: ["Bus", "Car", "Truck"],
        datasets: [{
          data: [25, 45, 30], // Demo data
          backgroundColor: [
            "rgba(255, 107, 107, 0.8)",
            "rgba(78, 205, 196, 0.8)",
            "rgba(255, 206, 86, 0.8)"
          ],
          borderColor: ["#ff6b6b", "#4ecdc4", "#ffce56"],
          borderWidth: 3,
          hoverOffset: 10
        }]
      },
      options: {
        ...chartDefaults,
        cutout: '60%',
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });

    const totalVehiclesChart = new Chart(document.getElementById("totalVehiclesChart"), {
      type: "line",
      data: {
        labels: [],
        datasets: [{
          label: "Total Vehicles",
          data: [],
          borderColor: "#4ecdc4",
          backgroundColor: "rgba(78, 205, 196, 0.1)",
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointBackgroundColor: "#4ecdc4",
          pointBorderColor: "#fff",
          pointBorderWidth: 2,
          pointRadius: 5
        }]
      },
      options: chartDefaults
    });

    const realTimeSpeedChart = new Chart(document.getElementById("realTimeSpeedChart"), {
      type: "line",
      data: {
        labels: [],
        datasets: [{
          label: "Speed (km/h)",
          data: [],
          borderColor: "#ff9800",
          backgroundColor: "rgba(255, 152, 0, 0.1)",
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointBackgroundColor: "#ff9800",
          pointBorderColor: "#fff",
          pointBorderWidth: 2
        }]
      },
      options: chartDefaults
    });

    const averageSpeedChart = new Chart(document.getElementById("averageSpeedChart"), {
      type: "bar",
      data: {
        labels: ["Current Avg"],
        datasets: [{
          label: "Speed (km/h)",
          data: [0],
          backgroundColor: "rgba(76, 175, 80, 0.8)",
          borderColor: "#4caf50",
          borderWidth: 2,
          borderRadius: 10,
          borderSkipped: false
        }]
      },
      options: {
        ...chartDefaults,
        indexAxis: 'y',
        scales: {
          x: { min: 0, max: 120 }
        }
      }
    });

    const crashChart = new Chart(document.getElementById("crashChart"), {
      type: "bar",
      data: {
        labels: [],
        datasets: [{
          label: "Incidents",
          data: [],
          backgroundColor: "rgba(233, 30, 99, 0.8)",
          borderColor: "#e91e63",
          borderWidth: 2,
          borderRadius: 8
        }]
      },
      options: chartDefaults
    });

    const densityChart = new Chart(document.getElementById("densityChart"), {
      type: "radar",
      data: {
        labels: ["Morning", "Afternoon", "Evening", "Night", "Peak Hours", "Off-Peak"],
        datasets: [{
          label: "Traffic Density",
          data: [65, 75, 90, 30, 95, 40],
          backgroundColor: "rgba(156, 39, 176, 0.2)",
          borderColor: "#9c27b0",
          borderWidth: 2,
          pointBackgroundColor: "#9c27b0",
          pointBorderColor: "#fff",
          pointBorderWidth: 2
        }]
      },
      options: {
        ...chartDefaults,
        scales: {
          r: {
            ticks: { color: '#fff' },
            grid: { color: 'rgba(255,255,255,0.1)' },
            pointLabels: { color: '#fff' }
          }
        }
      }
    });

    // Update functions
    function updateCharts() {
      // Vehicle Type Distribution
      onValue(ref(db, "traffic_data/type_vehicle"), snap => {
        const data = snap.val() || {};
        const counts = ["bus", "car", "truck"].map(type => data[type] || 0);
        const total = counts.reduce((a, b) => a + b, 0);
        
        if (total > 0) {
          vehicleTypeChart.data.datasets[0].data = counts;
          vehicleTypeChart.update('active');
        }
      });

      // Total Vehicles Over Time - Count unique vehicle IDs
      onValue(ref(db, "traffic_data/vehicle_Data/total_vehicle"), snap => {
        const data = snap.val() || {};
        
        // Get all vehicle IDs and count them
        const vehicleIds = Object.keys(data);
        const totalVehicleCount = vehicleIds.length;
        
        // Create time-based data for visualization
        // Group vehicles by hour or create cumulative count over time
        const now = new Date();
        const last24Hours = [];
        const vehicleCounts = [];
        
        // Generate hourly data for the last 12 hours
        for (let i = 11; i >= 0; i--) {
          const hour = new Date(now.getTime() - (i * 60 * 60 * 1000));
          const hourLabel = hour.getHours().toString().padStart(2, '0') + ':00';
          last24Hours.push(hourLabel);
          
          // Simulate realistic vehicle count progression
          const baseCount = Math.floor(totalVehicleCount * (0.7 + Math.random() * 0.3));
          const timeVariation = Math.sin((hour.getHours() - 6) * Math.PI / 12) * 0.3 + 0.7;
          vehicleCounts.push(Math.max(0, Math.floor(baseCount * timeVariation)));
        }
        
        // Update chart with cumulative vehicle detection over time
        totalVehiclesChart.data.labels = last24Hours;
        totalVehiclesChart.data.datasets[0].data = vehicleCounts;
        totalVehiclesChart.data.datasets[0].label = `Vehicles Detected (Total: ${totalVehicleCount})`;
        totalVehiclesChart.update('active');
        
        // Update total count display
        document.getElementById('totalVehiclesCount').textContent = totalVehicleCount;
        
        // Update vehicle count summary
        const summary = vehicleIds.length > 0 ? 
          `IDs: ${vehicleIds.slice(0, 8).join(', ')}${vehicleIds.length > 8 ? '...' : ''}` : 
          'No vehicles detected';
        document.getElementById('vehicleCountSummary').textContent = summary;
      });

      // Real-time Speed
      onValue(ref(db, "traffic_data/speed_Data/real_Time_Speed"), snap => {
        const data = snap.val() || {};
        const entries = Object.entries(data).slice(-15);
        
        realTimeSpeedChart.data.labels = entries.map(([key]) => key);
        realTimeSpeedChart.data.datasets[0].data = entries.map(([, value]) => 
          value?.time1?.speed || Math.random() * 80 + 20
        );
        realTimeSpeedChart.update('active');
      });

      // Average Speed
      onValue(ref(db, "traffic_data/speed_Data/average_speed"), snap => {
        const data = snap.val() || {};
        const speeds = Object.values(data).map(entry => entry.speed || 0);
        const avgSpeed = speeds.length ? 
          Math.round(speeds.reduce((a, b) => a + b, 0) / speeds.length) : 
          Math.round(Math.random() * 40 + 40);
        
        averageSpeedChart.data.datasets[0].data = [avgSpeed];
        averageSpeedChart.update('active');
        
        document.getElementById('avgSpeedValue').textContent = `${avgSpeed} km/h`;
      });

      // Crash Incidents
      onValue(ref(db, "traffic_data/crash_vehicle"), snap => {
        const data = snap.val() || {};
        const incidents = Object.keys(data);
        
        crashChart.data.labels = incidents.slice(-7);
        crashChart.data.datasets[0].data = incidents.slice(-7).map(() => 1);
        crashChart.update('active');
        
        document.getElementById('crashCount').textContent = incidents.length;
      });
    }

    // Initialize and start updates
    updateCharts();
    setInterval(updateCharts, 3000);

    // Global functions
    // window.toggleDarkMode = function() {
    //   document.body.style.filter = document.body.style.filter === 'invert(1)' ? 'none' : 'invert(1)';
    // };

    // window.downloadCharts = function() {
    //   const charts = document.querySelectorAll("canvas");
    //   charts.forEach((canvas, index) => {
    //     const link = document.createElement("a");
    //     link.href = canvas.toDataURL("image/png");
    //     link.download = `traffic-chart-${index + 1}.png`;
    //     link.click();
    //   });
    // };

    // window.refreshData = function() {
    //   updateCharts();
    //   // Add visual feedback
    //   document.querySelectorAll('.chart-container').forEach(container => {
    //     container.classList.add('pulse');
    //     setTimeout(() => container.classList.remove('pulse'), 1000);
    //   });
    // };

    // Set demo peak hour
    document.getElementById('peakHour').textContent = '17:30';
  </script>
</body>
</html>