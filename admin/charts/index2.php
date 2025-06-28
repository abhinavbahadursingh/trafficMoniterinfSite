<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Traffic Monitoring Dashboard</title>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 20px;
      width: 95%;
      max-width: 1400px;
      padding: 30px;
    }

    .graph-container {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    h2 {
      margin-bottom: 10px;
      color: #333;
      font-size: 22px;
    }

    canvas {
      width: 100% !important;
      height: 300px !important;
    }
  </style>
</head>
<body>

  <div class="dashboard">
    <div class="graph-container">
      <h2 id="avg-title">Average Speed Graph</h2>
      <canvas id="avgSpeedChart"></canvas>
    </div>
    <div class="graph-container">
      <h2 id="realtime-title">Real-Time Speed</h2>
      <canvas id="realTimeSpeedChart"></canvas>
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
      appId: "1:572081795374:web:da24f2255697d84c454abf",
      measurementId: "G-W5MMS4B9L2"
    };

    const app = initializeApp(firebaseConfig);
    const db = getDatabase(app);
    const realTimeRef = ref(db, "traffic_data/speed_Data/real_Time_Speed");

    let avgChart, realTimeChart;

    function createLineChart(ctx, label, color) {
      return new Chart(ctx, {
        type: 'line',
        data: {
          labels: [],
          datasets: [{
            label,
            data: [],
            borderColor: color,
            backgroundColor: color + "30",
            borderWidth: 2,
            fill: true,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          scales: {
            x: { title: { display: true, text: "Time" } },
            y: { title: { display: true, text: label }, min: 0 }
          }
        }
      });
    }

    function initCharts() {
      avgChart = createLineChart(document.getElementById("avgSpeedChart").getContext("2d"), "Average Speed (km/h)", "#2575fc");
      realTimeChart = createLineChart(document.getElementById("realTimeSpeedChart").getContext("2d"), "Real-Time Speed (km/h)", "#ff9800");
    }

    function updateCharts() {
      onValue(realTimeRef, (snapshot) => {
        const data = snapshot.val();
        if (!data) return;

        const realTimeLabels = [], realTimeSpeeds = [], allSpeeds = [];

        Object.entries(data).forEach(([key, val]) => {
          if (val.time1 && typeof val.time1.speed === "number") {
            realTimeLabels.push(key);
            realTimeSpeeds.push(val.time1.speed);
            allSpeeds.push(val.time1.speed);
          }
        });

        // Update real-time speed chart
        realTimeChart.data.labels = realTimeLabels;
        realTimeChart.data.datasets[0].data = realTimeSpeeds;
        realTimeChart.update();

        // Calculate average speed
        if (allSpeeds.length > 0) {
          const sum = allSpeeds.reduce((a, b) => a + b, 0);
          const avg = parseFloat((sum / allSpeeds.length).toFixed(2));
          const time = new Date().toLocaleTimeString();

          avgChart.data.labels.push(time);
          avgChart.data.datasets[0].data.push(avg);

          if (avgChart.data.labels.length > 20) {
            avgChart.data.labels.shift();
            avgChart.data.datasets[0].data.shift();
          }

          avgChart.update();
        }
      });
    }

    initCharts();
    updateCharts();
  </script>
</body>
</html>