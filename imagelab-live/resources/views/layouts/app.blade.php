<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#09090b">
    <meta name="description" content="ImageLab Live — Digital Image Processing Laboratory. Explore real-time image processing, color models, filters, edge detection, and image analysis.">

    {{-- ImageLab Live favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <title>@yield('title', 'ImageLab Live - Digital Image Processing Laboratory')</title>

    {{-- Tailwind CSS CDN: sesuai dengan setup awal project ImageLab Live. --}}
    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
])

    <style>
        :root {
            color-scheme: dark;
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            background: #09090b;
            color: #f4f4f5;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .imagelab-nav-link {
            transition: color .2s ease, background-color .2s ease, border-color .2s ease;
        }

        .imagelab-nav-link:focus-visible,
        .imagelab-mobile-link:focus-visible,
        .imagelab-menu-toggle:focus-visible {
            outline: 2px solid #60a5fa;
            outline-offset: 3px;
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="flex min-h-screen flex-col bg-zinc-950 text-zinc-100 antialiased">
    @php
        $currentPath = trim(request()->path(), '/');
        $isHome = $currentPath === '';
        $isStudio = request()->is('studio');
        $isAbout = request()->is('about');

        $desktopActive = 'border border-white/10 bg-white/[0.08] text-white';
        $desktopInactive = 'border border-transparent text-zinc-400 hover:bg-white/[0.05] hover:text-white';

        $mobileActive = 'border border-blue-500/20 bg-blue-500/10 text-blue-300';
        $mobileInactive = 'border border-transparent text-zinc-300 hover:border-zinc-800 hover:bg-zinc-800/70 hover:text-white';
    @endphp

    {{-- NAVBAR --}}
    <header class="sticky top-0 z-50 border-b border-white/[0.08] bg-zinc-950/90 backdrop-blur-xl">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <div class="flex h-[72px] items-center justify-between gap-4">
                {{-- BRAND --}}
                <a href="{{ url('/') }}" class="group flex min-w-0 items-center gap-3" aria-label="ImageLab Live — Home">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-blue-400/20 bg-gradient-to-br from-blue-500/20 via-cyan-500/10 to-violet-500/20 shadow-lg shadow-blue-950/30 transition duration-300 group-hover:border-blue-400/40">
                        <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="5.5" y="7.5" width="29" height="25" rx="4.5" stroke="#60A5FA" stroke-width="1.5"/>
                            <rect x="10" y="12" width="6" height="6" rx="1" fill="#3B82F6"/>
                            <rect x="18" y="12" width="6" height="6" rx="1" fill="#06B6D4"/>
                            <rect x="26" y="12" width="4" height="6" rx="1" fill="#8B5CF6"/>
                            <rect x="10" y="21" width="6" height="6" rx="1" fill="#06B6D4" opacity=".8"/>
                            <rect x="18" y="21" width="6" height="6" rx="1" fill="#8B5CF6" opacity=".9"/>
                            <rect x="26" y="21" width="4" height="6" rx="1" fill="#3B82F6" opacity=".8"/>
                        </svg>
                    </span>

                    <span class="min-w-0 leading-none">
                        <span class="flex items-center gap-1.5 whitespace-nowrap text-[17px] font-bold tracking-tight text-white sm:text-lg">
                            ImageLab
                            <span class="bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">Live</span>
                        </span>
                        <span class="mt-1.5 block whitespace-nowrap text-[8px] font-semibold uppercase tracking-[.19em] text-zinc-500 sm:text-[9px]">
                            Digital Image Processing
                        </span>
                    </span>
                </a>

                {{-- DESKTOP NAVIGATION --}}
                <div class="hidden items-center gap-1 rounded-xl border border-white/[0.06] bg-zinc-900/70 p-1.5 md:flex">
                    <a href="{{ url('/') }}"
                       @if($isHome) aria-current="page" @endif
                       class="imagelab-nav-link rounded-lg px-4 py-2.5 text-sm font-medium {{ $isHome ? $desktopActive : $desktopInactive }}">
                        Home
                    </a>

                    <a href="{{ url('/studio') }}"
                       @if($isStudio) aria-current="page" @endif
                       class="imagelab-nav-link rounded-lg px-4 py-2.5 text-sm font-medium {{ $isStudio ? $desktopActive : $desktopInactive }}">
                        Studio
                        <span class="ml-1.5 inline-block h-1.5 w-1.5 rounded-full bg-cyan-400 align-middle"></span>
                    </a>

                    <a href="{{ url('/about') }}"
                       @if($isAbout) aria-current="page" @endif
                       class="imagelab-nav-link rounded-lg px-4 py-2.5 text-sm font-medium {{ $isAbout ? $desktopActive : $desktopInactive }}">
                        About
                    </a>
                </div>

                {{-- DESKTOP ACTION --}}
                <div class="hidden items-center gap-4 md:flex">
                    <span class="hidden items-center gap-2 text-xs text-zinc-500 lg:inline-flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Browser-based laboratory
                    </span>

                    <a href="{{ url('/studio') }}"
                       class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition duration-200 hover:-translate-y-0.5 hover:from-blue-500 hover:to-cyan-500 hover:shadow-blue-900/40 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-zinc-950">
                        Launch Studio
                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </a>
                </div>

                {{-- MOBILE MENU BUTTON --}}
                <button id="imagelabMenuToggle"
                        type="button"
                        class="imagelab-menu-toggle inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-zinc-800 bg-zinc-900/70 text-zinc-300 transition hover:border-zinc-700 hover:bg-zinc-800 hover:text-white md:hidden"
                        aria-label="Buka menu navigasi"
                        aria-expanded="false"
                        aria-controls="imagelabMobileMenu">
                    <svg id="imagelabMenuOpenIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                    <svg id="imagelabMenuCloseIcon" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="m18 6-12 12M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- MOBILE NAVIGATION --}}
            <div id="imagelabMobileMenu" class="hidden border-t border-white/[0.06] pb-4 pt-3 md:hidden">
                <div class="space-y-1 rounded-xl border border-white/[0.06] bg-zinc-900/80 p-2">
                    <a href="{{ url('/') }}"
                       @if($isHome) aria-current="page" @endif
                       class="imagelab-mobile-link block rounded-lg px-4 py-3 text-sm font-medium {{ $isHome ? $mobileActive : $mobileInactive }}">
                        Home
                    </a>
                    <a href="{{ url('/studio') }}"
                       @if($isStudio) aria-current="page" @endif
                       class="imagelab-mobile-link block rounded-lg px-4 py-3 text-sm font-medium {{ $isStudio ? $mobileActive : $mobileInactive }}">
                        Processing Studio
                    </a>
                    <a href="{{ url('/about') }}"
                       @if($isAbout) aria-current="page" @endif
                       class="imagelab-mobile-link block rounded-lg px-4 py-3 text-sm font-medium {{ $isAbout ? $mobileActive : $mobileInactive }}">
                        About Platform
                    </a>

                    <div class="border-t border-zinc-800 px-2 pt-3">
                        <a href="{{ url('/studio') }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-3 text-sm font-semibold text-white transition hover:from-blue-500 hover:to-cyan-500">
                            Launch Studio
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-white/[0.07] bg-zinc-950">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-7 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold tracking-tight text-zinc-200">
                    ImageLab <span class="text-cyan-400">Live</span>
                </a>
                <p class="mt-1 text-xs text-zinc-600">Digital Image Processing Laboratory</p>
            </div>

            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-zinc-500">
                <a href="{{ url('/') }}" class="transition hover:text-zinc-200">Home</a>
                <a href="{{ url('/studio') }}" class="transition hover:text-zinc-200">Studio</a>
                <a href="{{ url('/about') }}" class="transition hover:text-zinc-200">About</a>
                <span class="text-zinc-700">© {{ date('Y') }} ImageLab Live</span>
            </div>
        </div>
    </footer>

    {{-- MOBILE NAVIGATION SCRIPT --}}
    <script>
        (function () {
            const toggle = document.getElementById('imagelabMenuToggle');
            const menu = document.getElementById('imagelabMobileMenu');
            const openIcon = document.getElementById('imagelabMenuOpenIcon');
            const closeIcon = document.getElementById('imagelabMenuCloseIcon');

            if (!toggle || !menu) return;

            function setMenuOpen(isOpen) {
                menu.classList.toggle('hidden', !isOpen);
                toggle.setAttribute('aria-expanded', String(isOpen));
                toggle.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
                if (openIcon) openIcon.classList.toggle('hidden', isOpen);
                if (closeIcon) closeIcon.classList.toggle('hidden', !isOpen);
            }

            toggle.addEventListener('click', function () {
                const currentlyOpen = toggle.getAttribute('aria-expanded') === 'true';
                setMenuOpen(!currentlyOpen);
            });

            menu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    setMenuOpen(false);
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') setMenuOpen(false);
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) setMenuOpen(false);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
