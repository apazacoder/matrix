<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Página expirada</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Roboto', sans-serif;
            font-weight: 300;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .code {
            border-right: 2px solid;
            font-size: 2rem;
            padding: 0 15px 0 15px;
            text-align: center;
        }

        .message {
            font-size: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="code">
        419
    </div>

    <div class="message" style="padding: 10px;">
        <p>Su sesión ha expirado, redireccionando al inicio...</p>
    </div>
    <a href="{{route('notfound')}}" id="goBack" style="visibility:hidden;">Volver</a>
    <script>
        setTimeout( function(){
            document.getElementById("goBack").click();
        }, 1000);
        // window.location.href="/";
    </script>
</div>
</body>
</html>
