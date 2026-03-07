<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Mail Bestätigung</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #0f172a;
            padding: 40px 0;
        }
        .main {
            background-color: #1e293b;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            border: 1px solid #334155;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            overflow: hidden;
            border-collapse: collapse;
        }
        .header {
            background: linear-gradient(135deg, #ea580c, #f97316);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 26px;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .content {
            padding: 40px 30px;
            color: #e2e8f0;
            font-size: 16px;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 15px 0;
        }
        .button-wrap {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.4);
            border: 1px solid #ea580c;
        }
        .footer {
            background-color: #0f172a;
            padding: 25px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            border-top: 1px solid #334155;
        }
        .footer a {
            color: #ea580c;
            text-decoration: none;
        }
        .fallback-link {
            font-size: 11px;
            margin-top: 15px;
            display: block;
            color: #475569;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header">
                    <h1>Die Bücherei</h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    @foreach($lines as $line)
                        @if(trim($line) === '{verification_button}')
                            <div class="button-wrap">
                                <a href="{{ $verificationUrl }}" class="button">E-Mail Adresse bestätigen</a>
                            </div>
                        @elseif(trim($line) !== '')
                            <p>{{ trim($line) }}</p>
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="footer">
                    &copy; {{ date('Y') }} Die Bücherei. Alle Rechte vorbehalten.<br>
                    <span class="fallback-link">
                        Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:<br><br>
                        <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
                    </span>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
