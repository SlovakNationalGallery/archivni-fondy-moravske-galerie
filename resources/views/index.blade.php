<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <title>Archivní fondy Moravské galerie</title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6C01HV73ET"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-6C01HV73ET');
    </script>
</head>

<body class="antialias bg-white font-sans text-base text-neutral-800">
    <noscript>
        <strong>We're sorry but this site doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
    </noscript>
    <div id="app"></div>
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="https://kit.fontawesome.com/4fb26d6f5e.js" crossorigin="anonymous"></script>
</body>

</html>
