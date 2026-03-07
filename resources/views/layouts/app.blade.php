<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Ebook Bücherei</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Gray/Orange Theme - Modernized */
            --bg-color: #0f172a; /* Slate 900 - Deep Gray */
            --glass-bg: rgba(30, 41, 59, 0.75); /* Slate 800 with opacity */
            --glass-border: rgba(255, 255, 255, 0.08);
            --primary: #f97316; /* Vibrant Orange */
            --primary-hover: #ea580c; /* Darker Orange */
            --primary-gradient: linear-gradient(135deg, #f97316, #ea580c);
            --text-light: #f8fafc; /* Slate 50 */
            --text-muted: #94a3b8; /* Slate 400 */
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            /* Richer, deeper gradient background */
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(249, 115, 22, 0.08), transparent 50%),
                radial-gradient(circle at 85% 30%, rgba(30, 41, 59, 0.8), transparent 50%);
            background-attachment: fixed;
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Glassmorphism Utilities */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        /* Navigation */
        nav {
            background: rgba(15, 23, 42, 0.85); /* Slightly darker nav */
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        nav .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-light);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(249, 115, 22, 0.2);
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
        }

        .menu-toggle span {
            width: 25px;
            height: 3px;
            background-color: var(--text-light);
            border-radius: 3px;
            transition: all 0.3s;
        }

        nav .links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        nav .links a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.3s, text-shadow 0.3s;
        }

        nav .links a:not(.btn):hover {
            color: var(--primary);
            text-shadow: 0 0 8px rgba(249, 115, 22, 0.4);
        }

        /* Buttons */
        .btn {
            background: var(--primary-gradient);
            color: #fff !important;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px 0 rgba(249, 115, 22, 0.39);
            -webkit-text-fill-color: initial; /* Override logo gradient if reused */
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.5);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--glass-border);
            box-shadow: none;
            color: var(--text-light) !important;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.2);
        }

        main {
            flex: 1;
            padding: 2.5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            backdrop-filter: blur(8px);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            margin-top: auto;
            border-top: 1px solid var(--glass-border);
            color: var(--text-muted);
            font-size: 0.9rem;
            background: rgba(15, 23, 42, 0.4);
        }

        .footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: var(--primary);
        }

        /* Form Elements (Global) */
        input.form-control, select.form-control, textarea.form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            background: rgba(15, 23, 42, 0.6);
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }

        input.form-control:focus, select.form-control:focus, textarea.form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            nav {
                padding: 1rem;
            }

            .menu-toggle {
                display: flex;
            }

            nav .links {
                display: none; /* Hidden by default on mobile */
                width: 100%;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                background: rgba(15, 23, 42, 0.95);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid var(--glass-border);
                padding: 1.5rem 1rem;
                gap: 1rem;
                box-sizing: border-box;
            }

            nav .links.active {
                display: flex;
            }

            nav .links a, nav .links form, nav .links span {
                width: 100%;
                text-align: center;
                justify-content: center;
                margin: 0 !important; /* Override inline margins */
            }

            nav .links form button {
                width: 100%;
            }

            main {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>

<nav>
    <a href="{{ route('ebooks.index') }}" class="logo">
        <span style="-webkit-text-fill-color: initial;">📚</span> Die Bücherei
    </a>
    
    <button class="menu-toggle" onclick="document.querySelector('.links').classList.toggle('active')">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="links">
        <a href="{{ route('ebooks.index') }}">Startseite</a>
        
        @guest
            <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
            <a href="{{ route('register') }}" class="btn">Registrieren</a>
        @else
            <a href="{{ route('generate.index') }}" style="color: var(--primary); font-weight: 600;">✨ KI-Buch generieren</a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.index') }}" class="btn">Admin Panel</a>
                <a href="{{ route('ebooks.create') }}" class="btn btn-outline">+ Upload</a>
            @else
                <span style="color:var(--text-muted); font-weight:600;">Hallo, {{ auth()->user()->name }}</span>
            @endif
            
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline">Logout</button>
            </form>
        @endauth
    </div>
</nav>

<main>
    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

<div class="footer">
    &copy; {{ date('Y') }} Sebastian Thielke
    @guest
        <br><br><a href="{{ route('login') }}">Admin Bereich</a>
    @endguest
</div>

</body>
</html>
