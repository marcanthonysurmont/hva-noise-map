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

// Generate weekly data structure starting from current week
function generateWeeklyData() {
    const weeklyData = {};
    const today = new Date();
    const monday = new Date(today);
    monday.setDate(today.getDate() - today.getDay() + 1); // Get Monday of current week

    // Generate data for each day of the week
    for (let i = 0; i < 7; i++) {
        const currentDate = new Date(monday);
        currentDate.setDate(monday.getDate() + i);
        const dateString = currentDate.toISOString().split('T')[0];

        // Check if date is in future
        if (currentDate > today) {
            continue; // Skip future dates
        }

        const hourlyData = [];
        const labels = [];

        // Generate data for each hour
        for (let hour = 0; hour < 24; hour++) {
            const currentDateTime = new Date(currentDate);
            currentDateTime.setHours(hour, 0, 0, 0);

            // Only add data for past hours on current day
            if (currentDateTime <= today) {
                labels.push(`${hour.toString().padStart(2, '0')}:00`);
                hourlyData.push(Math.floor(Math.random() * 35) + 40);
            }
        }

        weeklyData[dateString] = {
            labels: labels,
            data: hourlyData
        };
    }
    return weeklyData;
}

const weeklyData = generateWeeklyData();

// Create popup with correct positioning
const popup = L.popup({
    maxWidth: 300,
    minWidth: 250,
    offset: [0, -30],  // Increased offset upward
    autoPan: true,     // Ensures popup is always in view
    closeButton: true,
    autoClose: true,
    className: 'custom-popup',
    // Set anchor point to bottom center of popup
    autoPanPadding: [50, 50],
    keepInView: true
});

// Create popup content with adjusted dimensions
const popupContent = document.createElement('div');
popupContent.classList.add('popup-content');
popupContent.style.width = '100%';
popupContent.style.height = '250px';  // Reduced from 300px

// Style date container for better fit
const dateContainer = document.createElement('div');
dateContainer.style.marginBottom = '5px';  // Reduced from 10px
dateContainer.style.display = 'flex';
dateContainer.style.gap = '5px';

// Set up week select
const weekSelect = document.createElement('select');
weekSelect.style.marginRight = '10px';
Object.keys(weeklyData).forEach(date => {
    const option = document.createElement('option');
    option.value = date;
    option.text = new Date(date).toLocaleDateString('en-US',
        { weekday: 'short', month: 'short', day: 'numeric' });
    weekSelect.appendChild(option);
});

// Adjust select and datepicker sizes
weekSelect.style.flex = '1';

// Set up date picker
const datePicker = document.createElement('input');
datePicker.type = 'date';
datePicker.valueAsDate = new Date();

// Adjust select and datepicker sizes
datePicker.style.flex = '1';

// Adjust canvas size
const canvas = document.createElement('canvas');
canvas.style.width = '100%';
canvas.style.height = '200px';  // Reduced from 250px

// Assemble all elements
dateContainer.appendChild(weekSelect);
dateContainer.appendChild(datePicker);
popupContent.appendChild(dateContainer);
popupContent.appendChild(canvas);

// Set popup content
popup.setContent(popupContent);

// Add marker with specific popup anchor
const marker = L.marker(hvaCoordinates, {
    // Set popup to anchor to top of marker
    popupAnchor: [0, -10]
}).addTo(map);

// Track chart instance
let activeChart = null;

// Track popup state
let isPopupOpen = false;

// Add click handler
marker.on('click', async function() {
    popup.setLatLng(hvaCoordinates).openOn(map);
    const currentDate = datePicker.value;
    setTimeout(async () => {
        await updateChart(currentDate);
    }, 100);
});

// Update popup states
marker.on('popupopen', function() {
    isPopupOpen = true;
    const currentDate = datePicker.value;
    setTimeout(async () => {
        await updateChart(currentDate);
    }, 100); // Small delay to ensure canvas is rendered
});

marker.on('popupclose', function() {
    isPopupOpen = false;
    if (activeChart) {
        activeChart.destroy();
        activeChart = null;
    }
});

async function fetchDataForDate(selectedDate) {
    try {
        const response = await fetch(`api/noise-levels/data?date=${selectedDate}`);
        if (!response.ok) throw new Error('Network response was not ok');
        return await response.json();
    } catch (error) {
        console.error('Error fetching noise data:', error);
        return { labels: [], data: [] };
    }
}

async function updateChart(selectedDate) {
    if (!selectedDate) return;

    const { labels, data } = await fetchDataForDate(selectedDate);

    if (activeChart) {
        activeChart.destroy();
    }

    activeChart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Noise Level (dB)',
                data: data,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Function to find and select matching week option
function updateWeekSelect(selectedDate) {
    const options = Array.from(weekSelect.options);
    const matchingOption = options.find(option => option.value === selectedDate);

    if (matchingOption) {
        weekSelect.value = selectedDate;
    } else {
        // If date is outside current week, repopulate week select
        const selected = new Date(selectedDate);
        const monday = new Date(selected);
        monday.setDate(selected.getDate() - selected.getDay() + 1);

        populateWeekSelect(monday);
        weekSelect.value = selectedDate;
    }
}

// Update datepicker event listener
datePicker.addEventListener('change', async (e) => {
    const selectedDate = e.target.value;
    updateWeekSelect(selectedDate);
    await updateChart(selectedDate);
});

// Modify populateWeekSelect to accept start date
function populateWeekSelect(startDate = new Date()) {
    weekSelect.innerHTML = '';
    const monday = new Date(startDate);
    monday.setDate(monday.getDate() - monday.getDay() + 1);

    for (let i = 0; i < 7; i++) {
        const currentDate = new Date(monday);
        currentDate.setDate(monday.getDate() + i);
        const dateString = currentDate.toISOString().split('T')[0];

        const option = document.createElement('option');
        option.value = dateString;
        option.text = currentDate.toLocaleDateString('en-US',
            { weekday: 'short', month: 'short', day: 'numeric' });
        weekSelect.appendChild(option);
    }
}

// Initialize selections
populateWeekSelect();
datePicker.valueAsDate = new Date();
const initialDate = datePicker.value;
updateChart(initialDate);

// Event listeners
weekSelect.addEventListener('change', async (e) => {
    datePicker.value = e.target.value;
    await updateChart(e.target.value);
});

datePicker.addEventListener('change', async (e) => {
    const selectedDate = e.target.value;
    updateWeekSelect(selectedDate);
    await updateChart(selectedDate);
});

marker.on('popupopen', function() {
    const currentDate = datePicker.value;
    updateWeekSelect(currentDate);
    updateChart(currentDate);
});
