<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Tunggu Jahit Bu Yasri</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 230px;
            height: 100vh;
            background: #263238;
            color: white;
            padding: 25px 15px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px 15px;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background: #455a64;
        }

        .content {
            margin-left: 230px;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 20px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin-top: 0;
            color: #555;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 180px;
            }

            .content {
                margin-left: 180px;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <h2>Jahit Bu Yasri</h2>

        <a href="/">Dashboard</a>
        <a href="#">Users</a>
        <a href="#">Customers</a>
        <a href="#">Services</a>
        <a href="#">Orders</a>
        <a href="#">Status Log</a>
        <a href="#">Reports</a>

    </div>


    <!-- CONTENT -->
    <div class="content">

        <div class="header">
            <h1>Dashboard</h1>
            <p>Selamat datang di Sistem Manajemen Tunggu Jahit Bu Yasri.</p>
        </div>


        <!-- CARDS -->
        <div class="cards">

            <div class="card">
                <h3>Total Users</h3>
                <div class="number">0</div>
            </div>

            <div class="card">
                <h3>Total Customers</h3>
                <div class="number">0</div>
            </div>

            <div class="card">
                <h3>Total Orders</h3>
                <div class="number">0</div>
            </div>

            <div class="card">
                <h3>Total Services</h3>
                <div class="number">0</div>
            </div>

            <div class="card">
                <h3>Total Revenue</h3>
                <div class="number">Rp 0</div>
            </div>

            <div class="card">
                <h3>Status</h3>
                <div class="number">Aktif</div>
            </div>

        </div>

    </div>

</body>
</html>