<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Onboarding') - {{ config('app.name', 'RMSaaS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Base Styles -->
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                min-height: 100vh;
                line-height: 1.6;
                color: #1e293b;
            }
            
            .onboarding-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            .onboarding-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 1rem 0;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .header-content {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .logo {
                font-size: 1.5rem;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .onboarding-main {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }
            
            .onboarding-content {
                width: 100%;
                max-width: 1000px;
            }
            
            /* Utility classes */
            .mr-1 { margin-right: 0.25rem; }
            .mr-2 { margin-right: 0.5rem; }
            .ml-1 { margin-left: 0.25rem; }
            .ml-2 { margin-left: 0.5rem; }
            .text-center { text-align: center; }
            .hidden { display: none; }
        </style>

        @stack('styles')
    </head>
    <body>
        <div class="onboarding-container">
            <!-- Clean Onboarding Header -->
            <header class="onboarding-header">
                <div class="header-content">
                    <div class="logo">
                        <i class="fas fa-utensils"></i>
                        RMSaaS
                    </div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">
                        Restaurant Management System
                    </div>
                </div>
            </header>

            <!-- Main Onboarding Content -->
            <main class="onboarding-main">
                <div class="onboarding-content">
                    @yield('content')
                </div>
            </main>
        </div>

        @stack('scripts')
    </body>
</html>