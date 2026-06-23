# SmartBiz Desktop App

A native desktop wrapper for the SmartBiz Business Management System, built with [Electron](https://www.electronjs.org/).

## Features

- Standalone desktop application (Windows, macOS, Linux)
- System tray with minimize-to-tray support
- Native OS notifications
- Dedicated application menu with keyboard shortcuts
- Automatic updates support (configurable)

## Prerequisites

- [Node.js](https://nodejs.org/) v18 or later
- [npm](https://www.npmjs.com/)

## Quick Start

### 1. Install dependencies

```bash
cd desktop
npm install
```

### 2. Configure the app URL

Edit `config.json` to point to your SmartBiz instance:

```json
{
  "appUrl": "https://your-smartbiz-domain.com",
  "devUrl": "http://127.0.0.1:8000"
}
```

- `appUrl`: The production URL (used when the app is packaged/built)
- `devUrl`: The development URL (used when running with `npm start`)

### 3. Run in development mode

Make sure your Laravel dev server is running:

```bash
cd /path/to/smartbiz
php artisan serve
```

Then start the desktop app:

```bash
cd desktop
npm start
```

Or double-click `start.bat` (Windows).

## Building Installers

Build a distributable installer for your platform:

### Windows (.exe)

```bash
cd desktop
npm run build:win
```

The installer will be created in `desktop/dist/`.

### macOS (.dmg)

```bash
cd desktop
npm run build:mac
```

### Linux (.AppImage)

```bash
cd desktop
npm run build:linux
```

## Keyboard Shortcuts

| Shortcut | Action |
|---|---|
| `Ctrl+R` / `Cmd+R` | Refresh page |
| `Ctrl+Q` / `Cmd+Q` | Quit app |
| `Ctrl+,` / `Cmd+,` | Go to dashboard/home |
| `Ctrl+Shift+H` / `Cmd+Shift+H` | Home/Dashboard |
| `Ctrl+[` / `Cmd+[` | Navigate back |
| `Ctrl+]` / `Cmd+]` | Navigate forward |
| `F12` | Toggle Developer Tools |
| `Ctrl+Shift+=` / `Cmd+Shift+=` | Zoom in |
| `Ctrl+-` / `Cmd+-` | Zoom out |
| `Ctrl+0` / `Cmd+0` | Reset zoom |
| `F11` | Toggle fullscreen |

## System Tray

When closed, the app minimizes to the system tray instead of quitting.
- Double-click the tray icon to restore the window
- Right-click for options: Open or Quit

## Configuration

All settings are in `config.json`:

| Setting | Description | Default |
|---|---|---|
| `appUrl` | Production app URL | `http://127.0.0.1:8000` |
| `devUrl` | Development app URL | `http://127.0.0.1:8000` |
| `appName` | Application display name | `SmartBiz` |
| `windowWidth` | Default window width | `1200` |
| `windowHeight` | Default window height | `800` |
| `minWidth` | Minimum window width | `900` |
| `minHeight` | Minimum window height | `600` |
| `enableTray` | Enable system tray | `true` |
| `startMinimized` | Start minimized to tray | `false` |
| `autoUpdates` | Enable auto-updates | `false` |

## Project Structure

```
desktop/
├── main.js              # Electron main process
├── preload.js           # Secure renderer bridge
├── config.json          # App configuration
├── package.json         # npm dependencies & build config
├── start.bat            # Windows launch script
├── generate-icon.js     # Icon generation script
├── .gitignore
├── icons/
│   └── icon.png         # App icon (512x512)
└── dist/                # Build output
```
