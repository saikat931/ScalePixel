# ScalePixel ⚡
### Universal High-Performance Image Compression & Conversion Platform

ScalePixel is an enterprise-grade, stateless image optimization engine and universal format converter designed to rival platforms like **TinyPNG**, **Cloudinary**, and **CloudConvert**.

---

### Features

- **Universal Format Conversion**: Convert between `JPG`, `PNG`, `WebP`, `AVIF`, `GIF`, `BMP`, and `ICO`.
- **Human Face / Profile Avatar Mode**: Auto-crops to 1:1 square (600×600 px), compresses with color-accurate 80% JPEG, and outputs cache-busting filenames:
  ```text
  provider_19_2026-11-26_12-30-45.jpg
  ```
- **Web Showcase Mode**: High-efficiency WebP compression (saving 75–85% bandwidth) for service banners and shop photos.
- **Developer REST API**: Double-token authentication via `X-ScalePixel-Key` and `X-ScalePixel-Secret` (or `Bearer` token).
- **Zero Persistent Data**: Stateless in-memory execution with automated ephemeral purge cron.
- **3D Graphical Web App**: Three.js WebGL background with interactive Before & After split slider and drag-and-drop batch upload.
- **SDK Integrations**: Drop-in PHP and TypeScript SDKs included under `integrations/`.

---

### Directory Structure

```text
ScalePixel/
  ├── assets/
  │    ├── css/scalepixel.css         # 3D Glassmorphism UI
  │    └── js/
  │         ├── 3d-scene.js           # Three.js 3D floating canvas
  │         └── studio.js             # Drag-and-drop & split slider
  ├── api/v1/
  │    ├── compress.php               # Core compression endpoint
  │    ├── convert.php                # Universal format conversion
  │    ├── keys.php                   # API Key generator & usage tracking
  │    └── purge.php                  # Ephemeral cleanup cron
  ├── includes/
  │    ├── ImageProcessor.php         # GD image engine
  │    ├── Auth.php                   # Source & Secret Key validation
  │    ├── header.php                 # Top navigation & 3D canvas
  │    └── footer.php                 # Footer & legal links
  ├── storage/
  │    ├── temp/                      # Ephemeral cache (purged every hour)
  │    └── keys.json                  # API Keys registry
  ├── integrations/
  │    ├── ScalePixelClient.php       # PHP backend SDK
  │    └── ScalePixelClient.ts        # React Native mobile SDK
  ├── index.php                       # Interactive Studio & 3D Hero
  ├── api-dashboard.php               # API Keys & Usage Dashboard
  ├── docs.php                        # REST API Reference & Playground
  ├── about.php                       # Architecture & Tech
  ├── privacy.php                     # Privacy Policy
  ├── terms.php                       # Terms of Service
  ├── disclaimer.php                  # Legal Disclaimer
  └── contact.php                     # Developer Inquiries
```

---

### API Quick Start

```bash
curl -X POST http://localhost/ScalePixel/api/v1/compress.php \
  -H "X-ScalePixel-Key: sp_live_pk_scalepixel_starter" \
  -H "X-ScalePixel-Secret: sp_live_sk_a92d8f1e4c7b30129481bfe48102a991" \
  -F "file=@photo.jpg" \
  -F "mode=avatar" \
  -F "prefix=provider_19"
```
