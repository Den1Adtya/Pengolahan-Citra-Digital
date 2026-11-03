// =====================================================
// IMAGELAB LIVE - CAMERA + REAL-TIME IMAGE PROCESSING
// Fallback: kamera tetap terlihat meskipun OpenCV belum siap
// =====================================================

(() => {
    "use strict";

    const $ = (id) => document.getElementById(id);

    // Elemen halaman Studio
    const video = $("cameraVideo");
    const canvas = $("cameraCanvas");
    const placeholder = $("cameraPlaceholder");

    const startButton = $("startCamera");
    const stopButton = $("stopCamera");
    const captureButton = $("captureImage");

    const cameraStatus = $("cameraStatus");
    const cameraIndicator = $("cameraIndicator");
    const cameraResolution = $("cameraResolution");
    const cameraFPS = $("cameraFPS");
    const activeEffect = $("activeEffect");

    const captureHistoryList = $("captureHistoryList");
    const captureHistoryEmpty = $("captureHistoryEmpty");
    const captureCount = $("captureCount");
    const clearCaptureHistoryButton = $("clearCaptureHistory");

    const thresholdSlider = $("thresholdSlider");
    const thresholdValue = $("thresholdValue");
    const kernelSlider = $("kernelSlider");
    const kernelValue = $("kernelValue");
    const cannyThreshold1Slider = $("cannyThreshold1");
    const cannyThreshold1Value = $("cannyThreshold1Value");
    const cannyThreshold2Slider = $("cannyThreshold2");
    const cannyThreshold2Value = $("cannyThreshold2Value");
    const colorChannelControl = $("colorChannelControl");
    const colorChannelSelect = $("colorChannelSelect");
    const brightnessSlider = $("brightnessSlider");
    const brightnessValue = $("brightnessValue");
    const contrastSlider = $("contrastSlider");
    const contrastValue = $("contrastValue");
    const gammaSlider = $("gammaSlider");
    const gammaValue = $("gammaValue");
    const histogramCanvas = $("histogramCanvas");
    const histogramModeSelect = $("histogramModeSelect");
    const histogramStatus = $("histogramStatus");
    const analysisOriginalCanvas = $("analysisOriginalCanvas");
    const analysisProcessedCanvas = $("analysisProcessedCanvas");
    const analysisOriginalPlaceholder = $("analysisOriginalPlaceholder");
    const analysisProcessedPlaceholder = $("analysisProcessedPlaceholder");
    const analysisLiveBadge = $("analysisLiveBadge");
    const analysisStatus = $("advancedAnalysisStatus");
    const analysisExportStatus = $("analysisExportStatus");
    const downloadAnalysisReportButton = $("downloadAnalysisReport");
    const downloadAnalysisCsvButton = $("downloadAnalysisCsv");
    const analysisProcessedEffect = $("analysisProcessedEffect");
    const pixelPosition = $("pixelPosition");
    const pixelRGB = $("pixelRGB");
    const pixelHex = $("pixelHex");
    const pixelHSV = $("pixelHSV");
    const resetButton = $("resetEffects");

    if (!video || !canvas || !startButton || !stopButton || !captureButton) {
        console.error("ImageLab Live: elemen utama Studio tidak ditemukan. Periksa ID di studio.blade.php.");
        return;
    }

    const analysisElements = {
        resolution: $("analysisResolution"),
        pixelCount: $("analysisPixelCount"),
        brightness: $("analysisBrightness"),
        contrast: $("analysisContrast"),
        saturation: $("analysisSaturation"),
        processedMean: $("analysisProcessedMean"),
        averageRGB: $("analysisAverageRGB"),
        averageSwatch: $("analysisAverageSwatch"),
        redBar: $("analysisRedBar"),
        greenBar: $("analysisGreenBar"),
        blueBar: $("analysisBlueBar"),
        redValue: $("analysisRedValue"),
        greenValue: $("analysisGreenValue"),
        blueValue: $("analysisBlueValue"),
        dominantSwatch: $("analysisDominantSwatch"),
        dominantHex: $("analysisDominantHex"),
        dominantRGB: $("analysisDominantRGB")
    };

    // Canvas perantara khusus untuk membaca frame kamera oleh OpenCV.
    const sourceCanvas = document.createElement("canvas");
    const sourceContext = sourceCanvas.getContext("2d", { willReadFrequently: true });

    let stream = null;
    let animationFrameId = null;
    let isRunning = false;
    let currentEffect = "original";
    const selectedColorChannels = {
        rgb: "red",
        cmyk: "cyan",
        hsv: "hue"
    };
    let lastFpsTime = 0;
    let fpsFrames = 0;
    const captureHistory = [];
    const MAX_CAPTURE_HISTORY = 8;
    let captureSequence = 0;
    let hasProcessedFrame = false;
    let lastProcessingError = "";
    let lastHistogramUpdate = 0;
    let lastAdvancedAnalysisUpdate = 0;
    let hasAnalysisSnapshot = false;

    const effectButtons = Array.from(document.querySelectorAll(".effect-button"));
    const parameterGroups = Array.from(document.querySelectorAll("[data-parameter]"));
    const parameterEmptyState = $("parameterEmptyState");
    const parameterDescription = $("parameterDescription");
    const accordionSections = Array.from(document.querySelectorAll("[data-accordion-section]"));

    // -----------------------------------------------------
    // OpenCV readiness
    // -----------------------------------------------------

    function isOpenCVReady() {
        return Boolean(window.cv && typeof window.cv.Mat === "function");
    }

    window.initializeImageLab = function () {
        if (isOpenCVReady()) {
            window.openCVReady = true;
            console.info("ImageLab Live: OpenCV.js siap digunakan.");
        } else {
            console.info("ImageLab Live: callback OpenCV diterima, tetapi runtime belum siap.");
        }
    };

    // Jika OpenCV ternyata sudah selesai dimuat sebelum file ini berjalan.
    if (isOpenCVReady()) {
        window.initializeImageLab();
    }

    // -----------------------------------------------------
    // UI helpers
    // -----------------------------------------------------

    function setStatus(text, active) {
        if (cameraStatus) cameraStatus.textContent = text;
        if (cameraIndicator) {
            cameraIndicator.classList.toggle("bg-green-500", Boolean(active));
            cameraIndicator.classList.toggle("bg-red-500", !active);
        }
    }

    function showVideoPreview() {
        // Video adalah preview utama sampai OpenCV berhasil merender frame.
        video.classList.remove("hidden");
        video.classList.add("w-full", "h-full", "object-contain");
        canvas.classList.add("hidden");
        if (placeholder) placeholder.classList.add("hidden");
    }

    function showProcessedCanvas() {
        canvas.classList.remove("hidden");
        canvas.classList.add("w-full", "h-full", "object-contain");
        video.classList.add("hidden");
        if (placeholder) placeholder.classList.add("hidden");
    }

    function setButtons(active) {
        startButton.disabled = Boolean(active);
        stopButton.disabled = !active;
        captureButton.disabled = !active;
    }

    // -----------------------------------------------------
    // Wait for a usable camera frame
    // -----------------------------------------------------

    function waitForVideoFrame(timeoutMs = 10000) {
        return new Promise((resolve, reject) => {
            const startedAt = performance.now();

            function check() {
                if (!isRunning && !stream) {
                    reject(new Error("Kamera dihentikan sebelum frame tersedia."));
                    return;
                }

                if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA && video.videoWidth > 0 && video.videoHeight > 0) {
                    resolve();
                    return;
                }

                if (performance.now() - startedAt > timeoutMs) {
                    reject(new Error("Kamera tidak mengirim frame dalam 10 detik."));
                    return;
                }

                setTimeout(check, 50);
            }

            check();
        });
    }

    // -----------------------------------------------------
    // Start camera
    // -----------------------------------------------------

    async function startCamera() {
        if (stream) return;

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert("Browser tidak menyediakan akses kamera. Buka melalui localhost atau HTTPS dan gunakan browser yang mendukung kamera.");
            return;
        }

        startButton.disabled = true;
        setStatus("Menghubungkan kamera...", false);

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: "user"
                }
            });

            video.muted = true;
            video.autoplay = true;
            video.playsInline = true;
            video.srcObject = stream;

            await video.play();

            // Tandai aktif sebelum menunggu frame agar fungsi wait dapat berjalan.
            isRunning = true;
            await waitForVideoFrame();

            const width = video.videoWidth;
            const height = video.videoHeight;

            canvas.width = width;
            canvas.height = height;
            sourceCanvas.width = width;
            sourceCanvas.height = height;

            showVideoPreview();
            setStatus(isOpenCVReady() ? "Camera Active" : "Camera Active · Preview Mode", true);
            setButtons(true);

            if (cameraResolution) cameraResolution.textContent = `${width} × ${height}`;
            if (cameraFPS) cameraFPS.textContent = "0";

            hasProcessedFrame = false;
            lastFpsTime = performance.now();
            fpsFrames = 0;

            console.info("ImageLab Live: kamera aktif", { width, height, openCVReady: isOpenCVReady() });
            startProcessingLoop();

        } catch (error) {
            console.error("ImageLab Live - gagal memulai kamera:", error);

            isRunning = false;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            video.srcObject = null;
            setButtons(false);
            setStatus("Camera Offline", false);

            let message = "Tidak dapat menampilkan kamera.";
            if (error && error.name === "NotAllowedError") {
                message = "Izin kamera ditolak. Izinkan akses kamera melalui pengaturan browser.";
            } else if (error && error.name === "NotFoundError") {
                message = "Kamera tidak ditemukan. Periksa webcam atau perangkat kamera.";
            } else if (error && error.name === "NotReadableError") {
                message = "Kamera sedang digunakan aplikasi lain. Tutup aplikasi tersebut lalu coba lagi.";
            } else if (error && error.message) {
                message += " Detail: " + error.message;
            }
            alert(message);
        }
    }

    // -----------------------------------------------------
    // Stop camera
    // -----------------------------------------------------

    function stopCamera() {
        isRunning = false;

        if (animationFrameId !== null) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }

        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        video.pause();
        video.srcObject = null;
        video.classList.add("hidden");
        canvas.classList.add("hidden");
        if (placeholder) placeholder.classList.remove("hidden");

        setButtons(false);
        setStatus("Camera Offline", false);
        if (cameraResolution) cameraResolution.textContent = "-";
        if (cameraFPS) cameraFPS.textContent = "-";

        hasProcessedFrame = false;
        lastProcessingError = "";
        lastAdvancedAnalysisUpdate = 0;
        if (analysisLiveBadge) {
            analysisLiveBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span> Camera offline';
            analysisLiveBadge.classList.remove("text-emerald-300", "border-emerald-500/30");
            analysisLiveBadge.classList.add("text-zinc-500", "border-zinc-700");
        }
        if (analysisStatus) analysisStatus.textContent = "Kamera dihentikan. Data analisis terakhir tetap ditampilkan sebagai frame terakhir.";
    }

    // -----------------------------------------------------
    // Histogram visualization (calculated from original camera frame)
    // -----------------------------------------------------

    function drawHistogram(src) {
        if (!histogramCanvas || !histogramModeSelect) return;

        const now = performance.now();
        // Histogram diperbarui maksimal sekitar 4 kali per detik agar kamera tetap ringan.
        if (now - lastHistogramUpdate < 250) return;
        lastHistogramUpdate = now;

        const context = histogramCanvas.getContext("2d");
        if (!context) return;

        const width = histogramCanvas.width;
        const height = histogramCanvas.height;
        const padding = { top: 14, right: 12, bottom: 28, left: 12 };
        const plotWidth = width - padding.left - padding.right;
        const plotHeight = height - padding.top - padding.bottom;
        const bins = 256;
        const mode = histogramModeSelect.value || "rgb";
        const red = new Uint32Array(bins);
        const green = new Uint32Array(bins);
        const blue = new Uint32Array(bins);
        const gray = new Uint32Array(bins);
        const pixels = src.data;
        const pixelCount = src.rows * src.cols;
        const sampleStep = Math.max(1, Math.floor(pixelCount / 120000));
        let samples = 0;

        for (let pixel = 0; pixel < pixelCount; pixel += sampleStep) {
            const offset = pixel * 4;
            const r = pixels[offset];
            const g = pixels[offset + 1];
            const b = pixels[offset + 2];
            red[r]++;
            green[g]++;
            blue[b]++;
            const luminance = Math.max(0, Math.min(255, Math.round(0.299 * r + 0.587 * g + 0.114 * b)));
            gray[luminance]++;
            samples++;
        }

        context.clearRect(0, 0, width, height);
        context.fillStyle = "#09090b";
        context.fillRect(0, 0, width, height);

        // Grid horizontal
        context.strokeStyle = "rgba(161, 161, 170, 0.18)";
        context.lineWidth = 1;
        for (let line = 0; line <= 4; line++) {
            const y = padding.top + (plotHeight * line / 4);
            context.beginPath();
            context.moveTo(padding.left, y);
            context.lineTo(width - padding.right, y);
            context.stroke();
        }

        const series = mode === "grayscale"
            ? [{ data: gray, color: "rgba(228, 228, 231, 0.78)", fill: "rgba(212, 212, 216, 0.20)" }]
            : [
                { data: red, color: "#f87171", fill: "rgba(248, 113, 113, 0.10)" },
                { data: green, color: "#4ade80", fill: "rgba(74, 222, 128, 0.10)" },
                { data: blue, color: "#60a5fa", fill: "rgba(96, 165, 250, 0.10)" }
            ];

        let maxCount = 1;
        for (const item of series) {
            for (let i = 0; i < bins; i++) maxCount = Math.max(maxCount, item.data[i]);
        }

        for (const item of series) {
            context.beginPath();
            for (let i = 0; i < bins; i++) {
                const x = padding.left + (i / (bins - 1)) * plotWidth;
                const y = padding.top + plotHeight - (item.data[i] / maxCount) * plotHeight;
                if (i === 0) context.moveTo(x, y);
                else context.lineTo(x, y);
            }
            context.strokeStyle = item.color;
            context.lineWidth = mode === "grayscale" ? 1.7 : 1.35;
            context.stroke();
        }

        context.fillStyle = "#a1a1aa";
        context.font = "11px sans-serif";
        context.textAlign = "left";
        context.fillText("0", padding.left, height - 8);
        context.textAlign = "center";
        context.fillText("Intensity", width / 2, height - 8);
        context.textAlign = "right";
        context.fillText("255", width - padding.right, height - 8);

        if (histogramStatus) {
            histogramStatus.textContent = `${mode === "grayscale" ? "Grayscale" : "RGB"} histogram · ${samples.toLocaleString("id-ID")} sampled pixels`;
        }
    }

    // -----------------------------------------------------
    // Advanced Image Analysis
    // -----------------------------------------------------

    function toHexColor(red, green, blue) {
        return "#" + [red, green, blue]
            .map(value => Math.max(0, Math.min(255, Math.round(value))).toString(16).padStart(2, "0"))
            .join("")
            .toUpperCase();
    }

    function updateAdvancedImageAnalysis(src, dst) {
        if (!analysisOriginalCanvas || !analysisProcessedCanvas) return;

        const now = performance.now();
        // Analisis dibuat sekitar dua kali per detik agar tidak membebani loop kamera.
        if (now - lastAdvancedAnalysisUpdate < 500) return;
        lastAdvancedAnalysisUpdate = now;

        const width = src.cols;
        const height = src.rows;
        const pixelCount = width * height;

        if (analysisOriginalCanvas.width !== width || analysisOriginalCanvas.height !== height) {
            analysisOriginalCanvas.width = width;
            analysisOriginalCanvas.height = height;
        }
        if (analysisProcessedCanvas.width !== width || analysisProcessedCanvas.height !== height) {
            analysisProcessedCanvas.width = width;
            analysisProcessedCanvas.height = height;
        }

        const originalContext = analysisOriginalCanvas.getContext("2d", { willReadFrequently: true });
        if (originalContext) {
            originalContext.drawImage(sourceCanvas, 0, 0, width, height);
        }

        // Simpan hasil algoritma yang sama dengan tampilan preview utama.
        window.cv.imshow(analysisProcessedCanvas, dst);
        analysisOriginalCanvas.classList.remove("hidden");
        analysisProcessedCanvas.classList.remove("hidden");
        if (analysisOriginalPlaceholder) analysisOriginalPlaceholder.classList.add("hidden");
        if (analysisProcessedPlaceholder) analysisProcessedPlaceholder.classList.add("hidden");

        hasAnalysisSnapshot = true;
        if (downloadAnalysisReportButton) downloadAnalysisReportButton.disabled = false;
        if (downloadAnalysisCsvButton) downloadAnalysisCsvButton.disabled = false;
        if (analysisExportStatus && analysisExportStatus.textContent.includes("tersedia setelah")) {
            analysisExportStatus.textContent = "Analisis siap diunduh sebagai PNG atau CSV.";
        }

        const rgba = src.data;
        // Batasi sampel maksimal sekitar 24 ribu piksel untuk menjaga performa.
        const sampleStep = Math.max(1, Math.floor(pixelCount / 24000));
        let samples = 0;
        let sumR = 0;
        let sumG = 0;
        let sumB = 0;
        let sumLuma = 0;
        let sumLumaSquared = 0;
        let sumSaturation = 0;
        const colorBins = new Uint32Array(512); // 8 x 8 x 8 color quantization

        for (let pixel = 0; pixel < pixelCount; pixel += sampleStep) {
            const offset = pixel * 4;
            const red = rgba[offset];
            const green = rgba[offset + 1];
            const blue = rgba[offset + 2];
            const luma = 0.299 * red + 0.587 * green + 0.114 * blue;
            const maxChannel = Math.max(red, green, blue);
            const minChannel = Math.min(red, green, blue);
            const saturation = maxChannel === 0 ? 0 : ((maxChannel - minChannel) / maxChannel) * 100;

            sumR += red;
            sumG += green;
            sumB += blue;
            sumLuma += luma;
            sumLumaSquared += luma * luma;
            sumSaturation += saturation;

            const binIndex = ((red >> 5) * 64) + ((green >> 5) * 8) + (blue >> 5);
            colorBins[binIndex]++;
            samples++;
        }

        if (!samples) return;

        const meanR = sumR / samples;
        const meanG = sumG / samples;
        const meanB = sumB / samples;
        const meanLuma = sumLuma / samples;
        const variance = Math.max(0, (sumLumaSquared / samples) - (meanLuma * meanLuma));
        const standardDeviation = Math.sqrt(variance);
        const meanSaturation = sumSaturation / samples;
        const totalChannelMean = meanR + meanG + meanB || 1;
        const redShare = (meanR / totalChannelMean) * 100;
        const greenShare = (meanG / totalChannelMean) * 100;
        const blueShare = (meanB / totalChannelMean) * 100;

        let dominantIndex = 0;
        for (let index = 1; index < colorBins.length; index++) {
            if (colorBins[index] > colorBins[dominantIndex]) dominantIndex = index;
        }
        const dominantRed = (((dominantIndex >> 6) & 7) * 32) + 16;
        const dominantGreen = (((dominantIndex >> 3) & 7) * 32) + 16;
        const dominantBlue = ((dominantIndex & 7) * 32) + 16;
        const dominantHex = toHexColor(dominantRed, dominantGreen, dominantBlue);
        const averageHex = toHexColor(meanR, meanG, meanB);

        const setText = (element, value) => {
            if (element) element.textContent = value;
        };
        setText(analysisElements.resolution, `${width} × ${height}`);
        setText(analysisElements.pixelCount, pixelCount.toLocaleString("id-ID"));
        setText(analysisElements.brightness, `${meanLuma.toFixed(1)} / 255`);
        setText(analysisElements.contrast, `${standardDeviation.toFixed(1)} / 128`);
        setText(analysisElements.saturation, `${meanSaturation.toFixed(1)}%`);
        setText(analysisElements.averageRGB, `R: ${meanR.toFixed(0)} · G: ${meanG.toFixed(0)} · B: ${meanB.toFixed(0)}`);
        setText(analysisElements.dominantHex, dominantHex);
        setText(analysisElements.dominantRGB, `RGB(${dominantRed}, ${dominantGreen}, ${dominantBlue})`);

        if (analysisElements.averageSwatch) analysisElements.averageSwatch.style.backgroundColor = averageHex;
        if (analysisElements.dominantSwatch) analysisElements.dominantSwatch.style.backgroundColor = dominantHex;

        const updateBar = (bar, valueElement, share, channelMean) => {
            if (bar) bar.style.width = `${share.toFixed(1)}%`;
            if (valueElement) valueElement.textContent = channelMean.toFixed(0);
        };
        updateBar(analysisElements.redBar, analysisElements.redValue, redShare, meanR);
        updateBar(analysisElements.greenBar, analysisElements.greenValue, greenShare, meanG);
        updateBar(analysisElements.blueBar, analysisElements.blueValue, blueShare, meanB);

        // Hitung rerata intensitas output setelah efek aktif diterapkan.
        const outputData = dst.data;
        const outputChannels = dst.channels();
        const outputPixelCount = dst.rows * dst.cols;
        const outputStep = Math.max(1, Math.floor(outputPixelCount / 12000));
        let outputSum = 0;
        let outputSamples = 0;
        for (let pixel = 0; pixel < outputPixelCount; pixel += outputStep) {
            const offset = pixel * outputChannels;
            let intensity;
            if (outputChannels === 1) {
                intensity = outputData[offset];
            } else {
                const first = outputData[offset];
                const second = outputData[offset + 1];
                const third = outputData[offset + 2];
                intensity = 0.299 * first + 0.587 * second + 0.114 * third;
            }
            outputSum += intensity;
            outputSamples++;
        }
        const outputMean = outputSamples ? outputSum / outputSamples : 0;
        setText(analysisElements.processedMean, `${outputMean.toFixed(1)} / 255`);
        setText(analysisProcessedEffect, activeEffect ? activeEffect.textContent.trim() : currentEffect);

        if (analysisLiveBadge) {
            analysisLiveBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live analysis';
            analysisLiveBadge.classList.remove("text-zinc-500", "border-zinc-700");
            analysisLiveBadge.classList.add("text-emerald-300", "border-emerald-500/30");
        }
        if (analysisStatus) {
            analysisStatus.textContent = `Diperbarui otomatis · ${samples.toLocaleString("id-ID")} sampel dari ${pixelCount.toLocaleString("id-ID")} piksel · statistik warna berdasarkan frame asli.`;
        }
    }

    function rgbToHsv(red, green, blue) {
        const r = red / 255;
        const g = green / 255;
        const b = blue / 255;
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        const delta = max - min;
        let hue = 0;

        if (delta !== 0) {
            if (max === r) hue = 60 * (((g - b) / delta) % 6);
            else if (max === g) hue = 60 * (((b - r) / delta) + 2);
            else hue = 60 * (((r - g) / delta) + 4);
        }
        if (hue < 0) hue += 360;
        const saturation = max === 0 ? 0 : (delta / max) * 100;
        return { hue, saturation, value: max * 100 };
    }

    function inspectOriginalPixel(event) {
        if (!analysisOriginalCanvas || analysisOriginalCanvas.classList.contains("hidden")) return;
        const rect = analysisOriginalCanvas.getBoundingClientRect();
        if (!rect.width || !rect.height) return;

        const x = Math.max(0, Math.min(analysisOriginalCanvas.width - 1,
            Math.floor((event.clientX - rect.left) * analysisOriginalCanvas.width / rect.width)));
        const y = Math.max(0, Math.min(analysisOriginalCanvas.height - 1,
            Math.floor((event.clientY - rect.top) * analysisOriginalCanvas.height / rect.height)));

        const context = analysisOriginalCanvas.getContext("2d", { willReadFrequently: true });
        if (!context) return;
        const pixel = context.getImageData(x, y, 1, 1).data;
        const red = pixel[0];
        const green = pixel[1];
        const blue = pixel[2];
        const hsv = rgbToHsv(red, green, blue);

        if (pixelPosition) pixelPosition.textContent = `(${x}, ${y})`;
        if (pixelRGB) pixelRGB.textContent = `${red}, ${green}, ${blue}`;
        if (pixelHex) pixelHex.textContent = toHexColor(red, green, blue);
        if (pixelHSV) pixelHSV.textContent = `${Math.round(hsv.hue)}°, ${Math.round(hsv.saturation)}%, ${Math.round(hsv.value)}%`;
    }

    if (analysisOriginalCanvas) {
        analysisOriginalCanvas.addEventListener("click", inspectOriginalPixel);
    }

    // -----------------------------------------------------
    // Download Advanced Image Analysis (PNG report + CSV data)
    // -----------------------------------------------------

    function getAnalysisText(id) {
        const element = $(id);
        return element ? element.textContent.trim() : "-";
    }

    function getSelectedOptionText(id) {
        const select = $(id);
        if (!select) return "-";
        const selected = select.selectedOptions && select.selectedOptions[0];
        return selected ? selected.textContent.trim() : select.value;
    }

    function makeAnalysisFileName(extension) {
        const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
        return `imagelab-analysis-${timestamp}.${extension}`;
    }

    function downloadBlob(blob, fileName) {
        if (!blob) throw new Error("File hasil ekspor tidak berhasil dibuat.");
        const url = URL.createObjectURL(blob);
        const anchor = document.createElement("a");
        anchor.href = url;
        anchor.download = fileName;
        anchor.style.display = "none";
        document.body.appendChild(anchor);
        anchor.click();
        anchor.remove();
        window.setTimeout(() => URL.revokeObjectURL(url), 1500);
    }

    function drawImageContained(context, source, x, y, maxWidth, maxHeight) {
        if (!source || !source.width || !source.height) return;
        const scale = Math.min(maxWidth / source.width, maxHeight / source.height);
        const drawWidth = source.width * scale;
        const drawHeight = source.height * scale;
        const drawX = x + (maxWidth - drawWidth) / 2;
        const drawY = y + (maxHeight - drawHeight) / 2;
        context.drawImage(source, drawX, drawY, drawWidth, drawHeight);
    }

    function drawReportCard(context, x, y, width, height, title, value, accent = "#a1a1aa") {
        context.fillStyle = "#18181b";
        context.fillRect(x, y, width, height);
        context.strokeStyle = "#3f3f46";
        context.lineWidth = 2;
        context.strokeRect(x, y, width, height);
        context.fillStyle = "#a1a1aa";
        context.font = "600 22px Arial, sans-serif";
        context.textAlign = "left";
        context.fillText(title.toUpperCase(), x + 22, y + 34);
        context.fillStyle = accent;
        context.font = "600 31px Arial, sans-serif";
        context.fillText(String(value || "-"), x + 22, y + 76, width - 44);
    }

    function downloadAnalysisReport() {
        if (!hasAnalysisSnapshot || !analysisOriginalCanvas || !analysisProcessedCanvas) {
            alert("Aktifkan kamera dan tunggu analisis pertama selesai sebelum mengunduh laporan.");
            return;
        }

        const report = document.createElement("canvas");
        report.width = 1800;
        report.height = 1540;
        const context = report.getContext("2d");
        if (!context) throw new Error("Canvas untuk laporan tidak dapat dibuat.");

        const width = report.width;
        context.fillStyle = "#09090b";
        context.fillRect(0, 0, report.width, report.height);

        // Report heading
        context.fillStyle = "#f4f4f5";
        context.font = "700 48px Arial, sans-serif";
        context.textAlign = "left";
        context.fillText("IMAGELAB LIVE", 70, 82);
        context.fillStyle = "#c4b5fd";
        context.font = "600 28px Arial, sans-serif";
        context.fillText("ADVANCED IMAGE ANALYSIS REPORT", 70, 130);
        context.fillStyle = "#a1a1aa";
        context.font = "20px Arial, sans-serif";
        context.textAlign = "right";
        context.fillText(new Date().toLocaleString("id-ID"), width - 70, 82);
        context.fillText(`Active Effect: ${getAnalysisText("analysisProcessedEffect")}`, width - 70, 122);
        context.strokeStyle = "#3f3f46";
        context.lineWidth = 2;
        context.beginPath();
        context.moveTo(70, 160);
        context.lineTo(width - 70, 160);
        context.stroke();

        // Original and processed frames
        const imageTop = 195;
        const imageCardWidth = 810;
        const imageCardHeight = 485;
        const imageX1 = 70;
        const imageX2 = 920;
        [imageX1, imageX2].forEach(x => {
            context.fillStyle = "#18181b";
            context.fillRect(x, imageTop, imageCardWidth, imageCardHeight);
            context.strokeStyle = "#3f3f46";
            context.strokeRect(x, imageTop, imageCardWidth, imageCardHeight);
        });
        context.textAlign = "left";
        context.font = "600 25px Arial, sans-serif";
        context.fillStyle = "#e4e4e7";
        context.fillText("ORIGINAL FRAME", imageX1 + 22, imageTop + 42);
        context.fillText("PROCESSED FRAME", imageX2 + 22, imageTop + 42);
        context.fillStyle = "#09090b";
        context.fillRect(imageX1 + 20, imageTop + 62, imageCardWidth - 40, 395);
        context.fillRect(imageX2 + 20, imageTop + 62, imageCardWidth - 40, 395);
        drawImageContained(context, analysisOriginalCanvas, imageX1 + 25, imageTop + 67, imageCardWidth - 50, 385);
        drawImageContained(context, analysisProcessedCanvas, imageX2 + 25, imageTop + 67, imageCardWidth - 50, 385);

        // Metrics grid
        context.fillStyle = "#f4f4f5";
        context.font = "700 27px Arial, sans-serif";
        context.textAlign = "left";
        context.fillText("IMAGE METRICS", 70, 735);
        const metrics = [
            ["Resolution", getAnalysisText("analysisResolution"), "#e4e4e7"],
            ["Total Pixels", getAnalysisText("analysisPixelCount"), "#e4e4e7"],
            ["Mean Brightness", getAnalysisText("analysisBrightness"), "#facc15"],
            ["Contrast (Std. Dev.)", getAnalysisText("analysisContrast"), "#93c5fd"],
            ["Mean Saturation", getAnalysisText("analysisSaturation"), "#c4b5fd"],
            ["Processed Mean", getAnalysisText("analysisProcessedMean"), "#6ee7b7"]
        ];
        const metricX = [70, 650, 1230];
        const metricY = [770, 888];
        metrics.forEach((metric, index) => {
            drawReportCard(context, metricX[index % 3], metricY[Math.floor(index / 3)], 540, 96, metric[0], metric[1], metric[2]);
        });

        // Histogram and color profile panels
        const panelY = 1025;
        const panelHeight = 420;
        context.fillStyle = "#18181b";
        context.fillRect(70, panelY, 960, panelHeight);
        context.fillRect(1060, panelY, 670, panelHeight);
        context.strokeStyle = "#3f3f46";
        context.strokeRect(70, panelY, 960, panelHeight);
        context.strokeRect(1060, panelY, 670, panelHeight);
        context.fillStyle = "#e4e4e7";
        context.font = "600 25px Arial, sans-serif";
        context.textAlign = "left";
        context.fillText(`HISTOGRAM · ${getSelectedOptionText("histogramModeSelect")}`, 95, panelY + 42);
        if (histogramCanvas && histogramCanvas.width && histogramCanvas.height) {
            drawImageContained(context, histogramCanvas, 95, panelY + 65, 910, 260);
        } else {
            context.fillStyle = "#a1a1aa";
            context.font = "20px Arial, sans-serif";
            context.fillText("Histogram belum tersedia.", 95, panelY + 120);
        }
        context.fillStyle = "#a1a1aa";
        context.font = "19px Arial, sans-serif";
        context.fillText(getAnalysisText("histogramStatus"), 95, panelY + 370);

        context.fillStyle = "#e4e4e7";
        context.font = "600 25px Arial, sans-serif";
        context.fillText("COLOR PROFILE & PIXEL INSPECTOR", 1085, panelY + 42);
        context.fillStyle = "#a1a1aa";
        context.font = "20px Arial, sans-serif";
        context.fillText("Average RGB", 1085, panelY + 95);
        context.fillStyle = "#f4f4f5";
        context.font = "600 25px Arial, sans-serif";
        context.fillText(getAnalysisText("analysisAverageRGB"), 1085, panelY + 132);
        const averageSwatch = analysisElements.averageSwatch;
        if (averageSwatch) {
            context.fillStyle = getComputedStyle(averageSwatch).backgroundColor || "#3f3f46";
            context.fillRect(1650, panelY + 105, 48, 48);
        }
        context.fillStyle = "#a1a1aa";
        context.font = "20px Arial, sans-serif";
        context.fillText("Dominant Sampled Color", 1085, panelY + 190);
        context.fillStyle = "#f4f4f5";
        context.font = "600 26px Arial, sans-serif";
        context.fillText(getAnalysisText("analysisDominantHex"), 1085, panelY + 230);
        context.font = "20px Arial, sans-serif";
        context.fillStyle = "#a1a1aa";
        context.fillText(getAnalysisText("analysisDominantRGB"), 1085, panelY + 260);
        const dominantSwatch = analysisElements.dominantSwatch;
        if (dominantSwatch) {
            context.fillStyle = getComputedStyle(dominantSwatch).backgroundColor || "#3f3f46";
            context.fillRect(1650, panelY + 200, 48, 48);
        }
        context.strokeStyle = "#3f3f46";
        context.beginPath();
        context.moveTo(1085, panelY + 290);
        context.lineTo(1705, panelY + 290);
        context.stroke();
        context.fillStyle = "#a1a1aa";
        context.font = "20px Arial, sans-serif";
        context.fillText("Pixel Inspector", 1085, panelY + 328);
        context.fillStyle = "#e4e4e7";
        context.font = "18px Arial, sans-serif";
        context.fillText(`Position: ${getAnalysisText("pixelPosition")}`, 1085, panelY + 360);
        context.fillText(`RGB: ${getAnalysisText("pixelRGB")}   HEX: ${getAnalysisText("pixelHex")}`, 1085, panelY + 390);
        context.fillStyle = "#a1a1aa";
        context.font = "16px Arial, sans-serif";
        context.fillText(`HSV: ${getAnalysisText("pixelHSV")}`, 1460, panelY + 390);

        // Footer
        context.fillStyle = "#71717a";
        context.font = "17px Arial, sans-serif";
        context.textAlign = "left";
        context.fillText("Generated locally by ImageLab Live · Statistics are based on sampled pixels.", 70, 1490);

        report.toBlob(blob => {
            try {
                downloadBlob(blob, makeAnalysisFileName("png"));
                if (analysisExportStatus) analysisExportStatus.textContent = "Laporan PNG berhasil dibuat.";
            } catch (error) {
                console.error("ImageLab Live - export report failed:", error);
                if (analysisExportStatus) analysisExportStatus.textContent = "Gagal membuat laporan PNG.";
                alert("Laporan analisis gagal diunduh. Periksa Console browser.");
            }
        }, "image/png");
    }

    function csvEscape(value) {
        const text = String(value ?? "");
        return `"${text.replace(/"/g, '""')}"`;
    }

    function downloadAnalysisCsv() {
        if (!hasAnalysisSnapshot || !analysisOriginalCanvas) {
            alert("Aktifkan kamera dan tunggu analisis pertama selesai sebelum mengekspor data.");
            return;
        }

        const rows = [["Category", "Metric", "Value"]];
        const summary = [
            ["General", "Generated At", new Date().toLocaleString("id-ID")],
            ["General", "Active Effect", getAnalysisText("analysisProcessedEffect")],
            ["General", "Resolution", getAnalysisText("analysisResolution")],
            ["General", "Total Pixels", getAnalysisText("analysisPixelCount")],
            ["Image Metrics", "Mean Brightness", getAnalysisText("analysisBrightness")],
            ["Image Metrics", "Contrast (Standard Deviation)", getAnalysisText("analysisContrast")],
            ["Image Metrics", "Mean Saturation", getAnalysisText("analysisSaturation")],
            ["Image Metrics", "Processed Mean", getAnalysisText("analysisProcessedMean")],
            ["Color Analysis", "Average RGB", getAnalysisText("analysisAverageRGB")],
            ["Color Analysis", "Dominant HEX", getAnalysisText("analysisDominantHex")],
            ["Color Analysis", "Dominant RGB", getAnalysisText("analysisDominantRGB")],
            ["Pixel Inspector", "Position", getAnalysisText("pixelPosition")],
            ["Pixel Inspector", "RGB", getAnalysisText("pixelRGB")],
            ["Pixel Inspector", "HEX", getAnalysisText("pixelHex")],
            ["Pixel Inspector", "HSV", getAnalysisText("pixelHSV")]
        ];
        rows.push(...summary);
        rows.push([]);
        rows.push(["Histogram", "Intensity", "Red", "Green", "Blue", "Grayscale"]);

        const context = analysisOriginalCanvas.getContext("2d", { willReadFrequently: true });
        if (!context) throw new Error("Data piksel tidak dapat dibaca.");
        const imageData = context.getImageData(0, 0, analysisOriginalCanvas.width, analysisOriginalCanvas.height).data;
        const redBins = new Uint32Array(256);
        const greenBins = new Uint32Array(256);
        const blueBins = new Uint32Array(256);
        const grayBins = new Uint32Array(256);
        for (let offset = 0; offset < imageData.length; offset += 4) {
            const red = imageData[offset];
            const green = imageData[offset + 1];
            const blue = imageData[offset + 2];
            redBins[red]++;
            greenBins[green]++;
            blueBins[blue]++;
            grayBins[Math.max(0, Math.min(255, Math.round(0.299 * red + 0.587 * green + 0.114 * blue)))]++;
        }
        for (let intensity = 0; intensity < 256; intensity++) {
            rows.push(["Histogram", intensity, redBins[intensity], greenBins[intensity], blueBins[intensity], grayBins[intensity]]);
        }

        const csv = "\uFEFF" + rows.map(row => row.map(csvEscape).join(",")).join("\r\n");
        const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
        downloadBlob(blob, makeAnalysisFileName("csv"));
        if (analysisExportStatus) analysisExportStatus.textContent = "Data analisis CSV berhasil diekspor, termasuk 256 bin histogram.";
    }

    if (downloadAnalysisReportButton) {
        downloadAnalysisReportButton.type = "button";
        downloadAnalysisReportButton.addEventListener("click", () => {
            try {
                downloadAnalysisReport();
            } catch (error) {
                console.error("ImageLab Live - export PNG error:", error);
                if (analysisExportStatus) analysisExportStatus.textContent = "Terjadi kesalahan saat membuat laporan PNG.";
                alert("Tidak dapat membuat laporan analisis. Periksa Console browser.");
            }
        });
    }

    if (downloadAnalysisCsvButton) {
        downloadAnalysisCsvButton.type = "button";
        downloadAnalysisCsvButton.addEventListener("click", () => {
            try {
                downloadAnalysisCsv();
            } catch (error) {
                console.error("ImageLab Live - export CSV error:", error);
                if (analysisExportStatus) analysisExportStatus.textContent = "Terjadi kesalahan saat mengekspor CSV.";
                alert("Tidak dapat mengekspor data analisis. Periksa Console browser.");
            }
        });
    }

    // -----------------------------------------------------
    // Process one frame with OpenCV
    // -----------------------------------------------------

    function processOneFrame() {
        if (!isRunning || !isOpenCVReady()) return false;
        if (video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) return false;

        let src = null;
        let dst = null;
        let gray = null;
        let rgb = null;
        let hsv = null;
        let channels = null;
        let selectedChannel = null;
        let colorMat = null;
        let filtered = null;
        let gradX = null;
        let gradY = null;
        let absX = null;
        let absY = null;
        let laplacian = null;
        let prewittKernelX = null;
        let prewittKernelY = null;
        let smoothed = null;

        try {
            sourceContext.drawImage(video, 0, 0, sourceCanvas.width, sourceCanvas.height);
            src = window.cv.imread(sourceCanvas);
            drawHistogram(src);

            const kernelSize = Math.max(3, Number(kernelSlider?.value || 5) | 1);
            const threshold = Number(thresholdSlider?.value ?? 127);
            const brightness = Number(brightnessSlider?.value ?? 0);
            const contrast = Number(contrastSlider?.value ?? 1);
            const gamma = Math.max(0.1, Number(gammaSlider?.value ?? 1));
            let cannyLow = Number(cannyThreshold1Slider?.value ?? 50);
            let cannyHigh = Number(cannyThreshold2Slider?.value ?? 150);
            if (cannyLow > cannyHigh) [cannyLow, cannyHigh] = [cannyHigh, cannyLow];
            if (cannyLow === cannyHigh) cannyHigh = Math.min(255, cannyLow + 1);

            switch (currentEffect) {
                case "grayscale":
                    dst = new cv.Mat();
                    cv.cvtColor(src, dst, cv.COLOR_RGBA2GRAY);
                    break;

                // RGB: tampilkan satu channel Red, Green, atau Blue sebagai grayscale.
                case "rgb": {
                    colorMat = new cv.Mat();
                    channels = new cv.MatVector();
                    cv.cvtColor(src, colorMat, cv.COLOR_RGBA2RGB);
                    cv.split(colorMat, channels);

                    const rgbChannelIndex = { red: 0, green: 1, blue: 2 }[
                        colorChannelSelect?.value || selectedColorChannels.rgb
                    ] ?? 0;
                    selectedChannel = channels.get(rgbChannelIndex);
                    dst = selectedChannel.clone();
                    break;
                }

                // CMYK: konversi matematis RGB ke Cyan, Magenta, Yellow, Key.
                // Channel yang dipilih divisualisasikan sebagai grayscale.
                case "cmyk": {
                    const cmykChannel = colorChannelSelect?.value || selectedColorChannels.cmyk;
                    const output = new Uint8Array(src.rows * src.cols);
                    const input = src.data;

                    for (let pixel = 0, sourceIndex = 0; pixel < output.length; pixel++, sourceIndex += 4) {
                        const r = input[sourceIndex] / 255;
                        const g = input[sourceIndex + 1] / 255;
                        const b = input[sourceIndex + 2] / 255;

                        const key = 1 - Math.max(r, g, b);
                        const denominator = 1 - key;
                        const cyan = denominator === 0 ? 0 : (1 - r - key) / denominator;
                        const magenta = denominator === 0 ? 0 : (1 - g - key) / denominator;
                        const yellow = denominator === 0 ? 0 : (1 - b - key) / denominator;

                        let value;
                        switch (cmykChannel) {
                            case "magenta": value = magenta; break;
                            case "yellow": value = yellow; break;
                            case "key": value = key; break;
                            case "cyan":
                            default: value = cyan;
                        }

                        output[pixel] = Math.round(Math.max(0, Math.min(1, value)) * 255);
                    }

                    dst = cv.matFromArray(src.rows, src.cols, cv.CV_8UC1, output);
                    break;
                }

                // HSV / HSB: pilih Hue, Saturation, atau Value/Brightness.
                case "hsv": {
                    rgb = new cv.Mat();
                    hsv = new cv.Mat();
                    channels = new cv.MatVector();
                    cv.cvtColor(src, rgb, cv.COLOR_RGBA2RGB);
                    cv.cvtColor(rgb, hsv, cv.COLOR_RGB2HSV);
                    cv.split(hsv, channels);

                    const hsvChannelIndex = { hue: 0, saturation: 1, value: 2 }[
                        colorChannelSelect?.value || selectedColorChannels.hsv
                    ] ?? 0;
                    selectedChannel = channels.get(hsvChannelIndex);
                    dst = new cv.Mat();

                    // Hue OpenCV memiliki rentang 0-179; skalakan ke 0-255 untuk display.
                    if (hsvChannelIndex === 0) {
                        selectedChannel.convertTo(dst, cv.CV_8UC1, 255 / 179);
                    } else {
                        selectedChannel.copyTo(dst);
                    }
                    break;
                }

                case "binary":
                    gray = new cv.Mat();
                    dst = new cv.Mat();
                    cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
                    cv.threshold(gray, dst, threshold, 255, cv.THRESH_BINARY);
                    break;

                // Otsu: menentukan nilai threshold otomatis dari distribusi intensitas grayscale.
                case "otsu":
                    gray = new cv.Mat();
                    dst = new cv.Mat();
                    cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
                    cv.threshold(gray, dst, 0, 255, cv.THRESH_BINARY | cv.THRESH_OTSU);
                    break;

                // Histogram Equalization: pemerataan histogram pada citra grayscale.
                case "equalize":
                    gray = new cv.Mat();
                    dst = new cv.Mat();
                    cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
                    cv.equalizeHist(gray, dst);
                    break;

                // Brightness: menggeser intensitas piksel dengan nilai beta.
                case "brightness":
                    dst = new cv.Mat();
                    src.convertTo(dst, cv.CV_8U, 1, brightness);
                    break;

                // Contrast: mengalikan intensitas piksel dengan faktor alpha.
                case "contrast":
                    dst = new cv.Mat();
                    src.convertTo(dst, cv.CV_8U, contrast, 0);
                    break;

                // Gamma correction: LUT diterapkan pada kanal RGB; alpha dipertahankan.
                case "gamma": {
                    dst = new cv.Mat(src.rows, src.cols, src.type());
                    const input = src.data;
                    const output = dst.data;
                    const exponent = 1 / gamma;
                    const lut = new Uint8Array(256);
                    for (let i = 0; i < 256; i++) {
                        lut[i] = Math.max(0, Math.min(255, Math.round(255 * Math.pow(i / 255, exponent))));
                    }
                    for (let i = 0; i < input.length; i += 4) {
                        output[i] = lut[input[i]];
                        output[i + 1] = lut[input[i + 1]];
                        output[i + 2] = lut[input[i + 2]];
                        output[i + 3] = input[i + 3];
                    }
                    break;
                }

                case "gaussian":
                    dst = new cv.Mat();
                    cv.GaussianBlur(src, dst, new cv.Size(kernelSize, kernelSize), 0, 0, cv.BORDER_DEFAULT);
                    break;

                case "median":
                    dst = new cv.Mat();
                    cv.medianBlur(src, dst, kernelSize);
                    break;

                case "bilateral":
                    rgb = new cv.Mat();
                    filtered = new cv.Mat();
                    dst = new cv.Mat();
                    cv.cvtColor(src, rgb, cv.COLOR_RGBA2RGB);
                    cv.bilateralFilter(rgb, filtered, kernelSize, 75, 75, cv.BORDER_DEFAULT);
                    cv.cvtColor(filtered, dst, cv.COLOR_RGB2RGBA);
                    break;

                // Sobel: menghitung perubahan intensitas pada arah horizontal dan vertikal.
                case "sobel":
                    gray = new cv.Mat();
                    gradX = new cv.Mat();
                    gradY = new cv.Mat();
                    absX = new cv.Mat();
                    absY = new cv.Mat();
                    dst = new cv.Mat();
                    cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
                    cv.Sobel(gray, gradX, cv.CV_16S, 1, 0, 3, 1, 0, cv.BORDER_DEFAULT);
                    cv.Sobel(gray, gradY, cv.CV_16S, 0, 1, 3, 1, 0, cv.BORDER_DEFAULT);
                    cv.convertScaleAbs(gradX, absX);
                    cv.convertScaleAbs(gradY, absY);
                    cv.addWeighted(absX, 0.5, absY, 0.5, 0, dst);
                    break;

                // Prewitt: konvolusi menggunakan kernel 3x3 arah X dan Y.
                case "prewitt":
                    gray = new cv.Mat();
                    gradX = new cv.Mat();
                    gradY = new cv.Mat();
                    absX = new cv.Mat();
                    absY = new cv.Mat();
                    dst = new cv.Mat();
                    prewittKernelX = cv.matFromArray(3, 3, cv.CV_32F, [
                        -1, 0, 1,
                        -1, 0, 1,
                        -1, 0, 1
                    ]);
                    prewittKernelY = cv.matFromArray(3, 3, cv.CV_32F, [
                        -1, -1, -1,
                         0,  0,  0,
                         1,  1,  1
                    ]);
                    cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
                    cv.filter2D(gray, gradX, cv.CV_32F, prewittKernelX);
                    cv.filter2D(gray, gradY, cv.CV_32F, prewittKernelY);
                    cv.convertScaleAbs(gradX, absX);
                    cv.convertScaleAbs(gradY, absY);
                    cv.addWeighted(absX, 0.5, absY, 0.5, 0, dst);
                    break;

                // Laplacian: mendeteksi perubahan intensitas menggunakan turunan kedua.
                case "laplacian":
                    gray = new cv.Mat();
                    laplacian = new cv.Mat();
                    dst = new cv.Mat();
                    cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
                    cv.Laplacian(gray, laplacian, cv.CV_16S, 3, 1, 0, cv.BORDER_DEFAULT);
                    cv.convertScaleAbs(laplacian, dst);
                    break;

                // Canny: smoothing, gradient, non-maximum suppression, dan hysteresis.
                case "canny":
                    gray = new cv.Mat();
                    smoothed = new cv.Mat();
                    dst = new cv.Mat();
                    cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
                    cv.GaussianBlur(gray, smoothed, new cv.Size(3, 3), 0, 0, cv.BORDER_DEFAULT);
                    cv.Canny(smoothed, dst, cannyLow, cannyHigh, 3, false);
                    break;

                case "original":
                default:
                    dst = src.clone();
                    break;
            }

            // Update comparison previews and image statistics at a throttled interval.
            updateAdvancedImageAnalysis(src, dst);

            cv.imshow(canvas, dst);
            hasProcessedFrame = true;
            lastProcessingError = "";
            showProcessedCanvas();
            setStatus("Camera Active · OpenCV", true);
            return true;

        } catch (error) {
            const message = error && error.message ? error.message : String(error);
            if (message !== lastProcessingError) {
                console.error("ImageLab Live - OpenCV processing error:", error);
                lastProcessingError = message;
            }

            // Bila pemrosesan gagal, tetap perlihatkan video live, bukan canvas hitam.
            showVideoPreview();
            setStatus("Camera Active · Preview Mode", true);
            return false;

        } finally {
            if (src) src.delete();
            if (dst) dst.delete();
            if (gray) gray.delete();
            if (rgb) rgb.delete();
            if (hsv) hsv.delete();
            if (channels) channels.delete();
            if (selectedChannel) selectedChannel.delete();
            if (colorMat) colorMat.delete();
            if (filtered) filtered.delete();
            if (gradX) gradX.delete();
            if (gradY) gradY.delete();
            if (absX) absX.delete();
            if (absY) absY.delete();
            if (laplacian) laplacian.delete();
            if (prewittKernelX) prewittKernelX.delete();
            if (prewittKernelY) prewittKernelY.delete();
            if (smoothed) smoothed.delete();
        }
    }

    // -----------------------------------------------------
    // Animation loop
    // -----------------------------------------------------

    function startProcessingLoop() {
        if (animationFrameId !== null) cancelAnimationFrame(animationFrameId);

        function tick(now) {
            if (!isRunning) {
                animationFrameId = null;
                return;
            }

            // Tetap menampilkan elemen video sampai OpenCV siap dan berhasil merender.
            if (isOpenCVReady()) {
                processOneFrame();
            } else {
                showVideoPreview();
                setStatus("Camera Active · Loading OpenCV", true);
            }

            fpsFrames++;
            if (now - lastFpsTime >= 1000) {
                if (cameraFPS) cameraFPS.textContent = String(fpsFrames);
                fpsFrames = 0;
                lastFpsTime = now;
            }

            animationFrameId = requestAnimationFrame(tick);
        }

        animationFrameId = requestAnimationFrame(tick);
    }

    // -----------------------------------------------------
    // Effects
    // -----------------------------------------------------

    const channelOptions = {
        rgb: [
            { value: "red", label: "Red (R)" },
            { value: "green", label: "Green (G)" },
            { value: "blue", label: "Blue (B)" }
        ],
        cmyk: [
            { value: "cyan", label: "Cyan (C)" },
            { value: "magenta", label: "Magenta (M)" },
            { value: "yellow", label: "Yellow (Y)" },
            { value: "key", label: "Key / Black (K)" }
        ],
        hsv: [
            { value: "hue", label: "Hue (H)" },
            { value: "saturation", label: "Saturation (S)" },
            { value: "value", label: "Value / Brightness (V)" }
        ]
    };

    function updateChannelSelector() {
        if (!colorChannelControl || !colorChannelSelect) return;

        const options = channelOptions[currentEffect];
        if (!options) return;

        colorChannelSelect.innerHTML = options.map(option =>
            `<option value="${option.value}">${option.label}</option>`
        ).join("");
        colorChannelSelect.value = selectedColorChannels[currentEffect];
    }

    // Tampilkan hanya parameter yang relevan dengan efek aktif.
    const parameterEffects = {
        "color-channel": ["rgb", "cmyk", "hsv"],
        threshold: ["binary"],
        kernel: ["gaussian", "median", "bilateral"],
        brightness: ["brightness"],
        contrast: ["contrast"],
        gamma: ["gamma"],
        canny: ["canny"]
    };

    const parameterDescriptions = {
        original: "Tampilan asli tidak memerlukan parameter tambahan.",
        rgb: "Pilih channel Red, Green, atau Blue untuk divisualisasikan.",
        cmyk: "Pilih channel Cyan, Magenta, Yellow, atau Key untuk divisualisasikan.",
        hsv: "Pilih Hue, Saturation, atau Value untuk divisualisasikan.",
        binary: "Atur ambang intensitas untuk memisahkan piksel menjadi hitam dan putih.",
        gaussian: "Atur ukuran kernel untuk mengendalikan tingkat Gaussian Blur.",
        median: "Atur ukuran kernel untuk mengendalikan pengurangan noise dengan Median Blur.",
        bilateral: "Atur ukuran kernel untuk menghaluskan citra sambil mempertahankan tepi.",
        brightness: "Geser nilai untuk membuat citra lebih gelap atau lebih terang.",
        contrast: "Atur faktor kontras untuk memperbesar atau mengurangi perbedaan intensitas.",
        gamma: "Atur gamma untuk mengoreksi intensitas piksel secara non-linear.",
        canny: "Atur lower dan upper threshold untuk mengendalikan sensitivitas deteksi tepi Canny."
    };

    function updateParameterVisibility() {
        let visibleCount = 0;

        parameterGroups.forEach(group => {
            const parameterName = group.dataset.parameter;
            const supportedEffects = parameterEffects[parameterName] || [];
            const shouldShow = supportedEffects.includes(currentEffect);
            group.classList.toggle("hidden", !shouldShow);
            if (shouldShow) visibleCount++;
        });

        if (parameterEmptyState) {
            parameterEmptyState.classList.toggle("hidden", visibleCount > 0);
        }

        if (parameterDescription) {
            parameterDescription.textContent = parameterDescriptions[currentEffect]
                || "Efek ini tidak memerlukan pengaturan parameter tambahan.";
        }
    }

    function openAccordionSection(section) {
        if (!section) return;
        accordionSections.forEach(item => {
            const trigger = item.querySelector("[data-accordion-trigger]");
            const panelId = trigger?.dataset.target;
            const panel = panelId ? document.getElementById(panelId) : null;
            const isOpen = item === section;

            if (trigger) trigger.setAttribute("aria-expanded", String(isOpen));
            if (panel) panel.classList.toggle("hidden", !isOpen);
        });
    }

    // Maksimal satu kategori accordion terbuka pada satu waktu.
    accordionSections.forEach(section => {
        const trigger = section.querySelector("[data-accordion-trigger]");
        if (!trigger) return;

        trigger.addEventListener("click", () => {
            const isCurrentlyOpen = trigger.getAttribute("aria-expanded") === "true";
            if (isCurrentlyOpen) {
                trigger.setAttribute("aria-expanded", "false");
                const panel = document.getElementById(trigger.dataset.target);
                if (panel) panel.classList.add("hidden");
            } else {
                openAccordionSection(section);
            }
        });
    });

    // Sinkronkan visibilitas parameter dan kategori saat efek dipilih.
    function selectEffect(button) {
        if (!button) return;
        currentEffect = button.dataset.effect || "original";

        effectButtons.forEach(item => {
            item.classList.remove("bg-blue-600", "text-white");
            item.classList.add("hover:bg-zinc-800");
        });
        button.classList.add("bg-blue-600", "text-white");
        button.classList.remove("hover:bg-zinc-800");

        if (activeEffect) activeEffect.textContent = button.textContent.trim();
        if (analysisProcessedEffect) analysisProcessedEffect.textContent = button.textContent.trim();
        updateChannelSelector();
        updateParameterVisibility();
        openAccordionSection(button.closest("[data-accordion-section]"));
    }

    // Initial UI state: contextual controls follow the default Original RGB effect.
    updateChannelSelector();
    updateParameterVisibility();

    if (colorChannelSelect) {
        colorChannelSelect.addEventListener("change", () => {
            if (Object.prototype.hasOwnProperty.call(selectedColorChannels, currentEffect)) {
                selectedColorChannels[currentEffect] = colorChannelSelect.value;
            }
        });
    }

    effectButtons.forEach(button => {
        button.type = "button";
        button.addEventListener("click", () => selectEffect(button));
    });

    if (thresholdSlider && thresholdValue) {
        thresholdSlider.addEventListener("input", () => {
            thresholdValue.textContent = thresholdSlider.value;
        });
    }

    if (kernelSlider && kernelValue) {
        kernelSlider.addEventListener("input", () => {
            kernelValue.textContent = `${kernelSlider.value} × ${kernelSlider.value}`;
        });
    }

    if (brightnessSlider && brightnessValue) {
        brightnessSlider.addEventListener("input", () => {
            brightnessValue.textContent = brightnessSlider.value;
        });
    }

    if (contrastSlider && contrastValue) {
        contrastSlider.addEventListener("input", () => {
            contrastValue.textContent = Number(contrastSlider.value).toFixed(1);
        });
    }

    if (gammaSlider && gammaValue) {
        gammaSlider.addEventListener("input", () => {
            gammaValue.textContent = Number(gammaSlider.value).toFixed(1);
        });
    }

    if (histogramModeSelect) {
        histogramModeSelect.addEventListener("change", () => {
            lastHistogramUpdate = 0;
        });
    }

    if (cannyThreshold1Slider && cannyThreshold1Value) {
        cannyThreshold1Slider.addEventListener("input", () => {
            cannyThreshold1Value.textContent = cannyThreshold1Slider.value;
        });
    }

    if (cannyThreshold2Slider && cannyThreshold2Value) {
        cannyThreshold2Slider.addEventListener("input", () => {
            cannyThreshold2Value.textContent = cannyThreshold2Slider.value;
        });
    }

    // -----------------------------------------------------
    // Capture history helpers
    // -----------------------------------------------------

    function updateCaptureHistoryUI() {
        const count = captureHistory.length;
        if (captureCount) captureCount.textContent = String(count);
        if (captureHistoryEmpty) captureHistoryEmpty.classList.toggle("hidden", count > 0);
        if (captureHistoryList) captureHistoryList.classList.toggle("hidden", count === 0);
        if (clearCaptureHistoryButton) clearCaptureHistoryButton.disabled = count === 0;
    }

    function removeCaptureEntry(entry) {
        const index = captureHistory.indexOf(entry);
        if (index !== -1) captureHistory.splice(index, 1);
        if (entry.element) entry.element.remove();
        if (entry.url) URL.revokeObjectURL(entry.url);
        updateCaptureHistoryUI();
    }

    function clearCaptureHistory() {
        // Revoke every object URL to avoid keeping captured image blobs in memory.
        captureHistory.slice().forEach(removeCaptureEntry);
        updateCaptureHistoryUI();
    }

    function addCaptureToHistory(blob) {
        if (!captureHistoryList) {
            throw new Error("Elemen captureHistoryList tidak ditemukan di studio.blade.php.");
        }

        const now = new Date();
        const timestamp = now.toLocaleString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        });
        const safeTimestamp = now.toISOString().replace(/[:.]/g, "-");
        const fileName = `imagelab-${currentEffect}-${safeTimestamp}.png`;
        const effectName = activeEffect ? activeEffect.textContent.trim() : currentEffect;
        const imageUrl = URL.createObjectURL(blob);

        const item = document.createElement("article");
        item.className = "flex items-center gap-3 rounded-lg border border-zinc-800 bg-zinc-950/70 p-2.5";

        const thumbnail = document.createElement("img");
        thumbnail.src = imageUrl;
        thumbnail.alt = `Capture ${effectName}`;
        thumbnail.className = "w-20 h-16 shrink-0 rounded-md border border-zinc-800 bg-black object-cover";

        const details = document.createElement("div");
        details.className = "min-w-0 flex-1";

        const title = document.createElement("p");
        title.className = "text-xs font-semibold text-zinc-200 truncate";
        title.textContent = effectName;

        const time = document.createElement("p");
        time.className = "text-[10px] text-zinc-500 mt-1";
        time.textContent = timestamp;

        const fileLabel = document.createElement("p");
        fileLabel.className = "text-[10px] text-zinc-600 mt-1";
        fileLabel.textContent = "PNG image";

        details.append(title, time, fileLabel);

        const actions = document.createElement("div");
        actions.className = "flex shrink-0 flex-col gap-1.5";

        const downloadLink = document.createElement("a");
        downloadLink.href = imageUrl;
        downloadLink.download = fileName;
        downloadLink.textContent = "Download";
        downloadLink.className = "rounded-md bg-blue-600 hover:bg-blue-500 px-2.5 py-1.5 text-[10px] font-semibold text-white text-center transition";
        downloadLink.setAttribute("aria-label", `Download ${effectName}`);

        const removeButton = document.createElement("button");
        removeButton.type = "button";
        removeButton.textContent = "Remove";
        removeButton.className = "rounded-md border border-zinc-700 hover:border-red-500/50 hover:text-red-300 px-2.5 py-1 text-[10px] text-zinc-400 transition";
        removeButton.addEventListener("click", () => removeCaptureEntry(entry));

        actions.append(downloadLink, removeButton);
        item.append(thumbnail, details, actions);

        const entry = { url: imageUrl, element: item, id: ++captureSequence };
        captureHistory.unshift(entry);
        captureHistoryList.prepend(item);

        // Batasi riwayat agar penggunaan memori tetap terkendali.
        while (captureHistory.length > MAX_CAPTURE_HISTORY) {
            removeCaptureEntry(captureHistory[captureHistory.length - 1]);
        }

        updateCaptureHistoryUI();
    }

    // -----------------------------------------------------
    // Capture current visible frame and save it to the sidebar
    // -----------------------------------------------------

    function captureImage() {
        if (!isRunning || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
            alert("Aktifkan kamera terlebih dahulu sebelum melakukan capture.");
            return;
        }

        try {
            // Jika OpenCV belum merender frame, ambil frame langsung dari video.
            if (!hasProcessedFrame || canvas.classList.contains("hidden")) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const captureContext = canvas.getContext("2d");
                if (!captureContext) throw new Error("Canvas context tidak tersedia.");
                captureContext.drawImage(video, 0, 0, canvas.width, canvas.height);
            }

            if (!canvas.width || !canvas.height) {
                alert("Belum ada frame kamera yang bisa disimpan.");
                return;
            }

            canvas.toBlob(blob => {
                if (!blob) {
                    alert("Browser gagal membuat gambar. Silakan coba kembali.");
                    return;
                }

                try {
                    addCaptureToHistory(blob);
                } catch (error) {
                    console.error("ImageLab Live - gagal menyimpan riwayat capture:", error);
                    alert("Hasil capture gagal ditampilkan. Periksa Console browser.");
                    return;
                }

                console.info("ImageLab Live: hasil capture ditambahkan ke Captured Results.");
            }, "image/png");
        } catch (error) {
            console.error("ImageLab Live - capture gagal:", error);
            alert("Gagal mengambil gambar. Periksa Console browser untuk detail error.");
        }
    }

    if (clearCaptureHistoryButton) {
        clearCaptureHistoryButton.type = "button";
        clearCaptureHistoryButton.addEventListener("click", clearCaptureHistory);
    }

    updateCaptureHistoryUI();

    // -----------------------------------------------------
    // Reset effects
    // -----------------------------------------------------

    if (resetButton) {
        resetButton.type = "button";
        resetButton.addEventListener("click", () => {
            const originalButton = document.querySelector('.effect-button[data-effect="original"]');
            if (originalButton) selectEffect(originalButton);
            if (thresholdSlider && thresholdValue) {
                thresholdSlider.value = "127";
                thresholdValue.textContent = "127";
            }
            if (kernelSlider && kernelValue) {
                kernelSlider.value = "5";
                kernelValue.textContent = "5 × 5";
            }
            if (cannyThreshold1Slider && cannyThreshold1Value) {
                cannyThreshold1Slider.value = "50";
                cannyThreshold1Value.textContent = "50";
            }
            if (cannyThreshold2Slider && cannyThreshold2Value) {
                cannyThreshold2Slider.value = "150";
                cannyThreshold2Value.textContent = "150";
            }
            if (brightnessSlider && brightnessValue) {
                brightnessSlider.value = "0";
                brightnessValue.textContent = "0";
            }
            if (contrastSlider && contrastValue) {
                contrastSlider.value = "1";
                contrastValue.textContent = "1.0";
            }
            if (gammaSlider && gammaValue) {
                gammaSlider.value = "1";
                gammaValue.textContent = "1.0";
            }
            if (histogramModeSelect) {
                histogramModeSelect.value = "rgb";
                lastHistogramUpdate = 0;
            }
        });
    }

    // -----------------------------------------------------
    // Button events
    // -----------------------------------------------------

    startButton.type = "button";
    stopButton.type = "button";
    captureButton.type = "button";

    startButton.addEventListener("click", startCamera);
    stopButton.addEventListener("click", stopCamera);
    captureButton.addEventListener("click", captureImage);

    setButtons(false);
    setStatus("Camera Offline", false);
    console.info("ImageLab Live: camera.js berhasil diinisialisasi.");
})();
