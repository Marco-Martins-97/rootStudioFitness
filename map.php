
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        #contacts {
            padding: 2rem;
            background: #f0f0f0;
        }

        .contacts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .map {
            flex: 1 1 50%;
            min-height: 500px;
        }

        .contact-info {
            flex: 1 1 50%;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .contact-info h2 {
            margin-top: 0;
        }

        @media (max-width: 768px) {
            .map, .contact-info {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<section id="contacts">
    <div class="contacts-container">
        <div id="map" class="map"></div>
        <div class="contact-info">
            <h2>Contact Us</h2>
            <p><strong>Address:</strong> 123 Main Street, City, Country</p>
            <p><strong>Phone:</strong> +1 (555) 123-4567</p>
            <p><strong>Email:</strong> info@example.com</p>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const officeLoc = [41.547187, -8.433896]; // Office coordinates
    
    // Initialize map
    const map = L.map('map').setView(officeLoc, 13);

    // Tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Office marker
    const officeIcon = L.icon({
        iconUrl: 'https://leafletjs.com/examples/custom-icons/leaf-green.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41]
    });
    L.marker(officeLoc, { icon: officeIcon }).addTo(map).bindPopup('My Office');

    // Check geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLoc = [position.coords.latitude, position.coords.longitude];

                // User marker
                const userIcon = L.icon({
                    iconUrl: 'https://leafletjs.com/examples/custom-icons/leaf-red.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                });
                L.marker(userLoc, { icon: userIcon }).addTo(map).bindPopup("You are here");

                // Fit map to both markers
                map.fitBounds([officeLoc, userLoc]);

                // Fetch route from OSRM
                const routingUrl = `https://router.project-osrm.org/route/v1/driving/${userLoc[1]},${userLoc[0]};${officeLoc[1]},${officeLoc[0]}?overview=full&geometries=geojson`;

                fetch(routingUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.routes && data.routes.length > 0) {
                            const routeCoords = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
                            L.polyline(routeCoords, { color: 'blue', weight: 4 }).addTo(map);
                        } else {
                            alert("Route not found.");
                        }
                    })
                    .catch(err => {
                        console.error("Routing error:", err);
                        alert("Failed to load route.");
                    });
            },
            (error) => {
                console.error("Geolocation error:", error.message);
                alert("Unable to get your location.");
            }
        );
    } else {
        alert("Geolocation is not supported by your browser.");
    }
});
</script>

</body>
</html>