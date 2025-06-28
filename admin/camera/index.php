<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Smart Traffic Video Panel</title>

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
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .card {
      background: white;
      border-radius: 14px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 95%;
      max-width: 800px;
      padding: 25px;
      margin-bottom: 30px;
      transition: 0.3s ease;
    }

    .card h2 {
      color: #1846a3;
      margin-bottom: 10px;
    }

    input {
      width: 100%;
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

    iframe {
      width: 100%;
      height: 450px;
      border-radius: 10px;
      border: none;
    }

    .footer {
      margin-top: 30px;
      color: #888;
      font-size: 14px;
    }

    @media (max-width: 600px) {
      iframe {
        height: 250px;
      }
    }
  </style>
</head>
<body>

  <div class="card">
    <h2>🚦 Add Video URL</h2>
    <input type="text" id="videoUrl" placeholder="Paste full video URL and press Enter">
  </div>

  <div class="card">
    <h2>📺 Video Preview</h2>
    <iframe id="videoFrame" allowfullscreen></iframe>
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

    const input = document.getElementById("videoUrl");
    const videoFrame = document.getElementById("videoFrame");

    input.addEventListener("keypress", function(e) {
      if (e.key === "Enter" && input.value.trim() !== "") {
        const url = input.value.trim();
        if (isYouTubeURL(url)) {
          db.ref("video_path/video_url").set(url);
          input.value = "";
          alert("✅ Full URL saved!");
        } else {
          alert("❌ Please enter a valid  video URL!");
        }
      }
    });

    function isYouTubeURL(url) {
      return /(youtube\.com\/watch\?v=|youtu\.be\/)/.test(url);
    }

    function extractVideoID(url) {
      const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
      return match ? match[1] : null;
    }

    let videoUrls = [];
    let index = 0;

    function loopVideos() {
      if (videoUrls.length === 0) return;
      const url = videoUrls[index];
      const videoID = extractVideoID(url);
      if (videoID) {
        videoFrame.src = `https://www.youtube.com/embed/${videoID}?autoplay=1&mute=1&controls=1&rel=0&modestbranding=1`;
      }
      index = (index + 1) % videoUrls.length;
      setTimeout(loopVideos, 60000); // 60 seconds
    }

    db.ref("video_path/video_url").on("value", (snapshot) => {
  const url = snapshot.val();
  videoUrls = [];
  if (url) {
    videoUrls.push(url);
    index = 0;
    loopVideos();
  }
});
  </script>

</body>
</html>