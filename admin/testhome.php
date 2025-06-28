<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
<hr class="bg-light">
<div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-light elevation-1"><i class="fas fa-calendar-day"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Today's Offences</span>
                <span class="info-box-number text-right">
                  <?php 
                    $offense = $conn->query("SELECT * FROM `offense_list` where date(date_created) = '".date('Y-m-d')."' ")->num_rows;
                    echo number_format($offense);
                  ?>
                  <?php ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-id-card"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Driver's Listed</span>
                <span class="info-box-number text-right">
                  <?php 
                    $drivers = $conn->query("SELECT id FROM `drivers_list` ")->num_rows;
                    echo number_format($drivers);
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-lightblue elevation-1"><i class="fas fa-traffic-light"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Traffic Offenses</span>
                <span class="info-box-number text-right">
                <?php 
                    $to = $conn->query("SELECT id FROM `offenses` where status = 1 ")->num_rows;
                    echo number_format($to);
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-lightblue elevation-1"><i class="fas fa-traffic-light"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Live Camera Feed</span>
                <span class="info-box-number text-right">
                <?php 
                    
                    echo("1");
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
        </div>



        <!-- alert and sms code -->



        <h1>Welcome to <?php echo $_settings->info('name') ?></h1>
    <hr class="bg-light">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-light elevation-1"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Offences</span>
                    <span class="info-box-number text-right">
                        <?php 
                            $offense = $conn->query("SELECT * FROM `offense_list` WHERE date(date_created) = '".date('Y-m-d')."' ")->num_rows;
                            echo number_format($offense);
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-id-card"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Drivers Listed</span>
                    <span class="info-box-number text-right">
                        <?php 
                            $drivers = $conn->query("SELECT id FROM `drivers_list` ")->num_rows;
                            echo number_format($drivers);
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-lightblue elevation-1"><i class="fas fa-traffic-light"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Traffic Offenses</span>
                    <span class="info-box-number text-right">
                        <?php 
                            $to = $conn->query("SELECT id FROM `offenses` WHERE status = 1 ")->num_rows;
                            echo number_format($to);
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Firebase Vehicle Breakdown Notification -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getDatabase, ref, onChildAdded } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

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
        const breakdownRef = ref(db, "traffic_data/vehicle_Breakdown");

        function showPopup(x, y) {
            const notification = document.createElement("div");
            notification.innerHTML = `<b>🚨 Accident Alert! 🚨</b><br>Coordinates: (${x}, ${y})`;
            notification.style.position = "fixed";
            notification.style.bottom = "20px";
            notification.style.right = "20px";
            notification.style.background = "#ff4d4d";
            notification.style.color = "white";
            notification.style.padding = "15px";
            notification.style.borderRadius = "8px";
            notification.style.boxShadow = "0px 5px 10px rgba(0, 0, 0, 0.3)";
            notification.style.fontSize = "14px";
            document.body.appendChild(notification);

            setTimeout(() => {
                document.body.removeChild(notification);
            }, 5000);
        }

        onChildAdded(breakdownRef, (snapshot) => {
            const data = snapshot.val();
            if (data && data.x && data.y) {
                showPopup(data.x, data.y);
            }
        });

        fetch('send_sms.php')
    .then(response => response.json())
    .then(data => console.log("SMS Status:", data))
    .catch(error => console.error("Error sending SMS:", error));
    </script>




<!-- without auto refresh code -->

<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
    <hr class="bg-light">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-light elevation-1"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Offences</span>
                    <span class="info-box-number text-right">
                        <?php 
                            $offense = $conn->query("SELECT * FROM `offense_list` WHERE date(date_created) = '".date('Y-m-d')."' ")->num_rows;
                            echo number_format($offense);
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-solid fa-truck"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Total Vehicle Counts</span>
            <span class="info-box-number text-right">
                <?php 
                    $firebase_url = "https://traffic-monitoring-f490d-default-rtdb.firebaseio.com/traffic_data/vehicle_Data.json";
                    $response = file_get_contents($firebase_url);
                    
                    if ($response !== false) {
                        $data = json_decode($response, true);
                        
                        // Ensure it's an array and remove null values
                        if (is_array($data)) {
                            $filtered_data = array_filter($data, function ($entry) {
                                return is_array($entry);  // Count only valid breakdown objects
                            });
                            $breakdown_count = count($filtered_data);
                        } else {
                            $breakdown_count = 0;
                        }
                    } else {
                        $breakdown_count = 0;
                    }

                    echo number_format($breakdown_count);
                ?>
            </span>
        </div>
    </div>
</div>

        
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-car-crash"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Accidents Listed</span>
                    <span class="info-box-number text-right">
                    <?php 
                    $firebase_url = "https://traffic-monitoring-f490d-default-rtdb.firebaseio.com/traffic_data/vehicle_Breakdown.json";
                    $response = file_get_contents($firebase_url);
                    
                    if ($response !== false) {
                        $data = json_decode($response, true);
                        
                        // Ensure it's an array and remove null values
                        if (is_array($data)) {
                            $filtered_data = array_filter($data, function ($entry) {
                                return is_array($entry);  // Count only valid breakdown objects
                            });
                            $breakdown_count = count($filtered_data);
                        } else {
                            $breakdown_count = 0;
                        }
                    } else {
                        $breakdown_count = 0;
                    }

                    echo number_format($breakdown_count);
                ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-lightblue elevation-1"><i class="fas fa-traffic-light"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Traffic Offenses</span>
                    <span class="info-box-number text-right">
                        <?php 
                            $to = $conn->query("SELECT id FROM `offenses` WHERE status = 1 ")->num_rows;
                            echo number_format($to);
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
</div>

      


    <!-- Firebase Vehicle Breakdown Notification -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getDatabase, ref, onChildAdded } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

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
        const breakdownRef = ref(db, "traffic_data/vehicle_Breakdown");

        function showPopup(x, y) {
            const notification = document.createElement("div");
            notification.innerHTML = `<b>🚨 Accident Alert! 🚨</b><br>Coordinates: (${x}, ${y})`;
            notification.style.position = "fixed";
            notification.style.bottom = "20px";
            notification.style.right = "20px";
            notification.style.background = "#ff4d4d";
            notification.style.color = "white";
            notification.style.padding = "15px";
            notification.style.borderRadius = "8px";
            notification.style.boxShadow = "0px 5px 10px rgba(0, 0, 0, 0.3)";
            notification.style.fontSize = "14px";
            document.body.appendChild(notification);

            setTimeout(() => {
                document.body.removeChild(notification);
            }, 5000);
        }

        onChildAdded(breakdownRef, (snapshot) => {
            const data = snapshot.val();
            if (data && data.x && data.y) {
                showPopup(data.x, data.y);
            }
        });

        fetch('send_sms.php')
    .then(response => response.json())
    .then(data => console.log("SMS Status:", data))
    .catch(error => console.error("Error sending SMS:", error));
    </script>