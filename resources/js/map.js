// HvA Campus Amstelcampus coordinates
const hvaCoordinates = [52.3590, 4.9092];

// Initialize map with zoom controls
const map = L.map('map', {
    zoomControl: true,
    minZoom: 2,
    maxZoom: 19
}).setView(hvaCoordinates, 16);

// Add tile layer with maxZoom specified
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19,
    maxNativeZoom: 19
}).addTo(map);

// Sample data for the chart
const hourlyData = {
    labels: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
    datasets: [{
        label: 'Noise Level (dB)',
        data: [45, 42, 47, 65, 71, 68, 69, 55],
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
    }]
};

// Configure marker and popup
const marker = L.marker(hvaCoordinates).addTo(map);
const popupContent = document.createElement('div');
popupContent.classList.add('popup-content');

// Create canvas for the chart
const canvas = document.createElement('canvas');
popupContent.appendChild(canvas);

const popup = L.popup()
    .setContent(popupContent);

marker.bindPopup(popup);

// Track chart instance
let activeChart = null;

// Simple zoom handler
marker.on('click', function(e) {
    const currentZoom = map.getZoom();
    const markerLatLng = marker.getLatLng();

    // Offset the center point slightly north of the marker
    const offsetLatLng = [markerLatLng.lat + 0.0002, markerLatLng.lng];

    map.setView(offsetLatLng, currentZoom === 19 ? 16 : 19);
    marker.openPopup();
});

// Cleanup chart on popup close
marker.on('popupclose', function() {
    if (activeChart) {
        activeChart.destroy();
        activeChart = null;
    }
});

// Initialize chart on popup open
marker.on('popupopen', function() {
    if (activeChart) {
        activeChart.destroy();
    }

    activeChart = new Chart(canvas, {
        type: 'line',
        data: hourlyData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Hourly Noise Levels'
                },
                legend: {
                    onClick: null  // Disable legend click interaction
                }
            }
        }
    });
});
