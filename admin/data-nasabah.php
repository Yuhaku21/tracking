<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!--Link style-->
    <link rel="stylesheet" href="style-dashboard-admin.css">
</head>

<body>

    <!-- Navbar Atas -->
    <nav class="navbar navbar-light bg-white shadow-sm px-3">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <i class="bi bi-list"></i>
        </button>
        <span class="fw-bold">Dashboard Admin</span>
    </nav>

    <!-- Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link active" href="#">
                        <i class="bi bi-house-door me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#">
                        <i class="bi bi-person-badge me-2"></i> Data AO
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#">
                        <i class="bi bi-people me-2"></i> Data Nasabah
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Content -->
    <div class="container mt-4">

        <h2><b>Data Nasabah</b></h2>

        <!--CTA Breadcrumb-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="data-nasabah.html">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Nasabah</li>
            </ol>
        </nav>

        <!--Main Content-->
        <button class="btn btn-primary" style="font-size: 14px;">Tambah Data Nasabah</button>

        <!--Show data nasabah-->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th scope="col">NB</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>

                    <td>Muhammad Jokowi</td>
                    <td>
                        <a href="" class="btn btn-warning"><i class="bi bi-pencil-square"></i></a>
                        <a href="" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                        <a href="" class="btn btn-info"><i class="bi bi-info-circle"></i></a>
                    </td>
                </tr>
                <tr>

                    <td>Asep Sunardin</td>
                    <td>
                        <a href="" class="btn btn-warning"><i class="bi bi-pencil-square"></i></a>
                        <a href="" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                        <a href="" class="btn btn-info"><i class="bi bi-info-circle"></i></a>
                    </td>
                </tr>
                <tr>

                    <td>Farhan Indra</td>
                    <td>
                        <a href="" class="btn btn-warning"><i class="bi bi-pencil-square"></i></a>
                        <a href="" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                        <a href="" class="btn btn-info"><i class="bi bi-info-circle"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>

        <!--Footer-->
        <footer class="fixed-bottom text-center">
            <p style="color: grey;">Aplikasi Versi 1.0.0</p>
        </footer>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>