@extends('layouts.app')

@section('title', 'ImageLab Live - Digital Image Processing Studio')

@push('styles')
<style>
    .home-grid-bg {
        background-image:
            linear-gradient(rgba(113, 113, 122, .13) 1px, transparent 1px),
            linear-gradient(90deg, rgba(113, 113, 122, .13) 1px, transparent 1px);
        background-size: 38px 38px;
        mask-image: linear-gradient(to bottom, black 0%, transparent 92%);
        -webkit-mask-image: linear-gradient(to bottom, black 0%, transparent 92%);
    }
    .home-glow {
        box-shadow: 0 0 90px rgba(37, 99, 235, .10), 0 24px 80px rgba(0, 0, 0, .28);
    }
    .home-card {
        transition: transform .22s ease, border-color .22s ease, background-color .22s ease;
    }
    .home-card:hover {
        transform: translateY(-4px);
        border-color: rgba(96, 165, 250, .38);
        background-color: rgba(24, 24, 27, .92);
    }
    .home-float { animation: home-float 6s ease-in-out infinite; }
    .home-float-delay { animation: home-float 7s ease-in-out 1.2s infinite; }
    .home-scan { animation: home-scan 4.8s ease-in-out infinite; }
    .home-bar { transform-origin: bottom; animation: home-bars 2.8s ease-in-out infinite alternate; }
    .home-bar:nth-child(2n) { animation-delay: -.8s; }
    .home-bar:nth-child(3n) { animation-delay: -1.4s; }
    .home-bar:nth-child(4n) { animation-delay: -.4s; }
    .home-anchor { scroll-margin-top: 6rem; }
    @keyframes home-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-9px); }
    }
    @keyframes home-scan {
        0%, 100% { transform: translateY(-105px); opacity: .25; }
        50% { transform: translateY(105px); opacity: .9; }
    }
    @keyframes home-bars {
        from { transform: scaleY(.72); opacity: .65; }
        to { transform: scaleY(1); opacity: 1; }
    }
    @media (prefers-reduced-motion: reduce) {
        .home-float, .home-float-delay, .home-scan, .home-bar { animation: none !important; }
        .home-card { transition: none; }
        .home-card:hover { transform: none; }
    }
</style>
@endpush

@section('content')
<div class="relative isolate overflow-hidden bg-zinc-950 text-zinc-100">
    <!-- Background atmosphere -->
    <div class="home-grid-bg pointer-events-none absolute inset-x-0 top-0 -z-10 h-[760px] opacity-70"></div>
    <div class="pointer-events-none absolute -top-40 right-[-8rem] -z-10 h-[34rem] w-[34rem] rounded-full bg-blue-600/10 blur-3xl"></div>
    <div class="pointer-events-none absolute left-[-12rem] top-[34rem] -z-10 h-[28rem] w-[28rem] rounded-full bg-violet-600/[0.08] blur-3xl"></div>

    <main class="mx-auto max-w-7xl px-4 pb-20 pt-10 sm:px-6 sm:pt-14 lg:px-8 lg:pb-28 lg:pt-20">
        <!-- HERO -->
        <section class="relative grid items-center gap-14 lg:grid-cols-[.94fr_1.06fr] lg:gap-10">
            <div class="relative z-10 max-w-2xl">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/[0.08] px-3.5 py-2 text-[10px] font-semibold uppercase tracking-[.2em] text-blue-300 sm:text-[11px]">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-400 opacity-50"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-cyan-300"></span>
                    </span>
                    Digital Image Processing Laboratory
                </div>

                <h1 class="max-w-2xl text-[2.7rem] font-semibold leading-[1.08] tracking-[-.045em] text-white sm:text-5xl lg:text-[4.15rem]">
                    See beyond the image.<br>
                    <span class="bg-gradient-to-r from-blue-400 via-cyan-300 to-violet-400 bg-clip-text text-transparent">Understand every pixel.</span>
                </h1>

                <p class="mt-6 max-w-xl text-sm leading-7 text-zinc-400 sm:text-base sm:leading-8">
                    Jelajahi pengolahan citra digital melalui kamera secara real-time. Uji model warna, terapkan algoritma pemrosesan, amati histogram, dan pahami karakteristik setiap piksel dalam satu studio interaktif.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ url('/studio') }}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/40 transition hover:-translate-y-0.5 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-zinc-950">
                        Explore Processing Studio
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ url('/about') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-900/50 px-6 py-3.5 text-sm font-semibold text-zinc-300 transition hover:border-zinc-500 hover:bg-zinc-800/80 hover:text-white">
                        Discover the platform
                    </a>
                </div>

                <div class="mt-9 flex flex-wrap items-center gap-x-5 gap-y-3 border-t border-zinc-800/80 pt-6 text-xs text-zinc-500">
                    <span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Browser-based processing</span>
                    <span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span> OpenCV.js powered</span>
                    <span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span> Interactive analysis</span>
                </div>
            </div>

            <!-- HERO VISUAL: inline animated SVG illustration -->
            <div class="relative mx-auto w-full max-w-[660px] lg:ml-auto">
                <div class="absolute inset-8 rounded-full bg-blue-500/10 blur-[80px]"></div>
                <div class="relative home-glow rounded-[1.7rem] border border-zinc-700/80 bg-zinc-900/75 p-3 backdrop-blur sm:p-4">
                    <div class="flex items-center justify-between border-b border-zinc-800 px-2 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="flex gap-1.5"><i class="h-2 w-2 rounded-full bg-red-400/80"></i><i class="h-2 w-2 rounded-full bg-amber-300/80"></i><i class="h-2 w-2 rounded-full bg-emerald-400/80"></i></span>
                            <span class="ml-2 text-[10px] font-semibold uppercase tracking-[.18em] text-zinc-500">ImageLab / Live Workspace</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[9px] font-semibold tracking-wider text-emerald-300"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> ANALYZING</span>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl bg-[#090e1a] p-3 sm:p-5">
                        <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image:linear-gradient(rgba(100,116,139,.18) 1px,transparent 1px),linear-gradient(90deg,rgba(100,116,139,.18) 1px,transparent 1px);background-size:24px 24px"></div>
                        <svg viewBox="0 0 620 390" class="relative z-10 block h-auto w-full" role="img" aria-labelledby="home-visual-title home-visual-desc">
                            <title id="home-visual-title">ImageLab Live image processing visualization</title>
                            <desc id="home-visual-desc">A stylized image frame with pixel grid, color channels, scanning line, and histogram representing digital image analysis.</desc>
                            <defs>
                                <linearGradient id="home-frame-grad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#172554"/><stop offset=".55" stop-color="#10253a"/><stop offset="1" stop-color="#312e81"/></linearGradient>
                                <linearGradient id="home-scan-grad" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22d3ee" stop-opacity="0"/><stop offset=".5" stop-color="#67e8f9" stop-opacity=".95"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></linearGradient>
                                <linearGradient id="home-hist-grad" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="#2563eb"/><stop offset="1" stop-color="#67e8f9"/></linearGradient>
                                <linearGradient id="home-rgb-grad" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#ef4444"/><stop offset=".5" stop-color="#22c55e"/><stop offset="1" stop-color="#3b82f6"/></linearGradient>
                                <filter id="home-soft-glow" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="5" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                                <clipPath id="home-image-clip"><rect x="54" y="48" width="350" height="220" rx="15"/></clipPath>
                            </defs>

                            <!-- Main image panel -->
                            <rect x="40" y="34" width="378" height="250" rx="20" fill="#0b1220" stroke="#334155" stroke-width="1.4"/>
                            <g clip-path="url(#home-image-clip)">
                                <rect x="54" y="48" width="350" height="220" fill="url(#home-frame-grad)"/>
                                <circle cx="320" cy="105" r="47" fill="#38bdf8" opacity=".20"/>
                                <path d="M45 238 140 142 197 194 270 112 420 253v35H45Z" fill="#1e40af" opacity=".55"/>
                                <path d="m60 251 106-80 57 47 73-66 111 91v35H60Z" fill="#0891b2" opacity=".38"/>
                                <path d="M54 48h350v220H54z" fill="none" stroke="#93c5fd" stroke-opacity=".12"/>
                                <!-- Pixel matrix -->
                                <g opacity=".62">
                                    @foreach ([0,1,2,3,4,5,6,7,8,9,10,11,12,13] as $i)
                                        @foreach ([0,1,2,3,4,5,6,7] as $j)
                                            <rect x="{{ 68 + $i * 23 }}" y="{{ 63 + $j * 23 }}" width="{{ (($i + $j) % 4 === 0) ? 8 : 4 }}" height="{{ (($i + $j) % 4 === 0) ? 8 : 4 }}" rx="1" fill="{{ (($i + 2 * $j) % 5 === 0) ? '#67e8f9' : '#bfdbfe' }}" opacity="{{ number_format(.12 + ((($i * 3 + $j * 2) % 7) * .08), 2) }}"/>
                                        @endforeach
                                    @endforeach
                                </g>
                                <!-- Edge contour -->
                                <path d="M101 215 157 156l44 36 69-77 74 56 31 38" fill="none" stroke="#a5f3fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="5 6" opacity=".8" filter="url(#home-soft-glow)"/>
                                <!-- Scanning line -->
                                <rect class="home-scan" x="54" y="142" width="350" height="34" fill="url(#home-scan-grad)" opacity=".75"/>
                            </g>
                            <rect x="54" y="48" width="350" height="220" rx="15" fill="none" stroke="#60a5fa" stroke-opacity=".28"/>
                            <path d="M65 61h18M65 61v18M393 61h-18M393 61v18M65 255h18M65 255v-18M393 255h-18M393 255v-18" fill="none" stroke="#67e8f9" stroke-width="2" stroke-linecap="round"/>
                            <text x="70" y="82" fill="#dbeafe" font-size="9" font-family="monospace" letter-spacing="1.5">FRAME ANALYSIS</text>
                            <text x="365" y="251" fill="#a5f3fc" font-size="8" text-anchor="end" font-family="monospace">640 × 480</text>

                            <!-- Floating channel module -->
                            <g class="home-float">
                                <rect x="438" y="48" width="148" height="105" rx="14" fill="#111827" stroke="#374151"/>
                                <text x="453" y="68" fill="#a1a1aa" font-size="9" font-family="monospace" letter-spacing="1.2">RGB CHANNELS</text>
                                <rect x="453" y="82" width="112" height="8" rx="4" fill="#27272a"/><rect x="453" y="82" width="82" height="8" rx="4" fill="#ef4444"/>
                                <rect x="453" y="101" width="112" height="8" rx="4" fill="#27272a"/><rect x="453" y="101" width="67" height="8" rx="4" fill="#22c55e"/>
                                <rect x="453" y="120" width="112" height="8" rx="4" fill="#27272a"/><rect x="453" y="120" width="94" height="8" rx="4" fill="#3b82f6"/>
                                <circle cx="575" cy="86" r="3" fill="#f87171"/><circle cx="575" cy="105" r="3" fill="#4ade80"/><circle cx="575" cy="124" r="3" fill="#60a5fa"/>
                            </g>

                            <!-- Histogram module -->
                            <g class="home-float-delay">
                                <rect x="438" y="172" width="148" height="112" rx="14" fill="#111827" stroke="#374151"/>
                                <text x="453" y="192" fill="#a1a1aa" font-size="9" font-family="monospace" letter-spacing="1.2">HISTOGRAM</text>
                                <path d="M453 261h118" stroke="#52525b" stroke-width="1"/>
                                @foreach ([18,27,42,35,61,75,58,86,68,48,72,40,27,18] as $index => $height)
                                    <rect class="home-bar" x="{{ 454 + ($index * 8) }}" y="{{ 258 - $height * .62 }}" width="5" height="{{ $height * .62 }}" rx="2" fill="url(#home-hist-grad)" opacity=".92"/>
                                @endforeach
                                <text x="453" y="274" fill="#71717a" font-size="7" font-family="monospace">0</text><text x="570" y="274" fill="#71717a" font-size="7" text-anchor="end" font-family="monospace">255</text>
                            </g>

                            <!-- Bottom process strip -->
                            <rect x="40" y="305" width="546" height="54" rx="13" fill="#111827" stroke="#334155"/>
                            <g font-family="monospace" font-size="9">
                                <circle cx="62" cy="332" r="4" fill="#34d399"/><text x="74" y="335" fill="#d4d4d8">CAPTURE</text>
                                <path d="M133 332h28m-5-4 5 4-5 4" fill="none" stroke="#52525b" stroke-width="1.2"/>
                                <circle cx="181" cy="332" r="4" fill="#60a5fa"/><text x="193" y="335" fill="#d4d4d8">PROCESS</text>
                                <path d="M260 332h28m-5-4 5 4-5 4" fill="none" stroke="#52525b" stroke-width="1.2"/>
                                <circle cx="309" cy="332" r="4" fill="#c4b5fd"/><text x="321" y="335" fill="#d4d4d8">ANALYZE</text>
                                <path d="M389 332h28m-5-4 5 4-5 4" fill="none" stroke="#52525b" stroke-width="1.2"/>
                                <circle cx="438" cy="332" r="4" fill="#67e8f9"/><text x="450" y="335" fill="#d4d4d8">EXPORT</text>
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- Floating labels -->
                <div class="home-float absolute -left-2 top-[18%] hidden rounded-xl border border-cyan-400/20 bg-zinc-900/95 px-3 py-2 text-[10px] font-semibold tracking-wide text-cyan-200 shadow-xl sm:block lg:-left-8">
                    <span class="mr-1.5 text-cyan-400">◈</span> PIXEL INSPECTOR
                </div>
                <div class="home-float-delay absolute -bottom-5 right-5 hidden rounded-xl border border-violet-400/20 bg-zinc-900/95 px-3 py-2 text-[10px] font-semibold tracking-wide text-violet-200 shadow-xl sm:block">
                    <span class="mr-1.5 text-violet-400">◉</span> HSV COLOR SPACE
                </div>
            </div>
        </section>

        <!-- TRUST / CAPABILITY STRIP -->
        <section class="mt-20 border-y border-zinc-800/80 py-6 lg:mt-24">
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-4 sm:gap-4">
                <div><p class="text-2xl font-semibold tracking-tight text-white">04</p><p class="mt-1 text-xs text-zinc-500">Processing categories</p></div>
                <div><p class="text-2xl font-semibold tracking-tight text-white">18</p><p class="mt-1 text-xs text-zinc-500">Selectable effects</p></div>
                <div><p class="text-2xl font-semibold tracking-tight text-white">Live</p><p class="mt-1 text-xs text-zinc-500">Camera processing</p></div>
                <div><p class="text-2xl font-semibold tracking-tight text-white">OpenCV.js</p><p class="mt-1 text-xs text-zinc-500">Image processing engine</p></div>
            </div>
        </section>

        <!-- VALUE PROPOSITION -->
        <section id="capabilities" class="home-anchor pt-20 lg:pt-28">
            <div class="mx-auto mb-10 max-w-2xl text-center">
                <p class="text-[11px] font-semibold uppercase tracking-[.22em] text-blue-400">Explore the capabilities</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white sm:text-3xl">One studio. Multiple ways to understand an image.</h2>
                <p class="mt-4 text-sm leading-7 text-zinc-400 sm:text-base">Dari representasi warna hingga analisis piksel, setiap alat dirancang agar proses pengolahan citra dapat diamati dan dipahami.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="home-card rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-violet-400/15 bg-violet-500/10 text-violet-300">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8.5"/><path d="M12 3.5v17M3.5 12h17M6 6l12 12M18 6 6 18"/></svg>
                    </div>
                    <p class="mt-5 text-[10px] font-semibold uppercase tracking-[.18em] text-violet-300">01 / Color</p>
                    <h3 class="mt-2 text-base font-semibold text-white">Color Processing</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Pelajari representasi warna melalui RGB, CMYK, HSV/HSB, Grayscale, dan Binary Threshold.</p>
                    <div class="mt-5 flex gap-1.5"><span class="h-1.5 flex-1 rounded-full bg-red-400"></span><span class="h-1.5 flex-1 rounded-full bg-green-400"></span><span class="h-1.5 flex-1 rounded-full bg-blue-400"></span><span class="h-1.5 flex-1 rounded-full bg-violet-400"></span></div>
                </article>

                <article class="home-card rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-cyan-400/15 bg-cyan-500/10 text-cyan-300">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16M4 12h16M4 17h16"/><circle cx="8" cy="7" r="2" fill="#09090b"/><circle cx="15" cy="12" r="2" fill="#09090b"/><circle cx="10" cy="17" r="2" fill="#09090b"/></svg>
                    </div>
                    <p class="mt-5 text-[10px] font-semibold uppercase tracking-[.18em] text-cyan-300">02 / Filtering</p>
                    <h3 class="mt-2 text-base font-semibold text-white">Image Filtering</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Bandingkan Gaussian Blur, Median Blur, dan Bilateral Filter untuk memahami smoothing dan reduksi noise.</p>
                    <div class="mt-5 flex items-end gap-1"><span class="h-3 w-3 rounded-sm bg-cyan-300/30"></span><span class="h-5 w-3 rounded-sm bg-cyan-300/45"></span><span class="h-7 w-3 rounded-sm bg-cyan-300/65"></span><span class="h-5 w-3 rounded-sm bg-cyan-300/45"></span><span class="h-3 w-3 rounded-sm bg-cyan-300/30"></span></div>
                </article>

                <article class="home-card rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-amber-400/15 bg-amber-500/10 text-amber-300">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m4 18 5-6 3 3 6-9 2 2"/><path d="M4 4v16h16"/></svg>
                    </div>
                    <p class="mt-5 text-[10px] font-semibold uppercase tracking-[.18em] text-amber-300">03 / Detection</p>
                    <h3 class="mt-2 text-base font-semibold text-white">Edge Detection</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Identifikasi batas objek menggunakan Sobel, Prewitt, Laplacian, dan Canny Edge Detection.</p>
                    <div class="mt-5 flex items-center gap-2 text-[10px] font-mono text-zinc-500"><span class="h-px w-8 bg-amber-300"></span> GRADIENT <span class="h-px w-8 bg-amber-300/40"></span> EDGE</div>
                </article>

                <article class="home-card rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-emerald-400/15 bg-emerald-500/10 text-emerald-300">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3M2 19h20"/></svg>
                    </div>
                    <p class="mt-5 text-[10px] font-semibold uppercase tracking-[.18em] text-emerald-300">04 / Enhancement</p>
                    <h3 class="mt-2 text-base font-semibold text-white">Image Enhancement</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Atur brightness, contrast, gamma, histogram equalization, serta threshold otomatis Otsu.</p>
                    <div class="mt-5 flex h-7 items-end gap-1">@foreach ([25,42,35,62,48,82,56,72,40,90,64,48] as $bar)<span class="home-bar flex-1 rounded-t-sm bg-gradient-to-t from-emerald-600/70 to-cyan-300/90" style="height:{{ $bar }}%"></span>@endforeach</div>
                </article>
            </div>
        </section>

        <!-- ANALYSIS / WORKFLOW -->
        <section class="mt-20 grid gap-6 lg:mt-28 lg:grid-cols-[.92fr_1.08fr] lg:items-stretch">
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6 sm:p-8">
                <p class="text-[11px] font-semibold uppercase tracking-[.2em] text-blue-400">Beyond filters</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white">Turn visual changes into measurable insight.</h2>
                <p class="mt-4 text-sm leading-7 text-zinc-400">ImageLab Live menyediakan alat analisis untuk membantu pengguna melihat bukan hanya bagaimana gambar berubah, tetapi juga karakteristik numerik di balik perubahan tersebut.</p>
                <a href="{{ url('/about') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-blue-300 transition hover:text-blue-200">Explore all features <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="mb-4 flex h-9 w-9 items-center justify-center rounded-lg bg-blue-500/10 text-blue-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 20V9h3v11m3 0V4h3v16m3 0v-8h3v8m3 0V6h2v14"/></svg></div>
                    <h3 class="font-semibold text-white">Image Histogram</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Amati distribusi intensitas melalui histogram RGB dan grayscale.</p>
                </div>
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="mb-4 flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-500/10 text-cyan-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 9h8M8 13h4M8 16h6"/></svg></div>
                    <h3 class="font-semibold text-white">Image Metrics</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Periksa resolusi, jumlah piksel, brightness, contrast, dan saturation.</p>
                </div>
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="mb-4 flex h-9 w-9 items-center justify-center rounded-lg bg-violet-500/10 text-violet-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5M8 10.5h5M10.5 8v5"/></svg></div>
                    <h3 class="font-semibold text-white">Pixel Inspector</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Pilih titik gambar untuk melihat koordinat, RGB, HEX, dan HSV.</p>
                </div>
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/55 p-5">
                    <div class="mb-4 flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v12m-4-4 4 4 4-4"/><path d="M4 17v3h16v-3"/></svg></div>
                    <h3 class="font-semibold text-white">Analysis Export</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-500">Unduh laporan analisis dalam PNG dan data histogram dalam CSV.</p>
                </div>
            </div>
        </section>

        <!-- SIMPLE WORKFLOW -->
        <section class="mt-20 rounded-2xl border border-zinc-800 bg-zinc-900/45 p-6 sm:p-8 lg:mt-28">
            <div class="mb-8 max-w-2xl">
                <p class="text-[11px] font-semibold uppercase tracking-[.2em] text-cyan-300">A simple workflow</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white sm:text-3xl">From camera to understanding.</h2>
                <p class="mt-3 text-sm leading-7 text-zinc-400">Mulai dari frame kamera, pilih proses yang ingin dipelajari, lalu amati dan ekspor hasil analisisnya.</p>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $homeSteps = [
                        ['01', 'Capture', 'Aktifkan kamera untuk mendapatkan frame sumber.'],
                        ['02', 'Process', 'Pilih model warna atau algoritma pemrosesan.'],
                        ['03', 'Analyze', 'Bandingkan hasil, histogram, metrik, dan nilai piksel.'],
                        ['04', 'Export', 'Simpan capture atau unduh laporan analisis.'],
                    ];
                @endphp
                @foreach ($homeSteps as $step)
                    <div class="relative">
                        <span class="text-xs font-semibold tracking-[.18em] text-blue-400">{{ $step[0] }}</span>
                        <h3 class="mt-2 text-base font-semibold text-white">{{ $step[1] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- FINAL CTA -->
        <section class="relative mt-20 overflow-hidden rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-950/70 via-zinc-900 to-violet-950/50 p-7 sm:p-10 lg:mt-28 lg:p-12">
            <div class="pointer-events-none absolute -right-16 -top-24 h-72 w-72 rounded-full bg-cyan-400/[0.08] blur-3xl"></div>
            <div class="relative flex flex-col gap-7 md:flex-row md:items-center md:justify-between">
                <div class="max-w-2xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[.2em] text-cyan-300">Start exploring</p>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white sm:text-3xl">Every image has more to reveal.</h2>
                    <p class="mt-3 text-sm leading-7 text-zinc-300/75">Masuk ke Processing Studio dan eksplorasi bagaimana warna, intensitas, tepi, serta piksel membentuk sebuah citra digital.</p>
                </div>
                <a href="{{ url('/studio') }}" class="group inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-zinc-950 transition hover:-translate-y-0.5 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-950">
                    Open ImageLab Studio
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>
        </section>
    </main>
</div>
@endsection
