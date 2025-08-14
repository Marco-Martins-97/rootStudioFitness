document.addEventListener("DOMContentLoaded", () => {
    const rootStudio = [41.53346835901555, -8.757288894843745]; // Localização do Root
    
    // Inicia o mapa
    const map = L.map('map').setView(rootStudio, 13);   //localização, zoom

    // camadas
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Root icon
    const rootIcon = L.icon({
        iconUrl: 'imgs/content/rootIcon.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32]
    });
    L.marker(rootStudio, { icon: rootIcon }).addTo(map).bindPopup('Root Studio');

    // Check geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLoc = [position.coords.latitude, position.coords.longitude];

                // User marker
                const userIcon = L.icon({
                    iconUrl: 'imgs/content/userIcon.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                });
                L.marker(userLoc, { icon: userIcon }).addTo(map).bindPopup("You are here");

                // Fit map to both markers
                map.fitBounds([rootStudio, userLoc]);

                // Fetch route from OSRM
                const routingUrl = `https://router.project-osrm.org/route/v1/driving/${userLoc[1]},${userLoc[0]};${rootStudio[1]},${rootStudio[0]}?overview=full&geometries=geojson`;

                fetch(routingUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.routes && data.routes.length > 0) {
                            const routeCoords = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
                            L.polyline(routeCoords, { color: 'blue', weight: 4 }).addTo(map);
                        } else {
                            alert("Rota não encontrada.");
                        }
                    })
                    .catch(err => {
                        console.error("Routing error:", err);
                        alert("Falha ao carregar a rota.");
                    });
            },
            (error) => {
                console.error("Geolocation error:", error.message);
                alert("Não foi possível obter a sua localização.");
            }
        );
    } else {
        alert("A geolocalização não é suportada pelo seu navegador.");
    }
});