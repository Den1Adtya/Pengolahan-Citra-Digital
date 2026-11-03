@extends('layouts.app')

@section('title', 'ImageLab Live - Processing Studio')

@push('styles')
<style>
    .studio-range { accent-color: #3b82f6; }
    .accordion-chevron { transition: transform .2s ease; }
    [data-accordion-trigger][aria-expanded="true"] .accordion-chevron { transform: rotate(180deg); }
    .studio-stat + .studio-stat { border-left: 1px solid #27272a; }
    .studio-sidebar-scroll { scrollbar-width: thin; scrollbar-color: #3f3f46 transparent; }
    .studio-sidebar-scroll::-webkit-scrollbar { width: 6px; }
    .studio-sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .studio-sidebar-scroll::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 999px; }
    @media (max-width: 640px) {
        .studio-stat + .studio-stat { border-left: 0; }
    }
</style>
@endpush

@section('content')
<div class="max-w-400 mx-auto px-4 sm:px-6 py-7">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 mb-7">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-xs uppercase tracking-widest text-blue-400 font-semibold">Real-Time Image Processing</span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight">Processing Studio</h1>
            <p class="text-zinc-500 mt-2 text-sm">Explore digital image processing through your camera.</p>
        </div>

        <div class="inline-flex items-center gap-3 self-start md:self-auto bg-zinc-900 border border-zinc-800 rounded-lg px-4 py-3">
            <span id="cameraIndicator" class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
            <span id="cameraStatus" class="text-sm text-zinc-400">Camera Offline</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: PREVIEW, PARAMETERS, HISTOGRAM -->
        <main class="xl:col-span-8 space-y-5">

            <!-- CAMERA PREVIEW -->
            <section class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-800">
                    <div>
                        <h2 class="font-semibold text-sm">Live Camera Preview</h2>
                        <p class="text-xs text-zinc-500 mt-1">Real-time processed camera output</p>
                    </div>
                    <span class="text-xs text-zinc-500 tracking-wide">OPENCV.JS</span>
                </div>

                <div class="relative aspect-video bg-black flex items-center justify-center overflow-hidden">
                    <video id="cameraVideo" autoplay playsinline muted class="w-full h-full object-contain hidden"></video>
                    <canvas id="cameraCanvas" class="w-full h-full object-contain hidden"></canvas>

                    <div id="cameraPlaceholder" class="text-center px-5">
                        <div class="w-20 h-20 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-zinc-500">
                                <path d="M14 4h-4l-2 3H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-4z"/>
                                <circle cx="12" cy="13" r="3"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-zinc-300">Camera Preview</h3>
                        <p class="text-sm text-zinc-600 mt-2">Activate your camera to start processing.</p>
                    </div>
                </div>

                <!-- CAMERA ACTIONS: PRIMARY / SECONDARY / GHOST -->
                <div class="p-4 sm:p-5 flex flex-wrap items-center gap-3">
                    <button id="startCamera" type="button"
                            class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-lg shadow-blue-950/30 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M7 4.8c0-.8.9-1.3 1.6-.9l11 6.2a1 1 0 0 1 0 1.8l-11 6.2c-.7.4-1.6-.1-1.6-.9V4.8z"/>
                        </svg>
                        Start Camera
                    </button>

                    <button id="stopCamera" type="button" disabled
                            class="inline-flex items-center justify-center gap-2 border border-red-500/50 bg-red-500/5 text-red-300 hover:bg-red-500/10 px-4 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-35 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <rect x="6" y="6" width="12" height="12" rx="1.5"/>
                        </svg>
                        Stop Camera
                    </button>

                    <button id="captureImage" type="button" disabled
                            class="inline-flex items-center justify-center gap-2 border border-blue-400/30 text-blue-300 hover:text-blue-200 hover:bg-blue-500/10 px-4 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-35 disabled:cursor-not-allowed">
                        <span class="w-7 h-7 rounded-full bg-blue-500/15 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 7h3l1.5-2h7L17 7h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z"/>
                                <circle cx="12" cy="13" r="3.2"/>
                            </svg>
                        </span>
                        Capture
                    </button>
                </div>
            </section>

            <!-- COMPACT INFO BAR -->
            <section class="bg-zinc-900/80 border border-zinc-800 rounded-xl px-4 sm:px-5 py-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-0">
                    <div class="studio-stat sm:pr-5 flex items-center justify-between sm:justify-start sm:gap-3">
                        <span class="text-[11px] uppercase tracking-wider text-zinc-500">Resolution</span>
                        <span id="cameraResolution" class="text-sm font-semibold text-zinc-200">-</span>
                    </div>
                    <div class="studio-stat sm:px-5 flex items-center justify-between sm:justify-start sm:gap-3">
                        <span class="text-[11px] uppercase tracking-wider text-zinc-500">Processing FPS</span>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span><span id="cameraFPS">-</span></span>
                    </div>
                    <div class="studio-stat sm:pl-5 flex items-center justify-between sm:justify-start sm:gap-3 min-w-0">
                        <span class="text-[11px] uppercase tracking-wider text-zinc-500 shrink-0">Active Effect</span>
                        <span id="activeEffect" class="text-sm font-semibold text-blue-300 truncate">Original RGB</span>
                    </div>
                </div>
            </section>

            <!-- CONTEXTUAL PARAMETERS: MOVED UNDER CAMERA / ABOVE HISTOGRAM -->
            <section id="processingParameters" class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M4 21v-7m0-4V3m8 18v-9m0-4V3m8 18v-5m0-4V3M1 14h6m2-6h6m2 8h6"/>
                                </svg>
                            </span>
                            <h2 class="font-semibold text-sm">Processing Parameters</h2>
                        </div>
                        <p id="parameterDescription" class="text-xs text-zinc-500 mt-2">Pilih efek untuk melihat pengaturan yang relevan.</p>
                    </div>
                    <span class="text-[10px] uppercase tracking-wider text-zinc-500 bg-zinc-950 border border-zinc-800 rounded-full px-3 py-1 self-start">Context Controls</span>
                </div>

                <div id="parameterEmptyState" class="rounded-lg border border-dashed border-zinc-800 bg-zinc-950/40 px-4 py-5 text-center">
                    <p class="text-sm text-zinc-400">Efek ini tidak memerlukan parameter tambahan.</p>
                    <p class="text-xs text-zinc-600 mt-1">Pilih efek lain untuk menampilkan kontrol yang sesuai.</p>
                </div>

                <!-- COLOR CHANNEL -->
                <div id="colorChannelControl" data-parameter="color-channel" class="hidden rounded-lg border border-zinc-800 bg-zinc-950/50 p-4">
                    <label for="colorChannelSelect" class="block text-xs font-medium text-zinc-300 mb-2">Channel Preview</label>
                    <select id="colorChannelSelect" class="w-full sm:max-w-sm rounded-lg border border-zinc-700 bg-zinc-900 text-sm text-zinc-200 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="red">Red (R)</option>
                    </select>
                    <p class="text-xs leading-relaxed text-zinc-500 mt-2">Pilih channel untuk melihat distribusi nilainya dalam tampilan grayscale.</p>
                </div>

                <!-- THRESHOLD -->
                <div data-parameter="threshold" class="hidden rounded-lg border border-zinc-800 bg-zinc-950/50 p-4">
                    <div class="flex justify-between mb-3">
                        <label for="thresholdSlider" class="text-sm text-zinc-300">Threshold</label>
                        <span id="thresholdValue" class="text-sm font-semibold text-blue-400">127</span>
                    </div>
                    <input type="range" id="thresholdSlider" min="0" max="255" value="127" class="studio-range w-full cursor-pointer">
                    <div class="flex justify-between mt-2 text-xs text-zinc-600"><span>0</span><span>255</span></div>
                </div>

                <!-- KERNEL -->
                <div data-parameter="kernel" class="hidden rounded-lg border border-zinc-800 bg-zinc-950/50 p-4">
                    <div class="flex justify-between mb-3">
                        <label for="kernelSlider" class="text-sm text-zinc-300">Kernel Size</label>
                        <span id="kernelValue" class="text-sm font-semibold text-blue-400">5 × 5</span>
                    </div>
                    <input type="range" id="kernelSlider" min="3" max="21" step="2" value="5" class="studio-range w-full cursor-pointer">
                    <div class="flex justify-between mt-2 text-xs text-zinc-600"><span>3 × 3</span><span>21 × 21</span></div>
                    <p class="text-xs text-zinc-600 mt-2">Kernel ganjil yang lebih besar menghasilkan efek blur yang lebih kuat.</p>
                </div>

                <!-- BRIGHTNESS -->
                <div data-parameter="brightness" class="hidden rounded-lg border border-zinc-800 bg-zinc-950/50 p-4">
                    <div class="flex justify-between mb-2">
                        <label for="brightnessSlider" class="text-sm text-zinc-300">Brightness</label>
                        <span id="brightnessValue" class="text-sm font-semibold text-blue-400">0</span>
                    </div>
                    <input type="range" id="brightnessSlider" min="-100" max="100" value="0" step="1" class="studio-range w-full cursor-pointer">
                    <div class="flex justify-between mt-2 text-xs text-zinc-600"><span>-100</span><span>+100</span></div>
                </div>

                <!-- CONTRAST -->
                <div data-parameter="contrast" class="hidden rounded-lg border border-zinc-800 bg-zinc-950/50 p-4">
                    <div class="flex justify-between mb-2">
                        <label for="contrastSlider" class="text-sm text-zinc-300">Contrast</label>
                        <span id="contrastValue" class="text-sm font-semibold text-blue-400">1.0×</span>
                    </div>
                    <input type="range" id="contrastSlider" min="0.5" max="2.5" value="1" step="0.1" class="studio-range w-full cursor-pointer">
                    <div class="flex justify-between mt-2 text-xs text-zinc-600"><span>0.5×</span><span>2.5×</span></div>
                </div>

                <!-- GAMMA -->
                <div data-parameter="gamma" class="hidden rounded-lg border border-zinc-800 bg-zinc-950/50 p-4">
                    <div class="flex justify-between mb-2">
                        <label for="gammaSlider" class="text-sm text-zinc-300">Gamma</label>
                        <span id="gammaValue" class="text-sm font-semibold text-blue-400">1.0</span>
                    </div>
                    <input type="range" id="gammaSlider" min="0.2" max="3" value="1" step="0.1" class="studio-range w-full cursor-pointer">
                    <div class="flex justify-between mt-2 text-xs text-zinc-600"><span>0.2</span><span>3.0</span></div>
                    <p class="text-xs text-zinc-600 mt-2">Gamma 1.0 mempertahankan intensitas asli.</p>
                </div>

                <!-- CANNY -->
                <div data-parameter="canny" class="hidden rounded-lg border border-zinc-800 bg-zinc-950/50 p-4">
                    <p class="text-sm font-medium text-zinc-300 mb-1">Canny Thresholds</p>
                    <p class="text-xs text-zinc-500 mb-4">Ambang atas sebaiknya lebih tinggi daripada ambang bawah.</p>
                    <div class="mb-4">
                        <div class="flex justify-between mb-2">
                            <label for="cannyThreshold1" class="text-xs text-zinc-400">Lower Threshold</label>
                            <span id="cannyThreshold1Value" class="text-xs text-blue-400">50</span>
                        </div>
                        <input type="range" id="cannyThreshold1" min="0" max="255" value="50" class="studio-range w-full cursor-pointer">
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <label for="cannyThreshold2" class="text-xs text-zinc-400">Upper Threshold</label>
                            <span id="cannyThreshold2Value" class="text-xs text-blue-400">150</span>
                        </div>
                        <input type="range" id="cannyThreshold2" min="0" max="255" value="150" class="studio-range w-full cursor-pointer">
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-[11px] text-zinc-600">Perubahan parameter diterapkan secara real-time.</p>
                    <button id="resetEffects" type="button" class="inline-flex items-center gap-2 border border-zinc-700 hover:border-zinc-600 hover:bg-zinc-800 rounded-lg px-3 py-2 text-xs text-zinc-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"/><path d="M3 3v5h5"/>
                        </svg>
                        Reset Parameters
                    </button>
                </div>
            </section>

            <!-- HISTOGRAM -->
            <section class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="font-semibold">Image Histogram</h2>
                        <p class="text-xs text-zinc-500 mt-1">Distribusi intensitas piksel dari frame kamera sebelum efek diterapkan.</p>
                    </div>
                    <select id="histogramModeSelect" class="rounded-lg border border-zinc-700 bg-zinc-950 text-sm text-zinc-200 px-3 py-2">
                        <option value="rgb">RGB Histogram</option>
                        <option value="grayscale">Grayscale Histogram</option>
                    </select>
                </div>
                <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-2">
                    <canvas id="histogramCanvas" width="900" height="280" class="w-full h-auto rounded-md"></canvas>
                </div>
                <p id="histogramStatus" class="text-xs text-zinc-500 mt-3">Histogram akan tampil ketika kamera aktif.</p>
                <div class="flex flex-wrap gap-4 mt-3 text-xs text-zinc-400">
                    <span class="flex items-center gap-2"><i class="w-2 h-2 rounded-full bg-red-400"></i>Red</span>
                    <span class="flex items-center gap-2"><i class="w-2 h-2 rounded-full bg-green-400"></i>Green</span>
                    <span class="flex items-center gap-2"><i class="w-2 h-2 rounded-full bg-blue-400"></i>Blue</span>
                    <span class="text-zinc-500">X: intensitas (0–255) · Y: jumlah piksel relatif</span>
                </div>
            </section>

            <!-- ADVANCED IMAGE ANALYSIS -->
            <section id="advancedAnalysisPanel" class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-violet-500/10 text-violet-300 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M3 3v18h18M7 14l4-4 4 3 5-7"/>
                                </svg>
                            </span>
                            <h2 class="font-semibold text-sm">Advanced Image Analysis</h2>
                        </div>
                        <p class="text-xs text-zinc-500 mt-2">Perbandingan frame asli dan hasil pemrosesan, statistik piksel, serta inspeksi warna.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span id="analysisLiveBadge" class="inline-flex items-center gap-2 self-start rounded-full border border-zinc-700 bg-zinc-950 px-3 py-2 text-[10px] uppercase tracking-wider text-zinc-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span> Waiting for camera
                        </span>
                        <button id="downloadAnalysisReport" type="button" disabled
                                class="inline-flex items-center gap-2 rounded-lg border border-violet-500/40 bg-violet-500/10 px-3 py-2 text-xs font-semibold text-violet-200 transition hover:bg-violet-500/20 disabled:cursor-not-allowed disabled:opacity-35"
                                title="Unduh ringkasan analisis sebagai gambar PNG">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v3h16v-3"/>
                            </svg>
                            Download Report
                        </button>
                        <button id="downloadAnalysisCsv" type="button" disabled
                                class="inline-flex items-center gap-2 rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-xs font-semibold text-zinc-300 transition hover:border-zinc-500 hover:text-white disabled:cursor-not-allowed disabled:opacity-35"
                                title="Ekspor metrik dan histogram sebagai CSV">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 4h10l6 6v10H4zM14 4v6h6M8 14h8M8 17h8"/>
                            </svg>
                            Export CSV
                        </button>
                    </div>
                </div>

                <!-- ORIGINAL VS PROCESSED -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <figure class="min-w-0 rounded-xl border border-zinc-800 bg-zinc-950/70 p-3">
                        <figcaption class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-xs font-semibold text-zinc-200">Original Frame</span>
                            <span class="text-[10px] text-zinc-500">Source</span>
                        </figcaption>
                        <div class="relative overflow-hidden rounded-lg bg-black aspect-4/3">
                            <canvas id="analysisOriginalCanvas" width="640" height="480" class="hidden w-full h-full object-contain cursor-crosshair"></canvas>
                            <div id="analysisOriginalPlaceholder" class="absolute inset-0 flex items-center justify-center px-4 text-center text-xs text-zinc-600">Kamera belum aktif</div>
                        </div>
                        <p class="text-[10px] text-zinc-600 mt-2">Klik gambar untuk memeriksa nilai piksel.</p>
                    </figure>

                    <figure class="min-w-0 rounded-xl border border-zinc-800 bg-zinc-950/70 p-3">
                        <figcaption class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-xs font-semibold text-zinc-200">Processed Frame</span>
                            <span id="analysisProcessedEffect" class="text-[10px] text-blue-300">Original RGB</span>
                        </figcaption>
                        <div class="relative overflow-hidden rounded-lg bg-black aspect-4/3">
                            <canvas id="analysisProcessedCanvas" width="640" height="480" class="hidden w-full h-full object-contain"></canvas>
                            <div id="analysisProcessedPlaceholder" class="absolute inset-0 flex items-center justify-center px-4 text-center text-xs text-zinc-600">Hasil pemrosesan akan tampil di sini</div>
                        </div>
                        <p class="text-[10px] text-zinc-600 mt-2">Mengikuti efek dan parameter yang sedang aktif.</p>
                    </figure>
                </div>

                <!-- IMAGE METRICS -->
                <div class="mt-5">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Image Metrics</h3>
                        <span class="text-[10px] text-zinc-600">Dihitung dari sampel frame kamera</span>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950/60 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Resolution</p>
                            <p id="analysisResolution" class="text-sm font-semibold text-zinc-100 mt-1">-</p>
                        </div>
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950/60 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Total Pixels</p>
                            <p id="analysisPixelCount" class="text-sm font-semibold text-zinc-100 mt-1">-</p>
                        </div>
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950/60 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Mean Brightness</p>
                            <p id="analysisBrightness" class="text-sm font-semibold text-zinc-100 mt-1">-</p>
                        </div>
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950/60 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Contrast (Std. Dev.)</p>
                            <p id="analysisContrast" class="text-sm font-semibold text-zinc-100 mt-1">-</p>
                        </div>
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950/60 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Mean Saturation</p>
                            <p id="analysisSaturation" class="text-sm font-semibold text-zinc-100 mt-1">-</p>
                        </div>
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950/60 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Processed Mean</p>
                            <p id="analysisProcessedMean" class="text-sm font-semibold text-zinc-100 mt-1">-</p>
                        </div>
                    </div>
                </div>

                <!-- COLOR PROFILE -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-5">
                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/60 p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-3">Average RGB Balance</h3>
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <p id="analysisAverageRGB" class="text-sm font-semibold text-zinc-100">R: - · G: - · B: -</p>
                                <p class="text-[10px] text-zinc-600 mt-1">Rata-rata intensitas tiap kanal warna.</p>
                            </div>
                            <span id="analysisAverageSwatch" class="w-10 h-10 shrink-0 rounded-lg border border-white/10 bg-zinc-800" title="Average color"></span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-[10px]"><span class="w-3 text-red-400">R</span><div class="h-2 flex-1 rounded-full bg-zinc-800 overflow-hidden"><div id="analysisRedBar" class="h-full bg-red-400 rounded-full" style="width:0%"></div></div><span id="analysisRedValue" class="w-8 text-right text-zinc-500">0</span></div>
                            <div class="flex items-center gap-2 text-[10px]"><span class="w-3 text-green-400">G</span><div class="h-2 flex-1 rounded-full bg-zinc-800 overflow-hidden"><div id="analysisGreenBar" class="h-full bg-green-400 rounded-full" style="width:0%"></div></div><span id="analysisGreenValue" class="w-8 text-right text-zinc-500">0</span></div>
                            <div class="flex items-center gap-2 text-[10px]"><span class="w-3 text-blue-400">B</span><div class="h-2 flex-1 rounded-full bg-zinc-800 overflow-hidden"><div id="analysisBlueBar" class="h-full bg-blue-400 rounded-full" style="width:0%"></div></div><span id="analysisBlueValue" class="w-8 text-right text-zinc-500">0</span></div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/60 p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-3">Dominant Sampled Color</h3>
                        <div class="flex items-center gap-3">
                            <span id="analysisDominantSwatch" class="w-14 h-14 shrink-0 rounded-xl border border-white/10 bg-zinc-800"></span>
                            <div class="min-w-0">
                                <p id="analysisDominantHex" class="text-sm font-semibold text-zinc-100">-</p>
                                <p id="analysisDominantRGB" class="text-xs text-zinc-500 mt-1">RGB(-, -, -)</p>
                                <p class="text-[10px] text-zinc-600 mt-1">Perkiraan warna dominan berdasarkan kuantisasi warna.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PIXEL INSPECTOR -->
                <div class="mt-4 rounded-xl border border-zinc-800 bg-zinc-950/60 p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Pixel Inspector</h3>
                            <p class="text-[10px] text-zinc-600 mt-1">Klik titik pada Original Frame untuk membaca nilai warna piksel.</p>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div><p class="text-[10px] text-zinc-600">Position</p><p id="pixelPosition" class="font-semibold text-zinc-200">-</p></div>
                            <div><p class="text-[10px] text-zinc-600">RGB</p><p id="pixelRGB" class="font-semibold text-zinc-200">-</p></div>
                            <div><p class="text-[10px] text-zinc-600">HEX</p><p id="pixelHex" class="font-semibold text-zinc-200">-</p></div>
                            <div><p class="text-[10px] text-zinc-600">HSV</p><p id="pixelHSV" class="font-semibold text-zinc-200">-</p></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <p id="advancedAnalysisStatus" class="text-[10px] text-zinc-600">Analisis akan diperbarui ketika kamera aktif dan OpenCV berhasil memproses frame.</p>
                    <p id="analysisExportStatus" class="text-[10px] text-zinc-500" role="status" aria-live="polite">Unduhan tersedia setelah analisis pertama selesai.</p>
                </div>
            </section>
        </main>

        <!-- RIGHT COLUMN: STICKY EFFECT LIBRARY + CAPTURE HISTORY -->
        <!-- The aside itself is sticky so BOTH panels stay anchored while the page scrolls. -->
        <aside class="xl:col-span-4 min-w-0 studio-sidebar-scroll space-y-4 xl:sticky xl:top-5 xl:max-h-[calc(100vh-2.5rem)] xl:overflow-y-auto xl:overscroll-contain xl:pr-1">
            <section class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="font-semibold">Processing Effects</h2>
                        <p class="text-xs text-zinc-500 mt-1">Pilih kategori, lalu tentukan efeknya.</p>
                    </div>
                    <span class="text-xs bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full whitespace-nowrap">18 Effects</span>
                </div>

                <!-- ACCORDION: COLOR -->
                <div data-accordion-section class="border border-zinc-800 rounded-lg overflow-hidden mb-2">
                    <button type="button" data-accordion-trigger data-target="accordion-color" aria-expanded="true" class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left hover:bg-zinc-800/70 transition">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-300 flex items-center justify-center text-xs font-bold">01</span>
                            <span><span class="block text-sm font-medium text-zinc-200">Color Processing</span><span class="block text-[11px] text-zinc-500 mt-0.5">RGB, CMYK, HSV, grayscale</span></span>
                        </span>
                        <svg class="accordion-chevron text-zinc-500 shrink-0" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="accordion-color" data-accordion-panel class="px-3 pb-3 pt-1 space-y-1">
                        <button type="button" class="effect-button w-full text-left p-3 bg-blue-600 rounded-lg text-sm transition" data-effect="original">Original RGB</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="rgb">RGB Color Model</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="cmyk">CMYK Color Model</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="hsv">HSV / HSB Color Model</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="grayscale">Grayscale</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="binary">Binary Threshold</button>
                    </div>
                </div>

                <!-- ACCORDION: FILTERS -->
                <div data-accordion-section class="border border-zinc-800 rounded-lg overflow-hidden mb-2">
                    <button type="button" data-accordion-trigger data-target="accordion-filter" aria-expanded="false" class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left hover:bg-zinc-800/70 transition">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-300 flex items-center justify-center text-xs font-bold">02</span>
                            <span><span class="block text-sm font-medium text-zinc-200">Image Filtering</span><span class="block text-[11px] text-zinc-500 mt-0.5">Smoothing and noise reduction</span></span>
                        </span>
                        <svg class="accordion-chevron text-zinc-500 shrink-0" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="accordion-filter" data-accordion-panel class="hidden px-3 pb-3 pt-1 space-y-1">
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="gaussian">Gaussian Blur</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="median">Median Blur</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="bilateral">Bilateral Filter</button>
                    </div>
                </div>

                <!-- ACCORDION: EDGE -->
                <div data-accordion-section class="border border-zinc-800 rounded-lg overflow-hidden mb-2">
                    <button type="button" data-accordion-trigger data-target="accordion-edge" aria-expanded="false" class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left hover:bg-zinc-800/70 transition">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-300 flex items-center justify-center text-xs font-bold">03</span>
                            <span><span class="block text-sm font-medium text-zinc-200">Edge Detection</span><span class="block text-[11px] text-zinc-500 mt-0.5">Object boundaries and gradients</span></span>
                        </span>
                        <svg class="accordion-chevron text-zinc-500 shrink-0" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="accordion-edge" data-accordion-panel class="hidden px-3 pb-3 pt-1 space-y-1">
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="sobel">Sobel Edge Detection</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="prewitt">Prewitt Edge Detection</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="laplacian">Laplacian Edge Detection</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="canny">Canny Edge Detection</button>
                    </div>
                </div>

                <!-- ACCORDION: ENHANCEMENT -->
                <div data-accordion-section class="border border-zinc-800 rounded-lg overflow-hidden">
                    <button type="button" data-accordion-trigger data-target="accordion-enhancement" aria-expanded="false" class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left hover:bg-zinc-800/70 transition">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-300 flex items-center justify-center text-xs font-bold">04</span>
                            <span><span class="block text-sm font-medium text-zinc-200">Image Enhancement</span><span class="block text-[11px] text-zinc-500 mt-0.5">Brightness, contrast, histogram</span></span>
                        </span>
                        <svg class="accordion-chevron text-zinc-500 shrink-0" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="accordion-enhancement" data-accordion-panel class="hidden px-3 pb-3 pt-1 space-y-1">
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="equalize">Histogram Equalization</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="brightness">Brightness Adjustment</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="contrast">Contrast Adjustment</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="gamma">Gamma Correction</button>
                        <button type="button" class="effect-button w-full text-left p-3 hover:bg-zinc-800 rounded-lg text-sm transition" data-effect="otsu">Otsu Automatic Threshold</button>
                    </div>
                </div>

                <p class="text-[11px] leading-relaxed text-zinc-600 mt-4">Tips: memilih efek akan membuka kategori terkait dan menampilkan parameter yang sesuai di panel sebelah kiri.</p>
            </section>

            <!-- CAPTURE HISTORY: displayed below Processing Effects -->
            <section id="captureHistoryPanel" class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-100">Captured Results</h3>
                            <p class="text-[11px] text-zinc-500 mt-1">Hasil capture tersimpan sementara di sesi ini.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="captureCount" class="text-[10px] font-medium text-zinc-400 bg-zinc-800 rounded-full px-2.5 py-1">0</span>
                            <button id="clearCaptureHistory" type="button" disabled
                                    class="text-[11px] text-zinc-500 hover:text-red-300 disabled:opacity-30 disabled:cursor-not-allowed transition">
                                Clear
                            </button>
                        </div>
                    </div>

                    <div id="captureHistoryEmpty" class="rounded-lg border border-dashed border-zinc-800 bg-zinc-950/40 px-3 py-5 text-center">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-full bg-zinc-800/70 flex items-center justify-center text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M4 7h3l1.5-2h8L18 7h2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z"/><circle cx="12" cy="13" r="3"/>
                            </svg>
                        </div>
                        <p class="text-xs text-zinc-400">Belum ada hasil capture.</p>
                        <p class="text-[11px] text-zinc-600 mt-1">Tekan tombol Capture pada preview kamera.</p>
                    </div>

                <div id="captureHistoryList" class="hidden max-h-80 overflow-y-auto space-y-2 pr-1" aria-live="polite"></div>
            </section>
        </aside>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var Module = {
        onRuntimeInitialized() {
            console.log("OpenCV.js berhasil dimuat!");
            window.openCVReady = true;
            if (window.initializeImageLab) window.initializeImageLab();
        }
    };
</script>
<script async src="https://docs.opencv.org/4.x/opencv.js"></script>
<script src="{{ asset('js/camera.js') }}"></script>
@endpush
