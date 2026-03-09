<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/style.css">
</head>

<body>
    <div class="app">
        <div class="header">
            <!--Header Here-->
        </div>
        <div class="content mt-4">
            <div class="main-content">
                <!--Logo Damara-->
                <img src="" alt="">
                <!--Logo Damara-->
                <h5 class="text-center mb-3 mt-3"><b>Selamat Datang di Portal Damara AO Tracking</b></h5>
                <div class="card">
                    <div class="card-body">
                        <form action="proses_login.php" method="post">
                            <div class="mb-3">
                                <label class="form-label">Nama Staff</label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" type="submit">Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--Footer-->
            <footer class="fixed-bottom">
                <p class="text-center" style="color: grey;">Version 1.0.0</p>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>