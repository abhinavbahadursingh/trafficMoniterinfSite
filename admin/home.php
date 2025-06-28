<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
<hr class="bg-light">
<div class="row">
    <!-- Today's Offences -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-light elevation-1"><i class="fas fa-calendar-day"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Today's Offences</span>
                <span class="info-box-number text-right" id="todays_offenses_mysql">
                     <?php 
                        $offense = $conn->query("SELECT * FROM `offense_list` WHERE date(date_created) = '".date('Y-m-d')."' ")->num_rows;
                        echo number_format($offense);
                    ?> 
                </span>
                <!-- <span class="info-box-number text-right text-muted" id="todays_offenses_firebase">(Fetching...)</span> -->
            </div>
        </div>
    </div>

    <!-- Total Vehicle Counts -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-solid fa-truck"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Vehicle Counts</span>
                <span class="info-box-number text-right" id="vehicle_count">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Total Accidents Listed -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-car-crash"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Accidents Listed</span>
                <span class="info-box-number text-right" id="breakdown_count">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Total Traffic Offenses -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-lightblue elevation-1"><i class="fas fa-traffic-light"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Traffic Offenses</span>
                <span class="info-box-number text-right" id="total_offenses_mysql">
                    <?php 
                        $to = $conn->query("SELECT id FROM `offenses` WHERE status = 1 ")->num_rows;
                        echo number_format($to);
                    ?>
                </span>
                <!-- <span class="info-box-number text-right text-muted" id="total_offenses_firebase">(Fetching...)</span> -->
            </div>
        </div>
    </div>
</div>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
    import { getDatabase, ref, get, onChildAdded } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

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

    function fetchAndUpdateData() {
        // Fetch Vehicle Count
        get(ref(db, "traffic_data/vehicle_Data/total_vehicle")).then(snapshot => {
            document.getElementById("vehicle_count").innerText = snapshot.exists() ? Object.keys(snapshot.val()).length : "0";
        });

        // Fetch Breakdown Count
        get(ref(db, "traffic_data/vehicle_Breakdown")).then(snapshot => {
            document.getElementById("breakdown_count").innerText = snapshot.exists() ? Object.keys(snapshot.val()).length : "0";
        });

        // Fetch Total Offenses from Firebase
        // get(ref(db, "traffic_data/traffic_offenses")).then(snapshot => {
        //     document.getElementById("total_offenses_firebase").innerText = snapshot.exists() ? Object.keys(snapshot.val()).length : "0";
        // });

        // Fetch Today's Offenses from Firebase
        // const today = new Date().toISOString().split("T")[0];
        // get(ref(db, "traffic_data/traffic_offenses")).then(snapshot => {
        //     if (snapshot.exists()) {
        //         const todayOffenses = Object.values(snapshot.val()).filter(offense => offense.date === today).length;
        //         document.getElementById("todays_offenses_firebase").innerText = todayOffenses;
        //     } else {
        //         document.getElementById("todays_offenses_firebase").innerText = "0";
        //     }
        // });
    }

    setInterval(fetchAndUpdateData, 1000); // Auto-refresh every second

    // 🚨 Accident Notification Popup
    function showPopup(latitude, longitude, vehicleId, timestamp) {
    const notification = document.createElement("div");
    notification.innerHTML = `<b>🚨 Crashed Vehicle Alert! 🚨</b><br>
                              Vehicle ID: ${vehicleId} <br>
                              Coordinates: (${latitude}, ${longitude}) <br>
                              Time: ${timestamp}`;
    notification.style.position = "fixed";
    notification.style.bottom = "20px";
    notification.style.right = "20px";
    notification.style.background = "#ff4d4d";
    notification.style.color = "white";
    notification.style.padding = "15px";
    notification.style.borderRadius = "8px";
    notification.style.boxShadow = "0px 5px 10px rgba(0, 0, 0, 0.3)";
    notification.style.fontSize = "14px";
    notification.style.zIndex = "1000";
    document.body.appendChild(notification);

    setTimeout(() => {
        document.body.removeChild(notification);
    }, 5000);
}

onChildAdded(ref(db, "traffic_data/vehicle_Breakdown"), (snapshot) => {
    const data = snapshot.val();
    if (data && data.latitude && data.longitude && data.vehicle_id && data.timestamp) {
        showPopup(data.latitude, data.longitude, data.vehicle_id, data.timestamp);
    }
});

    // 📩 Fetch SMS Status
    function sendSMS() {
        fetch('send_sms.php')
            .then(response => response.json())
            .then(data => console.log("SMS Status:", data))
            .catch(error => console.error("Error sending SMS:", error));
    }

    sendSMS(); // Call once when page loads
</script>