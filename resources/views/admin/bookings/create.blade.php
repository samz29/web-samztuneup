@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Booking Service</h1>
    <form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
        @csrf

        <div class="mb-3">
            <label for="searchInput" class="form-label">Cari Lokasi:</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Cari alamat...">
            <ul id="suggestionsList" class="list-group" style="display: none;"></ul>
        </div>

        <div id="mapContainer" style="height: 300px;"></div>
        <input type="hidden" name="latitude" id="latInput">
        <input type="hidden" name="longitude" id="lngInput">
        <input type="hidden" name="distance_km" id="distanceInput">

        <div class="mb-3">
            <label for="addressInput" class="form-label">Alamat:</label>
            <textarea class="form-control" id="addressInput" name="customer_address" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="customer_name" class="form-label">Nama:</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name">
        </div>

        <div class="mb-3">
            <label for="customer_phone" class="form-label">Telepon:</label>
            <input type="text" class="form-control" id="customer_phone" name="customer_phone">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
<link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi Platform
        const platform = new H.service.Platform({
            'apikey': '{{ $mapsApiKey }}'
        });
        const defaultLayers = platform.createDefaultLayers();
        const service = platform.getSearchService();

        // Koordinat Base Workshop (Dari Controller)
        const baseLat = {{ $baseLat }};
        const baseLng = {{ $baseLng }};
        const basePoint = new H.geo.Point(baseLat, baseLng);

        // 2. Render Peta
        const map = new H.Map(
            document.getElementById('mapContainer'),
            defaultLayers.vector.normal.map,
            {
                zoom: 13,
                center: { lat: baseLat, lng: baseLng },
                pixelRatio: window.devicePixelRatio || 1
            }
        );

        window.addEventListener('resize', () => map.getViewPort().resize());
        const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
        const ui = H.ui.UI.createDefault(map, defaultLayers);

        let mainMarker = null;

        // 3. Fungsi Marker & Drag
        function initMarker(lat, lng) {
            if (mainMarker) map.removeObject(mainMarker);

            mainMarker = new H.map.Marker({ lat: lat, lng: lng }, { volatility: true });
            mainMarker.draggable = true;
            map.addObject(mainMarker);

            updateLocationData(lat, lng);

            // Event Listeners
            map.addEventListener('dragstart', (ev) => {
                if (ev.target instanceof H.map.Marker) behavior.disable();
            }, false);

            map.addEventListener('dragend', (ev) => {
                if (ev.target instanceof H.map.Marker) {
                    behavior.enable();
                    const coord = ev.target.getGeometry();
                    updateLocationData(coord.lat, coord.lng);
                }
            }, false);

            map.addEventListener('drag', (ev) => {
                if (ev.target instanceof H.map.Marker) {
                    const pointer = ev.currentPointer;
                    ev.target.setGeometry(map.screenToGeo(pointer.viewportX - ev.target['offset'].x, pointer.viewportY - ev.target['offset'].y));
                }
            }, false);
        }

        // 4. Update Data Form & Hitung Jarak
        function updateLocationData(lat, lng) {
            // Isi Input Hidden
            document.getElementById('latInput').value = lat.toFixed(6);
            document.getElementById('lngInput').value = lng.toFixed(6);

            // Hitung Jarak ke Base
            const currentPoint = new H.geo.Point(lat, lng);
            const distanceMeters = basePoint.distance(currentPoint);
            const distanceKm = (distanceMeters / 1000).toFixed(2);
            document.getElementById('distanceInput').value = distanceKm;

            // Reverse Geocode (Ambil Alamat)
            reverseGeocode(lat, lng);
        }

        function reverseGeocode(lat, lng) {
            document.getElementById('addressInput').value = "Mengambil alamat...";
            service.reverseGeocode({
                at: lat + "," + lng
            }, (result) => {
                if (result.items.length > 0) {
                    const item = result.items[0];
                    document.getElementById('addressInput').value = item.address.label;
                } else {
                    document.getElementById('addressInput').value = "Alamat tidak ditemukan";
                }
            }, (error) => {
                console.error(error);
            });
        }

        // 5. Autocomplete Search
        const searchInput = document.getElementById('searchInput');
        const suggestionsList = document.getElementById('suggestionsList');
        let debounceTimeout = null;

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value;
            clearTimeout(debounceTimeout);
            if (query.length < 3) {
                suggestionsList.style.display = 'none';
                return;
            }

            debounceTimeout = setTimeout(() => {
                const center = map.getCenter();
                service.autosuggest({
                    q: query,
                    at: center.lat + "," + center.lng,
                    limit: 5
                }, (result) => {
                    suggestionsList.innerHTML = '';
                    suggestionsList.style.display = 'block';
                    result.items.forEach(item => {
                        if (item.position) {
                            const li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action';
                            li.style.cursor = 'pointer';
                            li.textContent = item.title + " (" + (item.address.label || '') + ")";
                            li.onclick = () => {
                                map.setCenter(item.position);
                                map.setZoom(15);
                                initMarker(item.position.lat, item.position.lng);
                                suggestionsList.style.display = 'none';
                                searchInput.value = '';
                            };
                            suggestionsList.appendChild(li);
                        }
                    });
                }, alert);
            }, 300);
        });

        // Inisialisasi Awal (Di Base Workshop)
        initMarker(baseLat, baseLng);
    });
</script>
@endsection
