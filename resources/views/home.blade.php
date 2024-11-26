@include('layouts.header')

<div class="min-h-screen bg-gray-50">
    <!-- Hero section with SVG pattern -->
    <div class="bg-gradient-to-r from-[#69b357] to-[#7ac968] text-white py-6 relative overflow-hidden">
        <!-- SVG Pattern overlay -->
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="3" cy="3" r="2" fill="#ffffff"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#dots)"/>
            </svg>
        </div>

        <!-- Content -->
        <div class="container mx-auto px-4 relative z-10">
            <h1 class="text-4xl font-bold text-center">Noise Level Monitoring</h1>
            <p class="text-center text-blue-100 mt-2">Real-time noise level measurements across the HvA Amstelcampus</p>
        </div>
    </div>

    <!-- Full-width Map container -->
    <div class="bg-white shadow-sm">
        <div id="map" class="h-[500px] w-full"></div>
        <div class="bg-white p-7">
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
