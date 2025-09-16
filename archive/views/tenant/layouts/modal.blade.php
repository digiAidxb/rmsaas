<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Modal') - {{ config('app.name', 'RMSaaS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Enterprise Modal Styles - Salesforce Inspired -->
        <style>
            :root {
                /* Salesforce Design System Colors */
                --slds-color-brand: #1589ee;
                --slds-color-brand-dark: #0070d2;
                --slds-color-success: #04844b;
                --slds-color-warning: #fe9339;
                --slds-color-error: #ea001e;
                --slds-color-neutral-1: #ffffff;
                --slds-color-neutral-2: #f3f3f3;
                --slds-color-neutral-3: #dddbda;
                --slds-color-neutral-4: #c9c7c5;
                --slds-color-neutral-8: #514f4d;
                --slds-color-neutral-9: #3e3e3c;
                --slds-color-neutral-10: #181818;
                
                /* Enterprise Typography */
                --slds-font-family: 'Salesforce Sans', Arial, sans-serif;
                --slds-font-size-1: 0.625rem;   /* 10px */
                --slds-font-size-2: 0.75rem;    /* 12px */
                --slds-font-size-3: 0.8125rem;  /* 13px */
                --slds-font-size-4: 0.875rem;   /* 14px */
                --slds-font-size-5: 1rem;       /* 16px */
                --slds-font-size-6: 1.125rem;   /* 18px */
                --slds-font-size-7: 1.25rem;    /* 20px */
                --slds-font-size-8: 1.5rem;     /* 24px */
                
                /* Spacing Scale */
                --slds-spacing-xxx-small: 0.125rem; /* 2px */
                --slds-spacing-xx-small: 0.25rem;   /* 4px */
                --slds-spacing-x-small: 0.5rem;     /* 8px */
                --slds-spacing-small: 0.75rem;      /* 12px */
                --slds-spacing-medium: 1rem;        /* 16px */
                --slds-spacing-large: 1.5rem;       /* 24px */
                --slds-spacing-x-large: 2rem;       /* 32px */
                --slds-spacing-xx-large: 3rem;      /* 48px */
                
                /* Enterprise Shadows */
                --slds-shadow-1: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --slds-shadow-2: 0 2px 4px 0 rgba(0, 0, 0, 0.07);
                --slds-shadow-3: 0 4px 8px 0 rgba(0, 0, 0, 0.08);
                --slds-shadow-4: 0 8px 16px 0 rgba(0, 0, 0, 0.1);
                --slds-shadow-5: 0 16px 32px 0 rgba(0, 0, 0, 0.15);
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: var(--slds-font-family);
                background: rgba(0, 0, 0, 0.5);
                line-height: 1.5;
                color: var(--slds-color-neutral-10);
                overflow: hidden;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                font-size: var(--slds-font-size-4);
            }
            
            /* Non-dismissible Modal Backdrop */
            .modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                /* NO pointer-events: none - prevents clicking outside */
            }
            
            .modal-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: var(--slds-spacing-medium);
                z-index: 1001;
                overflow-y: auto;
            }
            
            .modal-content {
                width: 100%;
                max-width: 72rem; /* 1152px */
                max-height: 90vh;
                background: var(--slds-color-neutral-1);
                border-radius: 0.375rem; /* Salesforce border radius */
                box-shadow: var(--slds-shadow-5);
                border: 1px solid var(--slds-color-neutral-3);
                overflow: hidden;
                position: relative;
                z-index: 1002;
            }
            
            /* Prevent accidental navigation */
            .modal-content::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                pointer-events: auto;
                z-index: -1;
            }
            
            /* Enterprise Utility Classes */
            .slds-text-heading_large {
                font-size: var(--slds-font-size-8);
                font-weight: 700;
                line-height: 1.25;
            }
            
            .slds-text-heading_medium {
                font-size: var(--slds-font-size-7);
                font-weight: 700;
                line-height: 1.25;
            }
            
            .slds-text-body_regular {
                font-size: var(--slds-font-size-4);
                font-weight: 400;
                line-height: 1.5;
            }
            
            .slds-text-color_default {
                color: var(--slds-color-neutral-10);
            }
            
            .slds-text-color_weak {
                color: var(--slds-color-neutral-8);
            }
            
            .slds-m-around_none { margin: 0; }
            .slds-m-around_small { margin: var(--slds-spacing-small); }
            .slds-m-around_medium { margin: var(--slds-spacing-medium); }
            .slds-m-around_large { margin: var(--slds-spacing-large); }
            
            .slds-p-around_none { padding: 0; }
            .slds-p-around_small { padding: var(--slds-spacing-small); }
            .slds-p-around_medium { padding: var(--slds-spacing-medium); }
            .slds-p-around_large { padding: var(--slds-spacing-large); }
            .slds-p-around_x-large { padding: var(--slds-spacing-x-large); }
            
            .slds-text-align_center { text-align: center; }
            .slds-hide { display: none; }
            .slds-show { display: block; }
            
            /* Legacy support for existing classes */
            .mr-1 { margin-right: var(--slds-spacing-xx-small); }
            .mr-2 { margin-right: var(--slds-spacing-x-small); }
            .ml-1 { margin-left: var(--slds-spacing-xx-small); }
            .ml-2 { margin-left: var(--slds-spacing-x-small); }
            .text-center { text-align: center; }
            .hidden { display: none; }
        </style>
        
        <script>
            // Enterprise Modal Protection
            document.addEventListener('DOMContentLoaded', function() {
                // Prevent all forms of modal dismissal
                
                // 1. Prevent ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' || e.keyCode === 27) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
                
                // 2. Prevent clicking outside modal
                const backdrop = document.getElementById('modalBackdrop');
                if (backdrop) {
                    backdrop.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                }
                
                // 3. Prevent browser back/forward
                if (window.history && window.history.pushState) {
                    window.history.pushState('forward', null, './');
                    window.addEventListener('popstate', function() {
                        window.history.pushState('forward', null, './');
                    });
                }
                
                // 4. Prevent page refresh
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R' || e.keyCode === 116)) {
                        e.preventDefault();
                        return false;
                    }
                    if (e.key === 'F5' || e.keyCode === 116) {
                        e.preventDefault();
                        return false;
                    }
                });
                
                // 5. Prevent right-click
                document.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });
                
                console.log('🛡️ Enterprise Onboarding Modal Protected - Blessed by Lord Bhairava');
            });
        </script>

        @stack('styles')
    </head>
    <body>
        <!-- Onboarding Modal Backdrop - Cannot be dismissed -->
        <div class="modal-backdrop" id="modalBackdrop"></div>
        
        <div class="modal-container" id="modalContainer">
            <div class="modal-content" id="modalContent">
                @yield('content')
            </div>
        </div>

        @stack('scripts')
    </body>
</html>