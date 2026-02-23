let map = L.map('map').setView([-8.5830695, 116.3202515], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let marker = null;
let watchID = null;

let positions = [];
let bestAccuracy = 9999;

// Tombol lokasi
const locateControl = L.control({ position: "topright" });

locateControl.onAdd = function () {
    const div = L.DomUtil.create("div", "leaflet-bar leaflet-control");
    div.innerHTML = '<a href="#" title="Lokasi Saya" style="padding:8px;">📍</a>';
    div.style.background = "#fff";
    div.style.cursor = "pointer";

    div.onclick = function (e) {
        e.preventDefault();
        requestLocation();
    };

    return div;
};

locateControl.addTo(map);

function requestLocation() {

    if (!navigator.geolocation) {
        console.log("Geolocation tidak didukung.");
        return;
    }

    positions = [];
    bestAccuracy = 9999;

    watchID = navigator.geolocation.watchPosition(
        successHandler,
        errorHandler,
        {
            enableHighAccuracy: true,
            timeout: 20000,
            maximumAge: 0
        }
    );
}

function successHandler(position) {

    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const accuracy = position.coords.accuracy;

    console.log("Akurasi:", accuracy);

    // ❌ Abaikan kalau akurasi terlalu jelek
    if (accuracy > 50) return;

    // Simpan posisi
    positions.push({ lat, lng, accuracy });

    // Simpan akurasi terbaik
    if (accuracy < bestAccuracy) {
        bestAccuracy = accuracy;
    }

    // Ambil 5 sample dulu supaya stabil
    if (positions.length < 5) return;

    // Stop tracking setelah cukup sample
    navigator.geolocation.clearWatch(watchID);

    // Hitung rata-rata koordinat (lebih stabil)
    let avgLat = positions.reduce((sum, p) => sum + p.lat, 0) / positions.length;
    let avgLng = positions.reduce((sum, p) => sum + p.lng, 0) / positions.length;

    map.setView([avgLat, avgLng], 18);

    if (!marker) {
        marker = L.marker([avgLat, avgLng]).addTo(map);
    } else {
        marker.setLatLng([avgLat, avgLng]);
    }

    marker.bindPopup(`
        <b>Lokasi Anda (Stabil)</b><br>
        Lat: ${avgLat.toFixed(6)}<br>
        Lng: ${avgLng.toFixed(6)}<br>
        Akurasi Terbaik: ±${Math.round(bestAccuracy)} meter
    `).openPopup();
}

function errorHandler(error) {
    console.log("Geolocation error:", error.message);
}