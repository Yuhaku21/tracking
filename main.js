let map = L.map('map').setView([-8.5830695, 116.3202515], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let marker = null;
let watchID = null;

let positions = [];
let bestAccuracy = 9999;

const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));

// Tombol lokasi
const locateControl = L.control({ position: "topright" });

locateControl.onAdd = function () {
    const div = L.DomUtil.create("div", "leaflet-bar leaflet-control");
    div.innerHTML = '<a href="#" title="Sesuaikan Koordinat" style="padding:8px;">📍</a>';
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
        alert("Geolocation tidak didukung.");
        return;
    }

    positions = [];
    bestAccuracy = 9999;

    loadingModal.show(); // 🔥 tampilkan modal loading

    watchID = navigator.geolocation.watchPosition(
        successHandler,
        errorHandler,
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

function successHandler(position) {

    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const accuracy = position.coords.accuracy;

    console.log("Akurasi:", accuracy);

    // Ambil semua sample cepat tanpa filter keras
    positions.push({ lat, lng, accuracy });

    if (accuracy < bestAccuracy) {
        bestAccuracy = accuracy;
    }

    // 🔥 Kalau sudah sangat akurat (<15m) langsung pakai
    if (accuracy <= 15) {
        finalizeLocation();
        return;
    }

    // 🔥 Atau kalau sudah 4 sample, langsung proses (biar cepat)
    if (positions.length >= 4) {
        finalizeLocation();
    }
}

function finalizeLocation() {

    navigator.geolocation.clearWatch(watchID);

    let avgLat = positions.reduce((sum, p) => sum + p.lat, 0) / positions.length;
    let avgLng = positions.reduce((sum, p) => sum + p.lng, 0) / positions.length;

    map.setView([avgLat, avgLng], 18);

    if (!marker) {
        marker = L.marker([avgLat, avgLng]).addTo(map);
    } else {
        marker.setLatLng([avgLat, avgLng]);
    }

    marker.bindPopup(`
        <b>Lokasi Anda</b><br>
        Lat: ${avgLat.toFixed(6)}<br>
        Lng: ${avgLng.toFixed(6)}<br>
        Akurasi Terbaik: ±${Math.round(bestAccuracy)} meter
    `).openPopup();

    loadingModal.hide(); // 🔥 tutup modal setelah selesai
}

function errorHandler(error) {
    loadingModal.hide();
    alert("Gagal mendapatkan lokasi: " + error.message);
}