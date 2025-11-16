<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Contacts')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            max-width: 1000px;
            margin: 0 auto;
            padding: 16px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f5f5;
            color: #222222;
        }
        .card {
            background: #ffffff;
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        h1, h2, h3 {
            margin-top: 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .space {
            margin-bottom: 16px;
        }
        .btn {
            padding: 6px 10px;
            cursor: pointer;
            border-radius: 4px;
            border: none;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #2f6fed;
            color: #ffffff;
        }
        .btn-secondary {
            background: #7f8c8d;
            color: #ffffff;
        }
        .btn-danger {
            background: #c0392b;
            color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            text-align: left;
            vertical-align: top;
        }
        input[type="text"], input[type="email"], textarea, select {
            width: 100%;
            padding: 4px 6px;
            box-sizing: border-box;
        }
        .error {
            color: #c0392b;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            background: #ecf0f1;
            font-size: 12px;
        }
        .flash {
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            background: #e0f7e9;
            color: #1b5e20;
            font-size: 14px;
        }
        .pagination {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="card">
    @if (session('status'))
        <div class="flash">
            {{ session('status') }}
        </div>
    @endif

    @yield('content')
</div>

@yield('scripts')
</body>
</html>
