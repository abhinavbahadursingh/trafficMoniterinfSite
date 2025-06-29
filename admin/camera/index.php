<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Multi-Camera Traffic Video Panel</title>

  <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: #eef4ff;
      padding: 20px;
      min-height: 100vh;
    }

    .container {
      max-width: 100%;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .header h1 {
      color: #1846a3;
      font-size: 2.5rem;
      margin-bottom: 10px;
    }

    .header p {
      color: #666;
      font-size: 1.1rem;
    }

    .input-section {
      background: white;
      border-radius: 14px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      padding: 25px;
      margin-bottom: 30px;
    }

    .input-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .input-group {
      display: flex;
      flex-direction: column;
    }

    .input-group label {
      color: #1846a3;
      font-weight: 600;
      margin-bottom: 8px;
      font-size: 1.1rem;
    }

    input {
      padding: 12px 14px;
      font-size: 16px;
      border: 2px solid #c8d9ff;
      border-radius: 8px;
      transition: 0.3s;
    }

    input:focus {
      border-color: #1846a3;
      outline: none;
      background: #f0f6ff;
    }

    .video-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 120px;
      margin-top: 15px;
    }

    .video-card {
      background: white;
      border-radius: 14px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      padding:15px;
      transition: 0.3s ease;
    }

    .video-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .video-card h3 {
      color: #1846a3;
      margin-bottom: 15px;
      font-size: 1.3rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    iframe {
      width: 100%;
      height: 350px;
      border-radius: 10px;
      border: 2px solid #f0f6ff;
      transition: 0.3s;
    }

    iframe:hover {
      border-color: #1846a3;
    }

    .status-indicator {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #ddd;
      margin-left: 10px;
      transition: 0.3s;
    }

    .status-indicator.active {
      background: #22c55e;
      box-shadow: 0 0 10px rgba(34, 197, 94, 0.3);
    }

    .footer {
      margin-top: 40px;
      text-align: center;
      color: #888;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .video-grid {
        grid-template-columns: 1fr;
      }
      
      .input-grid {
        grid-template-columns: 1fr;
      }
      
      iframe {
        height: 200px;
      }
      
      .header h1 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="header">
      <h1>🚦 Traffic Monitoring System</h1>
      <p>Multi-Camera Video Feed Dashboard</p>
    </div>

    <div class="input-section">
      <div class="input-grid">
        <div class="input-group">
          <label for="videoUrl1">📹 Camera 1 URL</label>
          <input type="text" id="videoUrl1" placeholder="Paste Camera 1 video URL and press Enter">
        </div>
        <div class="input-group">
          <label for="videoUrl2">📹 Camera 2 URL</label>
          <input type="text" id="videoUrl2" placeholder="Paste Camera 2 video URL and press Enter">
        </div>
        <div class="input-group">
          <label for="videoUrl3">📹 Camera 3 URL</label>
          <input type="text" id="videoUrl3" placeholder="Paste Camera 3 video URL and press Enter">
        </div>
        <div class="input-group">
          <label for="videoUrl4">📹 Camera 4 URL</label>
          <input type="text" id="videoUrl4" placeholder="Paste Camera 4 video URL and press Enter">
        </div>
      </div>
    </div>

    <div class="video-grid">
      <div class="video-card">
        <h3>📹 Camera 1 - Main Junction<span class="status-indicator" id="status1"></span></h3>
        <iframe id="videoFrame1" allowfullscreen></iframe>
      </div>
      <div class="video-card">
        <h3>📹 Camera 2 - North Entrance<span class="status-indicator" id="status2"></span></h3>
        <iframe id="videoFrame2" allowfullscreen></iframe>
      </div>
      <div class="video-card">
        <h3>📹 Camera 3 - South Exit<span class="status-indicator" id="status3"></span></h3>
        <iframe id="videoFrame3" allowfullscreen></iframe>
      </div>
      <div class="video-card">
        <h3>📹 Camera 4 - West Corridor<span class="status-indicator" id="status4"></span></h3>
        <iframe id="videoFrame4" allowfullscreen></iframe>
      </div>
    </div>

    <div class="footer">
      <p>🚦 Smart Traffic Management System | Real-time Video Monitoring</p>
    </div>
  </div>

  <script>
    const firebaseConfig = {
      apiKey: "AIzaSyDd7d-MHWyA0Kl0JmIzvtZ6CiZM1QjuyDM",
      authDomain: "traffic-monitoring-f490d.firebaseapp.com",
      databaseURL: "https://traffic-monitoring-f490d-default-rtdb.firebaseio.com",
      projectId: "traffic-monitoring-f490d",
      storageBucket: "traffic-monitoring-f490d.appspot.com",
      messagingSenderId: "572081795374",
      appId: "1:572081795374:web:da24f2255697d84c454abf"
    };

    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    // Get all input and iframe elements
    const inputs = [
      document.getElementById("videoUrl1"),
      document.getElementById("videoUrl2"),
      document.getElementById("videoUrl3"),
      document.getElementById("videoUrl4")
    ];

    const videoFrames = [
      document.getElementById("videoFrame1"),
      document.getElementById("videoFrame2"),
      document.getElementById("videoFrame3"),
      document.getElementById("videoFrame4")
    ];

    const statusIndicators = [
      document.getElementById("status1"),
      document.getElementById("status2"),
      document.getElementById("status3"),
      document.getElementById("status4")
    ];

    // Add event listeners for each input
    inputs.forEach((input, index) => {
      input.addEventListener("keypress", function(e) {
        if (e.key === "Enter" && input.value.trim() !== "") {
          const url = input.value.trim();
          if (isYouTubeURL(url)) {
            db.ref(`video_path/camera_${index + 1}`).set(url);
            // Keep the URL in the input field - don't clear it
            alert(`✅ Camera ${index + 1} URL saved!`);
          } else {
            alert("❌ Please enter a valid YouTube video URL!");
          }
        }
      });
    });

    function isYouTubeURL(url) {
      return /(youtube\.com\/watch\?v=|youtu\.be\/)/.test(url);
    }

    function extractVideoID(url) {
      const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
      return match ? match[1] : null;
    }

    // Video loop management for each camera
    const videoLoops = [
      { urls: [], index: 0, interval: null },
      { urls: [], index: 0, interval: null },
      { urls: [], index: 0, interval: null },
      { urls: [], index: 0, interval: null }
    ];

    function loopVideos(cameraIndex) {
      const loop = videoLoops[cameraIndex];
      if (loop.urls.length === 0) return;
      
      const url = loop.urls[loop.index];
      const videoID = extractVideoID(url);
      if (videoID) {
        videoFrames[cameraIndex].src = `https://www.youtube.com/embed/${videoID}?autoplay=1&mute=1&controls=1&rel=0&modestbranding=1`;
        statusIndicators[cameraIndex].classList.add('active');
      }
      
      loop.index = (loop.index + 1) % loop.urls.length;
      loop.interval = setTimeout(() => loopVideos(cameraIndex), 60000); // 60 seconds
    }

    function stopVideoLoop(cameraIndex) {
      const loop = videoLoops[cameraIndex];
      if (loop.interval) {
        clearTimeout(loop.interval);
        loop.interval = null;
      }
      statusIndicators[cameraIndex].classList.remove('active');
    }

    // Listen for changes in Firebase for each camera
    for (let i = 0; i < 4; i++) {
      (function(cameraIndex) {
        db.ref(`video_path/camera_${cameraIndex + 1}`).on("value", (snapshot) => {
          const url = snapshot.val();
          const loop = videoLoops[cameraIndex];
          
          // Stop existing loop
          stopVideoLoop(cameraIndex);
          
          // Reset and start new loop
          loop.urls = [];
          if (url) {
            loop.urls.push(url);
            loop.index = 0;
            loopVideos(cameraIndex);
          } else {
            // Clear the iframe if no URL
            videoFrames[cameraIndex].src = "";
          }
        });
      })(i);
    }

    // Initialize with empty frames
    videoFrames.forEach(frame => {
      frame.src = "";
    });
  </script>

</body>
</html>