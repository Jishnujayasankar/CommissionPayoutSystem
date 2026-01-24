<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Affiliate Commission System')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a2e 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(90deg, #0f3460 0%, #16213e 100%);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
        }
        .navbar h1 {
            color: #4da6ff;
            font-size: 1.8rem;
        }
        .nav-links {
            margin-top: 1rem;
        }
        .nav-links a {
            color: #4da6ff;
            text-decoration: none;
            margin-right: 2rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: rgba(77, 166, 255, 0.2);
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .alert-success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            color: #00ff00;
        }
        .alert-error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #ff0000;
            color: #ff0000;
        }
        .card {
            background: linear-gradient(135deg, #16213e 0%, #0f3460 100%);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(77, 166, 255, 0.2);
            margin-bottom: 2rem;
        }
        .card h2 {
            color: #4da6ff;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4da6ff;
            font-weight: 500;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid #4da6ff;
            border-radius: 5px;
            color: #ffffff;
            font-size: 1rem;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(77, 166, 255, 0.5);
        }
        .btn {
            padding: 0.75rem 2rem;
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table th {
            background: rgba(77, 166, 255, 0.2);
            color: #4da6ff;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #4da6ff;
        }
        table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(77, 166, 255, 0.2);
        }
        table tr:hover {
            background: rgba(77, 166, 255, 0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🎯 Affiliate Commission System</h1>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}">📊 Dashboard</a>
            <a href="{{ route('users.create') }}">➕ Add User</a>
            <a href="{{ route('commission-levels.index') }}">⚙️ Commission Levels</a>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ✗ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
