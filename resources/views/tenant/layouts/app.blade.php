<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RMSaaS - ' . (app('currentTenant')->name ?? 'Dashboard'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tenant-specific styles -->
    @stack('styles')
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }
        
        .tenant-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .tenant-brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .tenant-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
            margin-left: auto;
        }
        
        .tenant-nav a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .tenant-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .main-content {
            min-height: calc(100vh - 80px);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .tenant-footer {
            background: #374151;
            color: #9ca3af;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }
        
        /* Loading animations */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Tenant Header -->
    <header class="tenant-header">
        <div class="tenant-brand">
            <i class="fas fa-utensils"></i>
            {{ app('currentTenant')->name ?? 'RMSaaS' }}
        </div>
        
        <nav class="tenant-nav">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-home mr-2"></i>Dashboard
            </a>
            <a href="{{ route('imports.index') }}">
                <i class="fas fa-upload mr-2"></i>Imports
            </a>
            <a href="{{ route('analytics.losses') }}">
                <i class="fas fa-chart-line mr-2"></i>Analytics
            </a>
            <a href="{{ route('profile.edit') }}">
                <i class="fas fa-user mr-2"></i>Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: rgba(255, 255, 255, 0.9); font-weight: 500; cursor: pointer; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: all 0.2s ease;">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
            </form>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content fade-in">
        @yield('content')
    </main>

    <!-- Tenant Footer -->
    <footer class="tenant-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} RMSaaS - Revolutionary Restaurant Management System</p>
            <p style="margin-top: 0.5rem; font-size: 0.875rem;">
                Powered by AI • Built for {{ app('currentTenant')->name ?? 'Excellence' }}
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    @stack('scripts')
    
    <!-- Revolutionary UI Enhancement Scripts -->
    <script>
        // Smooth scrolling and enhanced UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to buttons
            document.querySelectorAll('button, .btn').forEach(button => {
                button.addEventListener('click', function() {
                    if (!button.disabled) {
                        const originalText = button.textContent;
                        button.style.opacity = '0.7';
                        button.style.transform = 'scale(0.98)';
                        
                        setTimeout(() => {
                            button.style.opacity = '1';
                            button.style.transform = 'scale(1)';
                        }, 150);
                    }
                });
            });
            
            // Enhanced form animations
            document.querySelectorAll('input, textarea, select').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.parentElement.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                    this.parentElement.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.05)';
                });
            });
        });
    </script>
</body>
</html>