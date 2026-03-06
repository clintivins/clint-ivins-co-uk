<?php
require_once 'secrets.php';
$google_maps_key = defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomad-Approved Map | T3chN0mad</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .map-layout {
            display: flex;
            gap: 2rem;
            margin-top: 20px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        #places-sidebar {
            flex: 1;
            min-width: 300px;
            max-width: 350px;
            background: rgba(10, 10, 15, 0.8);
            border: 1px solid var(--neon-cyan);
            border-radius: 10px;
            height: 600px;
            overflow-y: auto;
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        /* Custom Scrollbar for Sidebar */
        #places-sidebar::-webkit-scrollbar {
            width: 8px;
        }

        #places-sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
        }

        #places-sidebar::-webkit-scrollbar-thumb {
            background: var(--neon-cyan);
            border-radius: 4px;
        }

        .sidebar-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 5px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--neon-cyan);
        }

        .sidebar-item h4 {
            color: white;
            margin: 0.5rem 0;
            font-size: 1.1rem;
            font-family: 'Outfit', sans-serif;
        }

        #map-container {
            flex: 2;
            min-width: 300px;
            height: 600px;
            border: 1px solid var(--neon-cyan);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.1);
        }

        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .map-filters {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s;
            font-family: 'Space Mono', monospace;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .filter-btn.active[data-filter="all"] {
            border-color: white;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            color: white;
        }

        .filter-btn[data-filter="tech"] {
            color: var(--neon-cyan);
        }

        .filter-btn.active[data-filter="tech"] {
            border-color: var(--neon-cyan);
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.3);
        }

        .filter-btn[data-filter="travel"] {
            color: var(--neon-gold);
        }

        .filter-btn.active[data-filter="travel"] {
            border-color: var(--neon-gold);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }

        .filter-btn[data-filter="taste"] {
            color: #ff3366;
        }

        .filter-btn.active[data-filter="taste"] {
            border-color: #ff3366;
            box-shadow: 0 0 10px rgba(255, 51, 102, 0.3);
        }

        /* Modal styling */
        .pin-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(10, 10, 15, 0.95);
            border: 1px solid var(--neon-cyan);
            box-shadow: 0 0 30px rgba(0, 243, 255, 0.3);
            padding: 2.5rem;
            border-radius: 10px;
            z-index: 2000;
            display: none;
            width: 90%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }

        .pin-modal h3 {
            color: var(--neon-cyan);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pin-modal input,
        .pin-modal select,
        .pin-modal textarea {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: white;
            border-radius: 5px;
            font-family: 'Space Mono', monospace;
        }

        .pin-modal input:focus,
        .pin-modal select:focus,
        .pin-modal textarea:focus {
            outline: none;
            border-color: var(--neon-cyan);
            background: rgba(0, 243, 255, 0.05);
        }

        .pin-modal select option {
            background: #0a0a0f;
            color: white;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
        }

        .modal-buttons button {
            flex: 1;
            padding: 1rem;
            cursor: pointer;
        }

        .add-hint {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(10, 10, 15, 0.8);
            border: 1px solid var(--neon-gold);
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            color: var(--neon-gold);
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
            z-index: 500;
            pointer-events: none;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
                box-shadow: 0 0 20px rgba(255, 215, 0, 0.4);
            }

            100% {
                opacity: 0.7;
            }
        }

        /* Search Bar */
        #pac-input {
            background-color: #fff;
            color: #000;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 300;
            margin-top: 10px;
            padding: 10px 15px;
            text-overflow: ellipsis;
            width: 80%;
            max-width: 400px;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            border-radius: 5px;
            border: 2px solid var(--neon-cyan);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #pac-input:focus {
            border-color: var(--neon-gold);
            outline: none;
        }

        /* Google Maps popup customization */
        .gm-style-iw {
            background-color: #fff !important;
            color: #000 !important;
            border-radius: 5px !important;
            padding: 10px !important;
            font-family: 'Outfit', sans-serif;
        }

        .gm-style-iw-d {
            overflow: auto !important;
        }

        .gm-ui-hover-effect {
            top: 5px !important;
            right: 5px !important;
        }

        .gmap-popup-content h4 {
            color: #000;
            margin: 0 0 5px 0;
            font-size: 1.3rem;
            font-family: 'Outfit', sans-serif;
        }

        .gmap-popup-content .category-label {
            font-size: 0.75rem;
            color: var(--neon-gold);
            margin-bottom: 10px;
            font-family: 'Space Mono', monospace;
            display: inline-block;
            padding: 2px 8px;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .gmap-popup-content .category-label.tech {
            color: #00bcd4;
            background: rgba(0, 188, 212, 0.1);
        }

        .gmap-popup-content .category-label.travel {
            color: #ffb300;
            background: rgba(255, 179, 0, 0.1);
        }

        .gmap-popup-content .category-label.taste {
            color: #ff3366;
            background: rgba(255, 51, 102, 0.1);
        }

        .gmap-popup-content p {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            color: #333;
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="glass-nav">
        <div class="logo">T3ch<span class="highlight">N0mad</span></div>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="dashboard.html">Threat Intel</a></li>
            <li><a href="academy.php">Academy</a></li>
            <li><a href="nomad-map.html" class="highlight" style="text-shadow: 0 0 5px var(--neon-cyan);">NomMap</a>
            </li>
            <li><a href="#contact" class="btn-glow">Connect</a></li>
        </ul>
        <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </nav>

    <main class="container" style="padding-top: 120px;">
        <div class="content-text" style="text-align: center; max-width: 800px; margin: 0 auto 2rem auto;">
            <h1 class="glitch" data-text="Nomad-Approved Map" style="font-size: 3rem; margin-bottom: 1rem;">
                Nomad-Approved Map</h1>
            <p style="font-size: 1.2rem; color: var(--text-muted);">
                A community-built archive of remote-work sanctuaries. Drop a pin or search a place to share it.
            </p>
        </div>

        <div class="map-filters">
            <button class="filter-btn active" data-filter="all"><i class="fas fa-globe"></i> All Spots</button>
            <button class="filter-btn" data-filter="tech"><i class="fas fa-laptop-code"></i> Tech</button>
            <button class="filter-btn" data-filter="travel"><i class="fas fa-plane-departure"></i> Travel</button>
            <button class="filter-btn" data-filter="taste"><i class="fas fa-utensils"></i> Taste</button>
        </div>

        <div class="map-layout">

            <!-- Sidebar for listed places -->
            <div id="places-sidebar">
                <h3
                    style="color: var(--neon-cyan); margin-bottom: 1rem; font-size: 1.2rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">
                    <span><i class="fas fa-list"></i> Nomad Spots</span>
                    <span id="spot-count"
                        style="font-size: 0.9rem; color: var(--text-muted); font-family: 'Space Mono', monospace;">0</span>
                </h3>
                <div id="places-list" style="display: flex; flex-direction: column;">
                    <!-- Places dynamically loaded here -->
                </div>
            </div>

            <!-- Map Container -->
            <div id="map-container">
                <input id="pac-input" class="controls" type="text"
                    placeholder="Search Google Maps (e.g. Costa Coffee, London)..." />
                <div id="map"></div>
                <div class="add-hint"><i class="fas fa-search"></i> Search a place or click map to add a spot</div>

                <!-- Custom Modal -->
                <div id="pinModal" class="pin-modal">
                    <h3><i class="fas fa-map-marker-alt"></i> Add Nomad Spot</h3>
                    <input type="text" id="spotName" placeholder="Spot Name (e.g. Cyber Cafe)" required
                        autocomplete="off">
                    <select id="spotCategory">
                        <option value="tech">Tech (Wi-Fi, Power, Coffee)</option>
                        <option value="travel">Travel (Quiet, Epic workspace)</option>
                        <option value="taste">Taste (Remote-friendly lunch)</option>
                    </select>
                    <textarea id="spotDesc" placeholder="Why is this spot Nomad-Approved?" rows="4" required></textarea>
                    <div class="modal-buttons">
                        <button id="savePinBtn" class="btn-glow"><i class="fas fa-save"></i> Save</button>
                        <button id="cancelPinBtn" class="btn-outline">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Clinton Ivins. All Rights Reserved.</p>
    </footer>

    <!-- Google Maps API - Securely loaded -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo htmlspecialchars($google_maps_key); ?>&libraries=places&callback=initMap"
        async defer></script>

    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', () => {
            const burger = document.querySelector('.burger');
            const nav = document.querySelector('.nav-links');
            if (burger) {
                burger.addEventListener('click', () => {
                    nav.classList.toggle('nav-active');
                    burger.classList.toggle('toggle');
                });
            }
        });

        let map;
        let allMarkers = [];
        let rawPins = [];
        let infoWindow;
        let tempMarker = null;
        let currentLatLng = null;

        const modal = document.getElementById('pinModal');
        const nameInput = document.getElementById('spotName');
        const categorySelect = document.getElementById('spotCategory');
        const descInput = document.getElementById('spotDesc');
        const saveBtn = document.getElementById('savePinBtn');
        const cancelBtn = document.getElementById('cancelPinBtn');
        const placesList = document.getElementById('places-list');
        const spotCount = document.getElementById('spot-count');

        // Custom marker icons 
        const getMarkerIcon = (category) => {
            let color = '#FF0000'; // Default Red
            if (category === 'tech') color = '#00FFFF'; // Cyan
            if (category === 'travel') color = '#FFD700'; // Gold
            if (category === 'taste') color = '#FF3366'; // Pink

            return {
                path: "M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z",
                fillColor: color,
                fillOpacity: 1,
                strokeWeight: 1,
                strokeColor: '#000',
                scale: 1.5,
                anchor: new google.maps.Point(12, 22)
            };
        };

        function initMap() {
            // Init Map centered on the UK
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 54.5, lng: -3.2 },
                zoom: 6,
                // Bright default styling (standard Google Maps style)
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true
            });

            infoWindow = new google.maps.InfoWindow();

            // Places Autocomplete
            const input = document.getElementById('pac-input');
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            autocomplete.addListener('place_changed', () => {
                infoWindow.close();
                const place = autocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                // Prompt user to add this place
                currentLatLng = place.geometry.location;
                openModalForSpot(place.name);
            });

            // Map Click to drop a pin manually
            map.addListener('click', (e) => {
                currentLatLng = e.latLng;
                openModalForSpot('');
            });

            // Fetch and render existing pins
            fetchPins();
        }

        function openModalForSpot(prefilledName) {
            if (tempMarker) tempMarker.setMap(null);

            tempMarker = new google.maps.Marker({
                position: currentLatLng,
                map: map,
                icon: getMarkerIcon('default')
            });

            nameInput.value = prefilledName;
            descInput.value = '';
            modal.style.display = 'block';
            nameInput.focus();
        }

        // Fetch and render pins
        async function fetchPins() {
            try {
                const response = await fetch('map_api.php');
                rawPins = await response.json();
                renderPinsAndSidebar('all');
            } catch (err) {
                console.error('Failed to load map pins:', err);
            }
        }

        function renderPinsAndSidebar(filter) {
            // Clear map markers
            allMarkers.forEach(item => item.marker.setMap(null));
            allMarkers = [];

            // Clear sidebar
            placesList.innerHTML = '';

            let count = 0;

            rawPins.forEach(pin => {
                if (filter !== 'all' && pin.category !== filter) return;

                count++;

                let labelIcon = '';
                let colorClass = '';
                let colorHex = '';
                if (pin.category === 'tech') { labelIcon = '<i class="fas fa-laptop-code" style="margin-right:5px"></i> TECH'; colorClass = 'tech'; colorHex = '#00FFFF'; }
                else if (pin.category === 'travel') { labelIcon = '<i class="fas fa-plane" style="margin-right:5px"></i> TRAVEL'; colorClass = 'travel'; colorHex = '#FFD700'; }
                else if (pin.category === 'taste') { labelIcon = '<i class="fas fa-utensils" style="margin-right:5px"></i> TASTE'; colorClass = 'taste'; colorHex = '#FF3366'; }

                const latLng = new google.maps.LatLng(pin.lat, pin.lng);

                const marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: pin.name,
                    icon: getMarkerIcon(pin.category)
                });

                const contentString = `
                    <div class="gmap-popup-content">
                        <h4>${pin.name}</h4>
                        <div class="category-label ${colorClass}">${labelIcon}</div>
                        <p>${pin.description}</p>
                    </div>
                `;

                marker.addListener('click', () => {
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });

                allMarkers.push({ category: pin.category, marker: marker });

                // Add to sidebar
                const item = document.createElement('div');
                item.className = 'sidebar-item';
                item.innerHTML = `
                    <div style="font-size: 0.75rem; color: ${colorHex}; margin-bottom: 5px; font-family: 'Space Mono', monospace; font-weight: bold;">${labelIcon}</div>
                    <h4>${pin.name}</h4>
                    <p style="font-size: 0.9rem; color: #ccc; margin: 0; line-height: 1.4;">${pin.description.substring(0, 60)}${pin.description.length > 60 ? '...' : ''}</p>
                `;
                item.onclick = () => {
                    map.setCenter(latLng);
                    map.setZoom(15);
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                };
                placesList.appendChild(item);
            });

            spotCount.textContent = count;
        }

        // Cancel button
        cancelBtn.onclick = () => {
            modal.style.display = 'none';
            if (tempMarker) tempMarker.setMap(null);
            tempMarker = null;
        };

        // Save pin
        saveBtn.onclick = async () => {
            const name = nameInput.value.trim();
            const desc = descInput.value.trim();
            const category = categorySelect.value;

            if (!name || !desc) {
                alert('Please provide a name and description.');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            const newPin = {
                name: name,
                category: category,
                description: desc,
                lat: currentLatLng.lat(),
                lng: currentLatLng.lng()
            };

            try {
                const response = await fetch('map_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newPin)
                });

                if (response.ok) {
                    modal.style.display = 'none';
                    if (tempMarker) tempMarker.setMap(null);
                    tempMarker = null;
                    document.getElementById('pac-input').value = '';
                    await fetchPins(); // Reload all pins
                } else {
                    alert('Failed to save pin.');
                }
            } catch (err) {
                console.error('Error saving pin:', err);
                alert('Error saving pin.');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save';
            }
        };

        // Filter Buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');
                renderPinsAndSidebar(e.currentTarget.getAttribute('data-filter'));
            });
        });

    </script>
</body>

</html>