<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Ebook Bücherei</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Gray/Orange Theme */
            --bg-color: #111827; /* Dark Gray */
            --glass-bg: rgba(31, 41, 55, 0.7); /* Lighter Gray Glass */
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary: #f97316; /* Vibrant Orange */
            --primary-hover: #ea580c; /* Darker Orange */
            --text-light: #f3f4f6; /* Off white */
            --text-muted: #9ca3af; /* Soft gray text */
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            background-image: radial-gradient(circle at top right, #1f2937, transparent 40%),
                              radial-gradient(circle at bottom left, #431407, transparent 40%);
            background-attachment: fixed;
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            top: 0;
            position: sticky;
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
        }

        nav .links a {
            color: var(--text-light);
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 600;
            transition: color 0.3s;
        }

        nav .links a:hover {
            color: var(--primary);
        }

        .btn {
            background: var(--primary);
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s, transform 0.2s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        main {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #f87171;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            nav .links {
                display: flex;
                width: 100%;
                justify-content: space-between;
                align-items: center;
            }
            
            nav .links a {
                margin-left: 0;
            }

            main {
                padding: 1rem;
            }
        }
    </style>
    <style>
        .footer {
            text-align: center;
            padding: 2rem;
            margin-top: auto;
            border-top: 1px solid var(--glass-border);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: var(--text-light);
        }
    </style>
</head>
<body>

<nav>
    <a href="{{ route('ebooks.index') }}" class="logo">
        📚 Ebook Bücherei
    </a>
    <div class="links">
        <a href="{{ route('ebooks.index') }}">Startseite</a>
        @auth
            <a href="{{ route('admin.index') }}" class="btn" style="background:var(--primary); margin-left:1rem; border:none;">Admin Panel</a>
            <a href="{{ route('ebooks.create') }}" class="btn" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.1); margin-left: 0.5rem;">+ Upload</a>
            
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn" style="background: transparent; border: 1px solid var(--glass-border); margin-left: 0.5rem;">Logout</button>
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
    &copy; {{ date('Y') }} Ebook Bücherei.
    @guest
        <a href="{{ route('login') }}" style="margin-left: 1rem;">Admin Bereich</a>
    @endguest
</div>

</body>
</html>
