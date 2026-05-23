<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Open Graph Meta Tags for Social Media Sharing --}}
    <meta property="og:title" content="শপনোপুরণ - সঞ্চয় ও বিনিয়োগ সংগঠন">
    <meta property="og:description" content="শপনোপুরণ একটি সদস্য-ভিত্তিক সঞ্চয় ও বিনিয়োগ সংগঠন। পারস্পরিক সহযোগিতা ও আস্থার ভিত্তিতে প্রতিটি সদস্যের আর্থিক স্বাধীনতা নিশ্চিত করতে প্রতিশ্রুতিবদ্ধ।">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:site_name" content="শপনোপুরণ">
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="শপনোপুরণ - সঞ্চয় ও বিনিয়োগ সংগঠন">
    <meta name="twitter:description" content="শপনোপুরণ একটি সদস্য-ভিত্তিক সঞ্চয় ও বিনিয়োগ সংগঠন।">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">
    
    {{-- Facebook Domain Verification --}}
    <meta property="fb:app_id" content="">
    
    {{-- WhatsApp & General Social Meta Tags --}}
    <meta name="description" content="শপনোপুরণ একটি সদস্য-ভিত্তিক সঞ্চয় ও বিনিয়োগ সংগঠন। পারস্পরিক সহযোগিতা ও আস্থার ভিত্তিতে প্রতিটি সদস্যের আর্থিক স্বাধীনতা নিশ্চিত করতে প্রতিশ্রুতিবদ্ধ।">
    <meta name="keywords" content="শপনোপুরণ, সঞ্চয়, বিনিয়োগ, সংগঠন, সদস্য, আর্থিক">
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    {{-- Link --}}
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.css') }}">
    
    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    {{-- inertia Head --}}
    @inertiaHead
    
    {{-- Initialize Theme Manager --}}
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overflow-y: auto !important;
            height: auto !important;
            min-height: 100vh;
            display: block;
        }
        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
    </style>
</head>

<body>

    @inertia

    {{-- plugins --}}
    <script src="{{ asset('plugins/js/jquery.min.js') }}"></script>
    {{-- script --}}
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    {{-- resource & vite --}}
    @vite(['resources/js/frontend/app.js'])
</body>

</html>
