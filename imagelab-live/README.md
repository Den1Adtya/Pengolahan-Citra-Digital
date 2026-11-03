# ImageLab Live

> **Digital Image Processing Laboratory** — an interactive, browser-based workspace for real-time image processing, visualization, and pixel-level analysis.

ImageLab Live is a web application built to explore fundamental digital image processing concepts through a live camera feed. Users can apply color-space transformations, image filters, edge-detection algorithms, and image-enhancement techniques, then inspect the resulting image through histograms and image-analysis metrics.

The current implementation performs camera capture and image processing in the browser using **WebRTC, HTML5 Canvas, JavaScript, and OpenCV.js**. Laravel and Blade provide the application structure and user interface.

---

## Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Application Workflow](#application-workflow)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Using ImageLab Live](#using-imagelab-live)
- [Browser and Camera Requirements](#browser-and-camera-requirements)
- [Technical Notes](#technical-notes)
- [Current Scope and Limitations](#current-scope-and-limitations)
- [Roadmap](#roadmap)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## Overview

ImageLab Live is designed as an educational digital image processing laboratory. Instead of applying a filter to a static image only, the application lets users observe how image-processing operations affect a live camera frame.

The application brings together three main activities:

1. **Process** — apply image-processing algorithms and adjust their parameters.
2. **Visualize** — observe histograms, color channels, and the difference between original and processed frames.
3. **Analyze** — inspect image metrics and individual pixel values, then export analysis results.

The interface uses a dark visual theme with a responsive layout intended to keep the processing workspace readable while presenting technical information clearly.

## Features

### 1. Real-Time Camera Processing

- Start and stop the camera from the Processing Studio.
- Display the live camera preview in the browser.
- Process frames with OpenCV.js when the OpenCV runtime is ready.
- Display camera resolution and approximate processing FPS.
- Capture the currently displayed processing result.

### 2. Color Processing

| Operation | Description |
|---|---|
| Original RGB | Displays the source frame without a color transformation. |
| RGB Color Model | Inspects the Red, Green, and Blue channels. |
| CMYK Color Model | Visualizes Cyan, Magenta, Yellow, and Key/Black components using an RGB-to-CMYK conversion. |
| HSV / HSB Color Model | Inspects Hue, Saturation, and Value/Brightness components. |
| Grayscale | Converts the source frame to grayscale intensity. |
| Binary Threshold | Converts a grayscale frame into a black-and-white image using a threshold value. |

Channel previews are presented as intensity visualizations to make the contribution of each channel easier to inspect. The CMYK conversion is intended for learning and visualization; it is not a printer-calibrated or ICC color-management workflow.

### 3. Image Filtering

| Filter | Purpose |
|---|---|
| Gaussian Blur | Smooths an image using a Gaussian kernel. |
| Median Blur | Reduces impulse-like noise using a median operation. |
| Bilateral Filter | Smooths regions while helping preserve edges. |

Kernel size can be adjusted where applicable.

### 4. Edge Detection

| Algorithm | Purpose |
|---|---|
| Sobel | Estimates image gradients in horizontal and vertical directions. |
| Prewitt | Detects intensity changes using Prewitt gradient kernels. |
| Laplacian | Highlights regions with rapid intensity changes using a second-order derivative. |
| Canny | Performs multi-stage edge detection using lower and upper thresholds. |

### 5. Image Enhancement

- **Histogram Equalization** — improves grayscale contrast by redistributing intensity values.
- **Brightness Adjustment** — adjusts image brightness.
- **Contrast Adjustment** — changes the separation between darker and brighter values.
- **Gamma Correction** — applies a nonlinear intensity transformation.
- **Otsu Automatic Threshold** — determines a threshold from the image histogram.

### 6. Histogram Visualization

The Histogram panel supports:

- RGB histogram visualization.
- Grayscale histogram visualization.
- Intensity range from 0 to 255.
- Periodic updates while the camera is active.

The histogram is calculated from the original camera frame before the selected processing effect is applied. This makes it possible to inspect the source-image distribution independently from the processed preview.

### 7. Advanced Image Analysis

The Advanced Image Analysis panel presents the original and processed frames side by side and provides the following information:

| Analysis | Description |
|---|---|
| Resolution | Width and height of the source frame. |
| Total Pixels | Total number of pixels in the source frame. |
| Mean Brightness | Average grayscale/luma intensity. |
| Contrast | Standard deviation of luma intensity. |
| Mean Saturation | Average saturation estimated from the source frame. |
| Processed Mean | Average intensity of the processed output. |
| Average RGB Balance | Average Red, Green, and Blue channel values. |
| Dominant Sampled Color | Approximate dominant color based on color quantization. |
| Pixel Inspector | Reads the selected pixel position and its RGB, HEX, and HSV values. |

Image metrics are calculated from sampled pixels to keep the real-time interface responsive. The interface indicates the sampling context where applicable; these values should be interpreted as analysis estimates rather than a full-resolution scientific measurement in every case.

### 8. Capture History and Export

- Capture the currently displayed frame.
- Review captured thumbnails in the **Captured Results** panel.
- Download an individual capture.
- Remove an individual capture or clear the capture history.
- Export an Advanced Image Analysis report as PNG.
- Export analysis data as CSV, including summary metrics and 256 intensity bins for the RGB and grayscale histograms.

Capture history is temporary and held in browser memory. It is limited to a maximum of eight entries in the current implementation and is cleared when the Studio page is refreshed or closed.

### 9. User Interface

- Responsive Home, Studio, and About pages.
- Sticky navigation with active-page indication.
- Mobile navigation menu.
- Accordion-based effect categories.
- Contextual parameter controls that show settings relevant to the selected effect.
- Sticky effect-library and capture-history sidebar on wide screens.
- Reduced-motion support for users who prefer less animation.

---

## Technology Stack

| Technology | Role |
|---|---|
| PHP / Laravel | Application framework and routing. |
| Blade | Server-rendered page templates and layouts. |
| Tailwind CSS | Utility-first styling and responsive interface. |
| JavaScript | Camera control, UI interaction, image analysis, and exports. |
| OpenCV.js | Browser-side image-processing operations. |
| WebRTC (`getUserMedia`) | Accesses the user's camera. |
| HTML5 Canvas | Displays, samples, and exports image frames. |

### Processing Architecture

The current image-processing flow is browser-based:

```text
Camera
  │
  ▼
getUserMedia() ──► HTMLVideoElement
                       │
                       ▼
                 Source Canvas
                       │
                       ▼
                  OpenCV.js
                       │
             ┌─────────┴─────────┐
             ▼                   ▼
      Processing Effect     Image Analysis
             │                   │
             ▼                   ▼
      Preview Canvas       Histogram / Metrics
             │                   │
             ▼                   ▼
         Capture            PNG / CSV Export
```

Camera frames and analysis are processed in the browser. The current workflow does not require an application database or user account for its image-processing features.

---

## Project Structure

The following is the application-specific structure. Other standard Laravel directories and files are omitted for readability.

```text
imagelab-live/
├── public/
│   ├── js/
│   │   └── camera.js              # Camera, OpenCV, processing, analysis, and capture logic
│   ├── favicon.svg                # Browser tab icon
│   ├── favicon.ico
│   └── ...                        # Additional favicon assets
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php      # Shared layout, navigation, and footer
│       ├── home.blade.php         # Landing page
│       ├── studio.blade.php       # Real-time processing workspace
│       └── about.blade.php        # Feature and platform information
│
└── routes/
    └── web.php                   # Home, Studio, and About routes
```

### Main Routes

| Route | Page |
|---|---|
| `/` | Home |
| `/studio` | Processing Studio |
| `/about` | About ImageLab Live |

---

## Getting Started

### Prerequisites

Make sure the development environment includes:

- PHP version compatible with the Laravel version in the project.
- Composer.
- A supported web browser with camera access.
- An available webcam or built-in camera.
- An internet connection to load the external Tailwind CSS and OpenCV.js CDN assets used by the current setup.

Node.js and npm are only required if frontend assets are later moved to a local Vite/Tailwind build.

### 1. Clone the Repository

```bash
git clone <repository-url>
cd imagelab-live
```

Replace `<repository-url>` with the actual Git repository URL.

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Configure the Environment

Create the environment file if it does not already exist.

**macOS / Linux / Git Bash:**

```bash
cp .env.example .env
```

**Windows PowerShell:**

```powershell
Copy-Item .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

If the project already has a configured `.env` file, keep the existing settings instead of replacing it.

### 4. Start the Development Server

```bash
php artisan serve
```

Open the local application at:

```text
http://127.0.0.1:8000
```

No database migration is required for the current browser-based image-processing workflow, provided no additional database-backed feature has been added to the project.

---

## Using ImageLab Live

1. Open the **Studio** page.
2. Select **Start Camera** and grant camera permission when the browser asks.
3. Choose an effect from the Processing Effects sidebar.
4. Adjust the contextual parameters when they are available for the selected effect.
5. Observe the processed preview, histogram, and Advanced Image Analysis panels.
6. Select **Capture** to add the current result to Captured Results.
7. Download an individual capture or export the analysis report as PNG or CSV.

To return to the unmodified camera image, select **Original RGB** or use the parameter reset control.

---

## Browser and Camera Requirements

Camera access uses the browser's `getUserMedia()` API.

- Use `localhost` or `127.0.0.1` during local development.
- Use HTTPS when accessing the application from a deployed domain. Modern browsers generally block camera access on an insecure, non-local HTTP origin.
- Allow camera permission for the site.
- Close other applications or browser tabs that may be using the camera if access fails.
- Wait for OpenCV.js to finish loading before expecting image effects to run. The camera preview may be available separately while the OpenCV runtime is still initializing.

---

## Technical Notes

### External CDN Assets

The current layout loads Tailwind CSS from `cdn.tailwindcss.com`, and the Studio page loads OpenCV.js from the OpenCV documentation CDN.

The Tailwind CDN message in the browser console is a **production-readiness warning**, not an application runtime error. For a production deployment, migrate Tailwind to a local build using the Tailwind Vite plugin and Laravel Vite integration, then remove the CDN script from the shared layout.

OpenCV.js is also loaded externally in the current setup. A production deployment should consider pinning and self-hosting a tested OpenCV.js build so that the application is less dependent on an external CDN version changing.

### Browser-Side Processing

- Image processing is performed in the client browser.
- The camera stream is not stored by the application server in the current workflow.
- Capture history is temporary browser memory, not persistent storage.
- PNG and CSV exports are generated in the browser.
- Processing performance depends on the browser, camera resolution, device CPU, and selected algorithm.

### OpenCV Memory Management

OpenCV.js uses WebAssembly-backed matrix objects. When changing or extending processing code, release matrices that are no longer needed by calling `.delete()` to avoid unnecessary memory growth during long camera sessions.

---

## Current Scope and Limitations

The current application focuses on live camera input and browser-side processing. The following items are not part of the current implemented scope and should not be described as completed features unless they are added to the project:

- Uploading images from local storage as an alternative input source.
- Morphological operations such as Erosion, Dilation, Opening, and Closing.
- Geometric transformations such as Translation, Rotation, Scaling, and Cropping.
- Persistent capture history across page reloads or browser sessions.
- User accounts, authentication, and server-side image storage.

The RGB-to-CMYK conversion is an educational visualization and does not replace color-managed print conversion. Some analysis metrics use sampled pixels to reduce the processing load.

---

## Roadmap

Potential next steps for the project include:

- [ ] Image upload and a shared processing pipeline for camera and uploaded images.
- [ ] Morphological image processing: Erosion, Dilation, Opening, and Closing.
- [ ] Geometric transformations: Translation, Rotation, Mirroring, Scaling, and Cropping.
- [ ] Persistent capture history using browser storage such as IndexedDB.
- [ ] Performance profiling across different camera resolutions and devices.
- [ ] Automated functional testing for camera controls, effects, parameters, and exports.
- [ ] Production asset pipeline using Laravel Vite and a locally built Tailwind stylesheet.

---

## Troubleshooting

| Issue | Suggested action |
|---|---|
| Camera does not start | Check browser permission, use localhost or HTTPS, and make sure another application is not using the camera. |
| Preview is black | Check the browser console, confirm that the camera stream is active, and verify that the OpenCV.js script has loaded. |
| Effects do not respond | Wait for OpenCV.js initialization, then reload the Studio page and check for JavaScript errors in DevTools. |
| Histogram or analysis is empty | Start the camera and wait for the first analysis update. |
| Capture history is empty after refresh | This is expected: capture history is temporary and is not persisted between page loads. |
| Tailwind CDN warning appears | This is an informational production warning. The current layout uses the Tailwind CDN; migrate to Vite for production. |
| Favicon appears unchanged | Hard-refresh the browser or clear the browser's cached favicon. |

For debugging, open **Developer Tools → Console** and review the first error message, if any.

---

## License

No license has been specified for this repository yet. Add a `LICENSE` file before distributing the project or granting others explicit permission to reuse, modify, or redistribute its source code.

---

<p align="center">
  <strong>ImageLab Live</strong><br>
  <sub>Explore the image. Understand every pixel.</sub>
</p>
