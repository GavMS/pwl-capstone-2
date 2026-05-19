<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - AsetLab</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --surface-color: #1e293b;
            --primary-color: #4f46e5;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: rgba(255,255,255,0.1);
            --accent: @yield('accent-color', '#4f46e5');
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',sans-serif; }
        body { background-color:var(--bg-color); color:var(--text-primary); min-height:100vh; }

        .navbar {
            background-color: var(--surface-color);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }
        .navbar-brand { font-size:20px; font-weight:700; color:white; display:flex; align-items:center; gap:12px; }
        .user-info { display:flex; align-items:center; gap:20px; }
        .role-badge {
            background: rgba(79,70,229,0.2);
            color: #818cf8;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-logout {
            background: transparent; color:#ef4444;
            border: 1px solid rgba(239,68,68,0.3);
            padding: 8px 16px; border-radius: 8px;
            cursor: pointer; font-weight:500; transition:all 0.2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.1); }

        .container { padding: 40px; max-width:1200px; margin: 0 auto; }

        .welcome-card {
            background: linear-gradient(135deg, var(--surface-color), rgba(79,70,229,0.15));
            border: 1px solid var(--border-color);
            border-radius: 16px; padding: 40px; margin-bottom: 32px;
        }
        .welcome-card h1 { font-size:28px; margin-bottom:8px; }
        .welcome-card p { color:var(--text-secondary); font-size:15px; }

        .cards-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px,1fr)); gap:20px; }
        .card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 12px; padding: 24px;
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-4px); }
        .card-icon { font-size:32px; margin-bottom:12px; }
        .card h3 { font-size:15px; font-weight:600; margin-bottom:6px; }
        .card p { font-size:13px; color:var(--text-secondary); }

        .alert-success {
            background: rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3);
            color: #6ee7b7; padding:12px 20px; border-radius:8px; margin-bottom:24px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg>
            AsetLab
        </div>
        <div class="user-info">
            <span class="role-badge">{{ $user['role'] ?? 'User' }}</span>
            <span>{{ $user['name'] ?? 'User' }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="welcome-card">
            <h1>Selamat Datang, {{ $user['name'] ?? 'User' }}! 👋</h1>
            <p>Anda login sebagai <strong>{{ $user['role'] ?? 'N/A' }}</strong>.</p>
        </div>

        @yield('content')
    </div>
</body>
</html>
