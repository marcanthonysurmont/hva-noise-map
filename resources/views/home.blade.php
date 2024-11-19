@include('layouts.header')

<div class="min-h-screen bg-gray-50">
    <!-- Hero section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-center">Noise Level Monitoring</h1>
            <p class="text-center text-blue-100 mt-2">Real-time noise level measurements across the HvA Amstelcampus</p>
        </div>
    </div>

    <!-- Full-width Map container -->
    <div class="bg-white shadow-sm">
        <div id="map" class="h-[500px] w-full"></div>
        <div class="bg-white p-7"> <!-- Added wrapper with explicit padding -->
            <div class="container mx-auto px-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Amsterdam Map</h2>
                <p class="text-gray-600">Click on markers to view detailed noise level data</p>
            </div>
        </div>
    </div>

    <!-- Stats cards with border and padding -->
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-7 pb-7"> <!-- Added padding-top after the line -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-gray-500 text-sm font-medium mb-2">Current Noise Level</h3>
                <p class="text-3xl font-bold text-gray-900">65 dB</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-gray-500 text-sm font-medium mb-2">Daily Average</h3>
                <p class="text-3xl font-bold text-gray-900">58 dB</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-gray-500 text-sm font-medium mb-2">Active Sensors</h3>
                <p class="text-3xl font-bold text-gray-900">1</p>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
