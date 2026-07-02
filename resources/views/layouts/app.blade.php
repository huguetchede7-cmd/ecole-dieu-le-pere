<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — École Dieu le Père</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #1a73e8;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 16px;
            font-weight: 700;
        }

        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 4px;
        }

        .sidebar-menu {
            padding: 20px 0;
            flex: 1;
            overflow-y: auto;
        }

        .menu-label {
            font-size: 11px;
            opacity: 0.6;
            padding: 10px 20px 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-item {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.15);
        }

        .menu-item span {
            margin-right: 10px;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .logout-btn {
            display: block;
            text-align: center;
            padding: 10px;
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
        }

        .logout-btn:hover { background: rgba(255,255,255,0.25); }

        /* MAIN CONTENT */
        .main {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: white;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .topbar h1 {
            font-size: 18px;
            color: #333;
        }

        .topbar .user-info {
            font-size: 13px;
            color: #666;
        }

        .content {
            padding: 30px;
            flex: 1;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>🏫 École Dieu le Père</h2>
            <p>{{ session('utilisateur_role') }}</p>
        </div>

        <div class="sidebar-menu">
            @yield('menu')
        </div>

        <div class="sidebar-footer">
            <a href="/logout" class="logout-btn">🚪 Se déconnecter</a>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="topbar">
            <h1>@yield('page_title')</h1>
            <div class="user-info">
                👤 {{ session('utilisateur_nom') }}
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>

</body>
</html>