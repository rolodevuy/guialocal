<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del servidor — Guía Local</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: #fafaf9;
            color: #1c1917;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .wrap { max-width: 28rem; text-align: center; }
        .code {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            color: #fcd34d;
            letter-spacing: -0.05em;
            margin-bottom: 1.5rem;
        }
        h1 { font-size: 1.375rem; font-weight: 700; color: #1c1917; margin-bottom: 0.75rem; }
        p { color: #78716c; font-size: 0.9375rem; line-height: 1.6; margin-bottom: 2rem; }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.75rem;
            background: #f59e0b;
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn:hover { background: #d97706; }
        .footer {
            margin-top: 3rem;
            font-size: 0.8125rem;
            color: #a8a29e;
        }
        .footer a { color: #f59e0b; text-decoration: none; font-weight: 600; }
        .divider {
            width: 3rem;
            height: 3px;
            background: #fcd34d;
            border-radius: 9999px;
            margin: 1.5rem auto;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="code">500</div>
        <div class="divider"></div>
        <h1>Algo salió mal de nuestro lado</h1>
        <p>
            El servidor tuvo un problema. Ya estamos al tanto y lo vamos a solucionar.<br>
            Intentá de nuevo en unos minutos.
        </p>
        <a href="/" class="btn">Volver al inicio</a>
        <p class="footer">
            <a href="/">Guía Local</a> — Tu barrio en un solo lugar
        </p>
    </div>
</body>
</html>
