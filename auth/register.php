<!DOCTYPE html>
<html>

<head>
    <title>Register Admin</title>
</head>

<body>

    <h2>Register Admin</h2>

    <form action="proses_register.php" method="POST">

        <input type="text" name="nama" placeholder="Nama Admin" required>
        <br><br>

        <input type="text" name="kode_kantor" placeholder="Kode Kantor" required>
        <br><br>

        <input type="password" name="password" placeholder="Password" required>
        <br><br>

        <button type="submit">Register</button>

    </form>

    <br>

    <a href="login.php">Login</a>

</body>

</html>