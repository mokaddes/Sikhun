<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="fb:app_id" content="1562071828675426">

    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1562071828675426');
    fbq('track', 'PageView');
    </script>
    <!-- End Meta Pixel Code -->

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-W8R245XB');</script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-102CKE347K"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-102CKE347K');
    </script>

    <!--
        Inline theme script — runs before Vue mounts to prevent a flash
        of the wrong theme (light) when the student has dark mode saved.
    -->
    <script>
        (function () {
            var mode = localStorage.getItem('sikhun_theme') || 'dark';
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (mode === 'dark' || (mode === 'system' && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @php
        $seoService = app(\App\Services\SeoService::class);
        $seoData = $seoService->resolveForRequest(request(), $page['props']['seo'] ?? null);
        $siteFavicon = $page['props']['site']['favicon_url'] ?? asset('favicon.svg');
    @endphp

    <title inertia>{{ $seoData['title'] }}</title>
    <meta name="description" content="{{ $seoData['description'] }}">
    @if(!empty($seoData['keywords']))
    <meta name="keywords" content="{{ $seoData['keywords'] }}">
    @endif
    <link rel="canonical" href="{{ $seoData['canonical'] }}">
    <meta name="robots" content="{{ $seoData['robots'] ?? 'index, follow' }}">

    <link rel="icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" href="{{ $siteFavicon }}">
    <link rel="apple-touch-icon" href="{{ $siteFavicon }}">

    <!-- OpenGraph & Twitter for Crawlers, AI Search Bots & Social Debuggers -->
    <meta property="og:site_name" content="{{ $seoData['site_name'] }}">
    <meta property="og:type" content="{{ $seoData['og_type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $seoData['title'] }}">
    <meta property="og:description" content="{{ $seoData['description'] }}">
    <meta property="og:url" content="{{ $seoData['canonical'] }}">
    @if(!empty($seoData['og_image']))
    <meta property="og:image" content="{{ $seoData['og_image'] }}">
    <meta property="og:image:alt" content="{{ $seoData['title'] }}">
    @endif
    <meta property="og:locale" content="{{ app()->getLocale() === 'bn' ? 'bn_BD' : 'en_US' }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@sikhun_com">
    <meta name="twitter:title" content="{{ $seoData['title'] }}">
    <meta name="twitter:description" content="{{ $seoData['description'] }}">
    @if(!empty($seoData['og_image']))
    <meta name="twitter:image" content="{{ $seoData['og_image'] }}">
    @endif

    <!-- Bilingual alternate tags -->
    <link rel="alternate" hreflang="bn" href="{{ $seoData['canonical'] }}">
    <link rel="alternate" hreflang="en" href="{{ $seoData['canonical'] }}">
    <link rel="alternate" hreflang="x-default" href="{{ $seoData['canonical'] }}">

    <!-- Structured Data (JSON-LD) for Search & GEO Engine Citations -->
    @if(!empty($seoData['json_ld']))
    <script type="application/ld+json">
    {!! json_encode($seoData['json_ld'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif
    @if(!empty($seoData['breadcrumb']))
    <script type="application/ld+json">
    {!! json_encode($seoData['breadcrumb'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Hind+Siliguri:wght@300;400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    <!-- Meta Pixel Code (noscript) -->
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1562071828675426&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code (noscript) -->
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W8R245XB"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @inertia
</body>
</html>
