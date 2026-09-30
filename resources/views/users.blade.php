<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Users - Tunggu Jahit Bu Yasri</title>

    <style>
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

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #263238;
            color: white;
        }
    </style>
</head>

<body>

    <div class="sidebar">

        <h2>Jahit Bu Yasri</h2>

        <a href="/">Dashboard</a>
        <a href="/users">Users</a>
        <a href="#">Customers</a>
        <a href="#">Services</a>
        <a href="#">Orders</a>
        <a href="#">Status Log</a>
        <a href="#">Reports</a>

    </div>

    <div class="content">

        <div class="card">

            <h1>Users</h1>

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($users as $user)

                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->role->role_name ?? '-' }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone_number }}</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
                                Belum ada data user.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>