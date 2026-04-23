<?php
require '../middleware/auth_staff.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AO Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Google Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
            }

            .header,
            .breadcrumb,
            form,
            .btn,
            .print-button {
                display: none !important;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            h2 {
                text-align: center;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            table th,
            table td {
                border: 1px solid #000;
                padding: 6px;
                text-align: center;
            }

            table th {
                background-color: #f2f2f2;
            }

            .note {
                margin-top: 20px;
                font-size: 12px;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="app">
        <div class="header">
            <h3 style="font-size: 16px;">Damara Tracking Mobile</h3>
        </div>

        <div class="content">
            <!--HeroSection-->
            <div class="welcome">
                <h2 id="username">Simulasi Kredit</h2>
            </div>

            <!--CTA Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Simulasi Kredit</li>
                </ol>
            </nav>

            <!--MainContent-->
            <div class="container">
                <div class="card">
                    <div class="card-body">
                        <form id="loan-simulation-form">
                            <div class="form-row">
                                <div class="form-group mb-3">
                                    <label for="loan-amount">Jumlah Pinjaman (Rp)</label>
                                    <input type="text" id="loan-amount" class="form-control" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="loan-term">Jangka Waktu (bulan)</label>
                                    <input type="number" id="loan-term" class="form-control" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="interest-rate">Suku Bunga (%) per tahun</label>
                                    <input type="number" id="interest-rate" class="form-control" step="0.01" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="interest-type">Jenis Perhitungan Bunga</label>
                                    <select id="interest-type" class="form-control">
                                        <option value="flat">Flat</option>
                                        <option value="effective">Efektif</option>
                                        <option value="annuity">Anuitas</option>
                                    </select>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary btn-block mt-3 mb-3" onclick="calculateLoan()">Hitung Angsuran</button>
                        </form>

                        <div class="result" id="result">
                            <p>Angsuran per bulan: <span id="monthly-payment">-</span></p>
                        </div>
                    </div>
                </div>

                <div id="payment-schedule" style="display: none;">
                    <div class="summary" id="summary">
                        <!-- Ringkasan akan dimasukkan di sini -->
                    </div>
                    <button class="btn btn-success print-button mt-3" onclick="printSchedule()">Cetak Rincian Angsuran</button>
                    <div class="spacer mt-3 mb-3"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="schedule-table">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Angsuran Pokok</th>
                                    <th>Bunga</th>
                                    <th>Total Angsuran</th>
                                    <th>Sisa Pinjaman</th>
                                </tr>
                            </thead>
                            <tbody id="schedule-body">
                                <!-- Data angsuran akan dimasukkan di sini -->
                            </tbody>
                        </table>
                    </div>
                    <div class="note">
                        Daftar Rincian Angsuran ini hanya simulasi perhitungan saja, silahkan hubungi kami segera.
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="main.js"></script>
    <script>
        document.getElementById('loan-amount').addEventListener('input', function(e) {
            let value = e.target.value;
            value = value.replace(/\./g, '')
                .replace(/\D/g, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            e.target.value = value;
        });

        // ================= HITUNG PINJAMAN =================
        function calculateLoan() {
            var loanAmount = parseFloat(
                document.getElementById('loan-amount').value.replace(/\./g, '').replace(/\D/g, '')
            );
            var loanTerm = parseInt(document.getElementById('loan-term').value);
            var interestRate = parseFloat(document.getElementById('interest-rate').value);
            var interestType = document.getElementById('interest-type').value;

            if (!loanAmount || !loanTerm || !interestRate) {
                alert("Semua nilai harus diisi dengan benar!");
                return;
            }

            var monthlyInterestRate = (interestRate / 100) / 12;
            var monthlyPayment = 0;

            // ================= PERHITUNGAN =================
            if (interestType === 'flat') {
                var monthlyPrincipal = loanAmount / loanTerm;
                var monthlyInterest = loanAmount * monthlyInterestRate;
                monthlyPayment = monthlyPrincipal + monthlyInterest;
            } else if (interestType === 'effective') {
                monthlyPayment = loanAmount / loanTerm + loanAmount * monthlyInterestRate;
            } else if (interestType === 'annuity') {
                var base = Math.pow(1 + monthlyInterestRate, loanTerm);
                monthlyPayment = (loanAmount * monthlyInterestRate * base) / (base - 1);
            }

            document.getElementById('monthly-payment').textContent =
                'Rp ' + formatRupiah(Math.round(monthlyPayment));

            // TAMPILKAN TABEL
            document.getElementById('payment-schedule').style.display = 'block';

            var scheduleBody = document.getElementById('schedule-body');
            scheduleBody.innerHTML = '';

            var remainingBalance = loanAmount;

            // ================= LOOP TABEL =================
            for (var i = 1; i <= loanTerm; i++) {
                var interest = 0;
                var principal = 0;
                var total = monthlyPayment;

                if (interestType === 'flat') {
                    interest = loanAmount * monthlyInterestRate;
                    principal = monthlyPayment - interest;
                    remainingBalance -= principal;
                } else if (interestType === 'effective') {
                    interest = remainingBalance * monthlyInterestRate;
                    principal = loanAmount / loanTerm;
                    total = principal + interest;
                    remainingBalance -= principal;
                } else if (interestType === 'annuity') {
                    interest = remainingBalance * monthlyInterestRate;
                    principal = monthlyPayment - interest;
                    remainingBalance -= principal;
                }

                var row = `
            <tr>
                <td>${i}</td>
                <td>Rp ${formatRupiah(Math.round(principal))}</td>
                <td>Rp ${formatRupiah(Math.round(interest))}</td>
                <td>Rp ${formatRupiah(Math.round(total))}</td>
                <td>Rp ${formatRupiah(Math.round(Math.max(0, remainingBalance)))}</td>
            </tr>
        `;

                scheduleBody.innerHTML += row;
            }
        }

        // ================= FORMAT RUPIAH =================
        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // ================= PRINT =================
        function printSchedule() {
            // pastikan data sudah muncul
            var table = document.getElementById('payment-schedule');

            if (table.style.display === 'none') {
                alert("Silakan hitung angsuran terlebih dahulu!");
                return;
            }

            // kasih delay biar render dulu
            setTimeout(() => {
                window.print();
            }, 300);
        }
    </script>
</body>

</html>