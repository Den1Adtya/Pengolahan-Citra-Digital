@extends('layouts.app')

@section('title', 'About ImageLab Live - Digital Image Processing Laboratory')

@push('styles')
<style>
    .about-grid-bg {
        background-image: linear-gradient(rgba(63,63,70,.22) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(63,63,70,.22) 1px, transparent 1px);
        background-size: 34px 34px;
        mask-image: linear-gradient(to bottom, black, transparent 88%);
    }
    .about-glow { box-shadow: 0 0 70px rgba(59,130,246,.12); }
    .about-card { transition: transform .2s ease, border-color .2s ease, background-color .2s ease; }
    .about-card:hover { transform: translateY(-3px); border-color: rgba(96,165,250,.4); background-color: rgba(24,24,27,.95); }
    .about-anchor { scroll-margin-top: 6rem; }
    @media (prefers-reduced-motion: reduce) {
        .about-card { transition: none; }
        .about-card:hover { transform: none; }
    }
</style>
@endpush

@section('content')
<div class="relative isolate overflow-hidden bg-zinc-950 text-zinc-100">
    <div class="about-grid-bg pointer-events-none absolute inset-x-0 top-0 -z-10 h-[520px] opacity-50"></div>
    <div class="pointer-events-none absolute -top-36 right-[-8rem] -z-10 h-96 w-96 rounded-full bg-blue-600/10 blur-3xl"></div>
    <div class="pointer-events-none absolute top-72 left-[-10rem] -z-10 h-80 w-80 rounded-full bg-violet-600/10 blur-3xl"></div>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <!-- HERO -->
        <section class="relative mb-16 overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900/70 p-7 sm:p-10 lg:p-14 about-glow">
            <div class="grid items-center gap-10 lg:grid-cols-[1.15fr_.85fr]">
                <div>
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[.18em] text-blue-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        About ImageLab Live
                    </div>
                    <h1 class="max-w-2xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        Understand every pixel. <span class="bg-gradient-to-r from-blue-400 via-cyan-300 to-violet-400 bg-clip-text text-transparent">Explore every process.</span>
                    </h1>
                    <p class="mt-6 max-w-2xl text-sm leading-7 text-zinc-400 sm:text-base">
                        ImageLab Live adalah laboratorium pengolahan citra digital berbasis web untuk mengeksplorasi bagaimana gambar direpresentasikan, dianalisis, dan diubah melalui berbagai algoritma. Proses dapat diamati langsung melalui kamera dengan visualisasi dan parameter yang dapat disesuaikan.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ url('/studio') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                            Buka Processing Studio
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                        <a href="#studio-features" class="inline-flex items-center rounded-lg border border-zinc-700 px-5 py-3 text-sm font-medium text-zinc-300 transition hover:border-zinc-500 hover:bg-zinc-800">Jelajahi fitur</a>
                    </div>
                    <div class="mt-8 flex flex-wrap gap-2 text-xs text-zinc-500">
                        <span class="rounded-full border border-zinc-800 bg-zinc-950/70 px-3 py-1.5">Real-time camera</span>
                        <span class="rounded-full border border-zinc-800 bg-zinc-950/70 px-3 py-1.5">OpenCV.js</span>
                        <span class="rounded-full border border-zinc-800 bg-zinc-950/70 px-3 py-1.5">Interactive analysis</span>
                    </div>
                </div>

                <!-- DECORATIVE PROCESS VISUAL -->
                <div class="relative mx-auto w-full max-w-md">
                    <div class="absolute inset-8 rounded-full bg-blue-500/10 blur-3xl"></div>
                    <div class="relative rounded-2xl border border-zinc-700/80 bg-zinc-950/90 p-4 shadow-2xl">
                        <div class="mb-4 flex items-center justify-between border-b border-zinc-800 pb-3">
                            <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-400"></span><span class="text-[10px] font-semibold uppercase tracking-widest text-zinc-400">Live pixel analysis</span></div>
                            <span class="text-[10px] text-zinc-600">FRAME / 001</span>
                        </div>
                        <div class="relative overflow-hidden rounded-xl border border-blue-500/20 bg-gradient-to-br from-blue-950 via-zinc-900 to-violet-950 p-5">
                            <div class="absolute inset-0 opacity-25" style="background-image:linear-gradient(rgba(148,163,184,.22) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,.22) 1px,transparent 1px);background-size:18px 18px"></div>
                            <div class="relative grid grid-cols-8 gap-1.5 sm:gap-2" aria-hidden="true">
                                @foreach ([35,55,75,45,25,40,70,50,55,90,130,105,45,35,75,60,25,65,180,220,125,50,45,85,40,80,150,195,230,90,35,60,20,55,100,165,190,75,30,45,35,70,130,175,145,60,25,40,45,85,115,130,95,45,30,55,30,50,80,90,75,35,25,45] as $tone)
                                    <span class="aspect-square rounded-[3px]" style="background:rgba({{ min(255, 25 + $tone) }},{{ min(255, 70 + (int)($tone * .55)) }},{{ min(255, 130 + (int)($tone * .4)) }},{{ number_format(.22 + ($tone / 320), 2) }})"></span>
                                @endforeach
                            </div>
                            <div class="pointer-events-none absolute inset-x-0 top-1/2 h-px bg-cyan-300/80 shadow-[0_0_14px_rgba(103,232,249,.9)]"></div>
                            <div class="absolute bottom-3 left-3 rounded-md border border-cyan-300/20 bg-zinc-950/80 px-2 py-1 text-[9px] tracking-widest text-cyan-200">PIXEL MATRIX</div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <div class="rounded-lg border border-zinc-800 bg-zinc-900 p-2.5"><div class="mb-2 h-1.5 rounded-full bg-red-400/80"></div><p class="text-[9px] uppercase tracking-widest text-zinc-500">Red</p></div>
                            <div class="rounded-lg border border-zinc-800 bg-zinc-900 p-2.5"><div class="mb-2 h-1.5 rounded-full bg-green-400/80"></div><p class="text-[9px] uppercase tracking-widest text-zinc-500">Green</p></div>
                            <div class="rounded-lg border border-zinc-800 bg-zinc-900 p-2.5"><div class="mb-2 h-1.5 rounded-full bg-blue-400/80"></div><p class="text-[9px] uppercase tracking-widest text-zinc-500">Blue</p></div>
                        </div>
                        <div class="mt-4 flex h-12 items-end gap-1 rounded-lg border border-zinc-800 bg-zinc-900/80 px-3 py-2" aria-hidden="true">
                            @foreach ([20,35,28,55,42,72,48,85,62,94,70,52,80,44,67,35,58,26,46,30] as $bar)
                                <span class="flex-1 rounded-t-sm bg-gradient-to-t from-blue-600/70 to-cyan-300/90" style="height:{{ $bar }}%"></span>
                            @endforeach
                        </div>
                        <div class="mt-3 flex items-center justify-between text-[10px] text-zinc-600"><span>RGB → PROCESS → ANALYZE</span><span class="text-emerald-400">● READY</span></div>
                    </div>
                    <div class="absolute -right-3 top-12 rounded-lg border border-violet-400/20 bg-zinc-900 px-3 py-2 text-[10px] font-medium text-violet-300 shadow-xl sm:-right-6">HSV / COLOR SPACE</div>
                    <div class="absolute -bottom-4 -left-3 rounded-lg border border-cyan-400/20 bg-zinc-900 px-3 py-2 text-[10px] font-medium text-cyan-300 shadow-xl sm:-left-6">EDGE DETECTION</div>
                </div>
            </div>
        </section>

        <!-- QUICK WORKFLOW -->
        <section class="mb-20">
            <div class="mb-7 max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[.2em] text-blue-400">How it works</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Dari kamera menjadi insight</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-400">Studio menggabungkan pratinjau kamera, pemrosesan, analisis, dan ekspor hasil dalam satu alur kerja.</p>
            </div>
            <div class="grid gap-3 md:grid-cols-4">
                @php
                    $workflow = [
                        ['01', 'Capture', 'Aktifkan kamera untuk mendapatkan frame sebagai sumber citra.'],
                        ['02', 'Process', 'Pilih model warna atau algoritma dan atur parameter yang tersedia.'],
                        ['03', 'Analyze', 'Amati histogram, metrik citra, distribusi warna, dan nilai piksel.'],
                        ['04', 'Export', 'Simpan hasil capture atau unduh laporan analisis dan data CSV.'],
                    ];
                @endphp
                @foreach ($workflow as $step)
                    <article class="rounded-xl border border-zinc-800 bg-zinc-900/70 p-5">
                        <span class="text-xs font-bold tracking-widest text-blue-400">{{ $step[0] }}</span>
                        <h3 class="mt-3 text-base font-semibold text-zinc-100">{{ $step[1] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $step[2] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- FEATURES -->
        <section id="studio-features" class="about-anchor mb-20">
            <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[.2em] text-blue-400">Studio capabilities</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Fitur yang tersedia di Processing Studio</h2>
                    <p class="mt-3 text-sm leading-6 text-zinc-400">Berikut penjelasan fitur yang dapat digunakan pada Studio saat ini, dikelompokkan berdasarkan tujuan pemrosesannya.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-300">18 pilihan pemrosesan</span>
            </div>

            <!-- COLOR PROCESSING -->
            <article class="mb-6 rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-300">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8M18.4 5.6 5.6 18.4"/></svg>
                    </div>
                    <div><p class="text-[11px] font-semibold uppercase tracking-widest text-violet-300">01 / Color Processing</p><h3 class="mt-1 text-xl font-semibold">Model dan representasi warna</h3><p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-400">Pelajari bagaimana satu citra direpresentasikan dalam ruang warna yang berbeda. Untuk model berbasis channel, Studio menyediakan pilihan channel yang ditampilkan dalam grayscale agar nilai masing-masing kanal dapat diamati.</p></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold text-zinc-100">Original RGB</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menampilkan frame kamera asli sebagai acuan sebelum efek pemrosesan diterapkan.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold text-zinc-100">RGB Color Model</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Memisahkan citra menjadi kanal Red, Green, dan Blue. Model aditif ini umum digunakan pada layar dan kamera digital.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold text-zinc-100">CMYK Color Model</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menampilkan kanal Cyan, Magenta, Yellow, dan Key/Black melalui konversi dari RGB untuk memahami model warna subtraktif yang digunakan dalam pencetakan.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold text-zinc-100">HSV / HSB Color Model</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Memisahkan Hue, Saturation, dan Value/Brightness sehingga jenis warna, kejenuhan, dan kecerahan dapat diamati secara terpisah.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold text-zinc-100">Grayscale</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Mengubah citra berwarna menjadi intensitas keabuan sebagai dasar untuk sejumlah analisis dan algoritma citra.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold text-zinc-100">Binary Threshold</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Mengubah citra grayscale menjadi dua kelas intensitas, yaitu hitam dan putih, berdasarkan nilai ambang yang dapat diatur.</p></div>
                </div>
            </article>

            <!-- IMAGE FILTERING -->
            <article class="mb-6 rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-500/10 text-cyan-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16M4 12h16M4 17h16"/><circle cx="8" cy="7" r="2" fill="#09090b"/><circle cx="15" cy="12" r="2" fill="#09090b"/><circle cx="11" cy="17" r="2" fill="#09090b"/></svg></div>
                    <div><p class="text-[11px] font-semibold uppercase tracking-widest text-cyan-300">02 / Image Filtering</p><h3 class="mt-1 text-xl font-semibold">Penghalusan dan pengurangan noise</h3><p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-400">Filter digunakan untuk mengubah karakter lokal citra, misalnya mengurangi noise atau menghaluskan detail. Ukuran kernel dapat disesuaikan pada filter yang memerlukannya.</p></div>
                </div>
                <div class="grid gap-3 md:grid-cols-3">
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Gaussian Blur</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menghaluskan citra menggunakan pembobotan Gaussian pada area kernel. Cocok untuk mengurangi detail kecil dan noise.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Median Blur</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Mengganti nilai piksel dengan median dari lingkungan sekitarnya. Sering digunakan untuk mengurangi noise impuls seperti salt-and-pepper.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Bilateral Filter</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menghaluskan area dengan mempertimbangkan kedekatan posisi dan kemiripan intensitas, sehingga tepi citra lebih terjaga dibanding blur biasa.</p></div>
                </div>
            </article>

            <!-- EDGE DETECTION -->
            <article class="mb-6 rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m4 18 5-12 4 8 3-5 4 9"/><path d="M3 21h18"/></svg></div>
                    <div><p class="text-[11px] font-semibold uppercase tracking-widest text-amber-300">03 / Edge Detection</p><h3 class="mt-1 text-xl font-semibold">Deteksi batas dan perubahan intensitas</h3><p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-400">Algoritma deteksi tepi membantu menonjolkan perubahan intensitas yang dapat menandai batas objek atau struktur pada citra.</p></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Sobel Edge Detection</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Mengestimasi gradien citra pada arah horizontal dan vertikal untuk menampilkan perubahan intensitas.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Prewitt Edge Detection</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menggunakan kernel gradien 3×3 untuk mendeteksi perubahan intensitas pada dua arah utama.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Laplacian Edge Detection</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menggunakan turunan kedua untuk menonjolkan perubahan intensitas di sekitar tepi objek.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Canny Edge Detection</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Metode bertahap yang mencakup peredaman noise, perhitungan gradien, penipisan tepi, dan hysteresis. Nilai lower dan upper threshold dapat diatur.</p></div>
                </div>
            </article>

            <!-- ENHANCEMENT -->
            <article class="mb-6 rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 20V9m6 11V4m6 16v-8m6 8V6"/><path d="M1 20h22"/></svg></div>
                    <div><p class="text-[11px] font-semibold uppercase tracking-widest text-emerald-300">04 / Image Enhancement</p><h3 class="mt-1 text-xl font-semibold">Penyesuaian dan peningkatan citra</h3><p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-400">Kumpulan fitur untuk mengubah intensitas dan kontras, serta mempelajari pengaruh distribusi histogram terhadap tampilan citra.</p></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Histogram Equalization</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Mendistribusikan ulang intensitas grayscale untuk membantu meningkatkan kontras global pada kondisi tertentu.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Brightness Adjustment</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menambah atau mengurangi nilai kecerahan piksel menggunakan kontrol interaktif.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Contrast Adjustment</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Mengatur rentang perbedaan intensitas gelap dan terang melalui faktor kontras.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Gamma Correction</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menerapkan transformasi gamma untuk mengoreksi respons intensitas, terutama pada area gelap atau terang.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Otsu Automatic Threshold</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menentukan nilai ambang otomatis berdasarkan histogram grayscale untuk memisahkan citra menjadi dua kelas.</p></div>
                </div>
            </article>

            <!-- HISTOGRAM -->
            <article class="mb-6 rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                <div class="flex flex-col gap-5 md:flex-row md:items-center">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 20V11h3v9M9 20V5h3v15m3 0v-8h3v8m3 0V3h2v17"/></svg></div>
                    <div class="flex-1"><p class="text-[11px] font-semibold uppercase tracking-widest text-blue-300">05 / Histogram Visualization</p><h3 class="mt-1 text-xl font-semibold">Membaca distribusi intensitas piksel</h3><p class="mt-2 text-sm leading-6 text-zinc-400">Panel Image Histogram menyediakan dua mode tampilan: RGB Histogram untuk melihat distribusi kanal merah, hijau, dan biru; serta Grayscale Histogram untuk melihat sebaran intensitas keabuan. Histogram membantu memahami karakteristik pencahayaan dan kontras frame sumber.</p></div>
                </div>
                <div class="mt-6 rounded-xl border border-zinc-800 bg-zinc-950 p-4">
                    <div class="mb-3 flex items-center justify-between"><span class="text-[10px] font-semibold uppercase tracking-widest text-zinc-500">Histogram preview</span><span class="text-[10px] text-zinc-600">0 — 255 intensity</span></div>
                    <div class="flex h-20 items-end gap-1" aria-hidden="true">
                        @foreach ([10,16,25,36,48,62,78,90,72,54,43,58,70,88,76,60,49,34,27,18,12,8,15,25,38,50,43,31,20,12,8,5] as $bar)
                            <span class="flex-1 rounded-t-sm bg-gradient-to-t from-blue-700/60 to-cyan-300/80" style="height:{{ $bar }}%"></span>
                        @endforeach
                    </div>
                    <div class="mt-2 flex justify-between text-[9px] text-zinc-600"><span>Dark</span><span>Midtones</span><span>Bright</span></div>
                </div>
            </article>

            <!-- ADVANCED ANALYSIS -->
            <article class="mb-6 rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-fuchsia-500/10 text-fuchsia-300"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5M10.8 7.5v3.6l2.3 1.4"/></svg></div>
                    <div><p class="text-[11px] font-semibold uppercase tracking-widest text-fuchsia-300">06 / Advanced Image Analysis</p><h3 class="mt-1 text-xl font-semibold">Membandingkan dan memahami isi citra</h3><p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-400">Panel analisis menampilkan frame sumber dan hasil pemrosesan berdampingan, disertai metrik, informasi warna, serta pemeriksaan nilai piksel.</p></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Original vs Processed Frame</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Membandingkan frame kamera asli dengan hasil dari efek yang sedang dipilih agar perubahannya mudah diamati.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Image Metrics</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menampilkan resolusi, jumlah piksel, mean brightness, contrast, mean saturation, dan processed mean sebagai ringkasan numerik citra.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Average RGB Balance</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menampilkan rata-rata nilai kanal Red, Green, dan Blue beserta bar visual untuk membaca keseimbangan warna frame.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4"><h4 class="font-semibold">Dominant Sampled Color</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Menampilkan perkiraan warna dominan hasil sampling, termasuk swatch warna, nilai HEX, dan RGB.</p></div>
                    <div class="about-card rounded-xl border border-zinc-800 bg-zinc-950/60 p-4 sm:col-span-2"><h4 class="font-semibold">Pixel Inspector</h4><p class="mt-2 text-sm leading-6 text-zinc-500">Klik titik pada Original Frame untuk memeriksa koordinat piksel, nilai RGB, kode HEX, dan representasi HSV dari lokasi tersebut.</p></div>
                </div>
            </article>

            <!-- CONTROLS AND OUTPUT -->
            <div class="grid gap-6 lg:grid-cols-2">
                <article class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-blue-300">07 / Contextual Controls</p>
                    <h3 class="mt-2 text-xl font-semibold">Parameter yang mengikuti efek</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-400">Panel Processing Parameters menampilkan kontrol yang relevan dengan efek aktif, sehingga pengaturan yang tidak diperlukan tidak memenuhi layar.</p>
                    <ul class="mt-5 space-y-3 text-sm text-zinc-400">
                        <li class="flex gap-3"><span class="text-blue-400">•</span><span>Threshold untuk Binary Threshold.</span></li>
                        <li class="flex gap-3"><span class="text-blue-400">•</span><span>Kernel Size untuk Gaussian, Median, dan Bilateral Filter.</span></li>
                        <li class="flex gap-3"><span class="text-blue-400">•</span><span>Lower dan Upper Threshold untuk Canny.</span></li>
                        <li class="flex gap-3"><span class="text-blue-400">•</span><span>Brightness, Contrast, dan Gamma untuk enhancement.</span></li>
                        <li class="flex gap-3"><span class="text-blue-400">•</span><span>Channel Preview untuk eksplorasi kanal warna.</span></li>
                    </ul>
                </article>
                <article class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 sm:p-7">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-emerald-300">08 / Capture & Export</p>
                    <h3 class="mt-2 text-xl font-semibold">Simpan hasil dan dokumentasikan analisis</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-400">Hasil capture dan ringkasan analisis dapat disimpan untuk dokumentasi atau bahan pembelajaran.</p>
                    <ul class="mt-5 space-y-3 text-sm text-zinc-400">
                        <li class="flex gap-3"><span class="text-emerald-400">•</span><span>Capture mengambil frame hasil pemrosesan yang sedang ditampilkan.</span></li>
                        <li class="flex gap-3"><span class="text-emerald-400">•</span><span>Captured Results menampilkan daftar capture, dengan opsi unduh dan hapus item.</span></li>
                        <li class="flex gap-3"><span class="text-emerald-400">•</span><span>Download Report mengekspor ringkasan analisis menjadi gambar PNG.</span></li>
                        <li class="flex gap-3"><span class="text-emerald-400">•</span><span>Export CSV mengunduh data metrik dan distribusi histogram untuk diolah lebih lanjut.</span></li>
                    </ul>
                </article>
            </div>
        </section>

        <!-- INTERFACE DETAILS -->
        <section class="mb-20 grid gap-6 lg:grid-cols-[.9fr_1.1fr]">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.2em] text-blue-400">Designed for exploration</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Dibuat agar prosesnya mudah diamati</h2>
                <p class="mt-4 text-sm leading-7 text-zinc-400">Selain algoritma, Studio menyediakan elemen antarmuka untuk menjaga alur eksperimen tetap teratur: status kamera dan FPS, kategori efek berbentuk accordion, parameter kontekstual, serta sidebar hasil capture.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-zinc-800 bg-zinc-900/70 p-4"><h3 class="text-sm font-semibold">Live Camera Status</h3><p class="mt-2 text-sm leading-6 text-zinc-500">Indikator status, resolusi, dan processing FPS membantu memantau sesi kamera.</p></div>
                <div class="rounded-xl border border-zinc-800 bg-zinc-900/70 p-4"><h3 class="text-sm font-semibold">Accordion Categories</h3><p class="mt-2 text-sm leading-6 text-zinc-500">Kategori efek dikelompokkan agar daftar pilihan lebih ringkas dan mudah dijelajahi.</p></div>
                <div class="rounded-xl border border-zinc-800 bg-zinc-900/70 p-4"><h3 class="text-sm font-semibold">Compact Information Bar</h3><p class="mt-2 text-sm leading-6 text-zinc-500">Resolution, FPS, dan Active Effect disajikan dalam satu panel informasi ringkas.</p></div>
                <div class="rounded-xl border border-zinc-800 bg-zinc-900/70 p-4"><h3 class="text-sm font-semibold">Sticky Results Sidebar</h3><p class="mt-2 text-sm leading-6 text-zinc-500">Daftar efek dan hasil capture ditempatkan di sidebar agar mudah dijangkau saat menjelajahi halaman.</p></div>
            </div>
        </section>

        <!-- TECHNOLOGY -->
        <section class="mb-16 rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6 sm:p-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[.2em] text-blue-400">Technology</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight">Teknologi yang digunakan</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-400">ImageLab Live dibangun menggunakan teknologi web untuk menghadirkan antarmuka interaktif dan pemrosesan citra langsung di browser.</p>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ([['Laravel','Application framework'],['Blade','Template rendering'],['Tailwind CSS','Interface styling'],['JavaScript','Client-side interaction'],['OpenCV.js','Image processing']] as $tech)
                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/70 p-4"><p class="font-semibold text-zinc-100">{{ $tech[0] }}</p><p class="mt-1 text-xs text-zinc-500">{{ $tech[1] }}</p></div>
                @endforeach
            </div>
            <p class="mt-5 text-xs leading-5 text-zinc-600">Pemrosesan kamera membutuhkan izin akses kamera dari browser dan berjalan pada localhost atau koneksi HTTPS. Ketersediaan serta performa pemrosesan dapat dipengaruhi perangkat dan browser yang digunakan.</p>
        </section>

        <!-- CTA -->
        <section class="rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-950/60 via-zinc-900 to-violet-950/40 p-7 text-center sm:p-10">
            <p class="text-xs font-semibold uppercase tracking-[.2em] text-blue-300">Start exploring</p>
            <h2 class="mt-3 text-2xl font-bold tracking-tight sm:text-3xl">Siap menjelajahi setiap piksel?</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-zinc-400">Buka Processing Studio, aktifkan kamera, lalu coba berbagai model warna, filter, deteksi tepi, dan alat analisis citra.</p>
            <a href="{{ url('/studio') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                Explore Processing Studio
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </section>
    </div>
</div>
@endsection
