<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crash Vehicle Map</title>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-database-compat.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            padding: 20px;
            width: 100%;
            height: 85vh;
        }

        #map {
            width: 50%;
            height: 100%;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            background: white;
        }

        .table-container {
            width: 50%;
            height: 100%;
            border-radius: 15px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 15px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            color: black;
            font-size: 14px;
        }

        th {
            background: #3a7bd5;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            cursor: pointer;
            text-align: center;
        }

        tr:hover {
            background: #dfe9f3;
        }

        .table-container, #map {
            border-radius: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div id="map"></div>

        <div class="table-container">
            <h3 style="text-align: center; color: #3a7bd5;">Crash Vehicle Data</h3>
            <table>
                <thead>
                    <tr>
                        <th>Vehicle ID</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody id="data-table">
                    <!-- Data from Firebase will appear here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

            firebase.initializeApp(firebaseConfig);
            const database = firebase.database();

            const map = L.map('map').setView([20.5937, 78.9629], 5); // Default India view

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            function loadCrashVehicleData() {
                const tableBody = document.getElementById("data-table");

                database.ref('traffic_data/crash_vehicle').on('value', (snapshot) => {
                    if (!snapshot.exists()) {
                        console.warn("⚠️ No crash data found!");
                        return;
                    }

                    tableBody.innerHTML = ""; // Clear old data

                    snapshot.forEach((childSnapshot) => {
                        const data = childSnapshot.val();
                        if (!data.latitude || !data.longitude) {
                            console.warn("⚠️ Incomplete data at:", childSnapshot.key);
                            return;
                        }

                        const latitude = parseFloat(data.latitude);
                        const longitude = parseFloat(data.longitude);
                        const vehicleId = data.vehicle_id || childSnapshot.key;
                        const timestamp = data.timestamp || "No Timestamp";

                        const row = document.createElement("tr");
                        row.setAttribute("data-lat", latitude);
                        row.setAttribute("data-lng", longitude);
                        row.innerHTML = `
                            <td>${vehicleId}</td>
                            <td>${latitude}</td>
                            <td>${longitude}</td>
                            <td>${timestamp}</td>
                        `;
                        tableBody.appendChild(row);

                        const marker = L.marker([latitude, longitude]).addTo(map)
                            .bindPopup(`<b>Vehicle ID:</b> ${vehicleId}<br><b>Time:</b> ${timestamp}`);

                        row.addEventListener("click", () => {
                            map.setView([latitude, longitude], 15);
                            marker.openPopup();
                        });
                    });
                }, (error) => {
                    console.error("❌ Firebase read failed:", error);
                });
            }

            loadCrashVehicleData();
        });
    </script>

</body>
</html>
