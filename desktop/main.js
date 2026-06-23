const { app, BrowserWindow, Tray, Menu, nativeImage, Notification, ipcMain, dialog, shell } = require('electron')
const path = require('path')
const fs = require('fs')

// ── Configuration ──
const configPath = path.join(__dirname, 'config.json')
const config = JSON.parse(fs.readFileSync(configPath, 'utf-8'))

const isDev = !app.isPackaged
const APP_URL = isDev ? config.devUrl : config.appUrl

let mainWindow = null
let tray = null
let isQuitting = false

// ── App Icon ──
function getAppIcon() {
  const iconPath = path.join(__dirname, 'icons', 'icon.png')
  if (fs.existsSync(iconPath)) {
    return nativeImage.createFromPath(iconPath)
  }
  return nativeImage.createEmpty()
}

// ── Main Window ──
function createMainWindow() {
  mainWindow = new BrowserWindow({
    width: config.windowWidth,
    height: config.windowHeight,
    minWidth: config.minWidth,
    minHeight: config.minHeight,
    title: config.appName,
    icon: getAppIcon(),
    show: false,
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true,
      sandbox: false,
      spellcheck: true,
    },
  })

  // Load app
  mainWindow.loadURL(APP_URL)

  // Show when ready
  mainWindow.once('ready-to-show', () => {
    if (!config.startMinimized || isQuitting) {
      mainWindow.show()
    }
  })

  // Window state tracking
  mainWindow.on('maximize', () => mainWindow.webContents.send('window-maximized'))
  mainWindow.on('unmaximize', () => mainWindow.webContents.send('window-unmaximized'))

  // Minimize to tray instead of closing
  mainWindow.on('close', (event) => {
    if (!isQuitting && config.enableTray) {
      event.preventDefault()
      mainWindow.hide()
    }
  })

  // Handle external links
  mainWindow.webContents.setWindowOpenHandler(({ url }) => {
    shell.openExternal(url)
    return { action: 'deny' }
  })

  return mainWindow
}

// ── System Tray ──
function createTray() {
  const icon = getAppIcon()
  tray = new Tray(icon.resize({ width: 16, height: 16 }))
  tray.setToolTip(config.appName)

  const contextMenu = Menu.buildFromTemplate([
    {
      label: `Open ${config.appName}`,
      icon: icon.resize({ width: 14, height: 14 }),
      click: () => showMainWindow(),
    },
    { type: 'separator' },
    {
      label: 'Quit',
      click: () => {
        isQuitting = true
        app.quit()
      },
    },
  ])

  tray.setContextMenu(contextMenu)
  tray.on('double-click', () => showMainWindow())
}

function showMainWindow() {
  if (mainWindow) {
    mainWindow.show()
    mainWindow.focus()
  }
}

// ── Application Menu ──
function createAppMenu() {
  const template = [
    {
      label: config.appName,
      submenu: [
        { label: `About ${config.appName}`, click: () => showAboutDialog() },
        { type: 'separator' },
        {
          label: 'Settings',
          accelerator: 'CmdOrCtrl+,',
          click: () => mainWindow?.webContents.send('menu-home'),
        },
        { type: 'separator' },
        {
          label: 'Quit',
          accelerator: 'CmdOrCtrl+Q',
          click: () => {
            isQuitting = true
            app.quit()
          },
        },
      ],
    },
    {
      label: 'Edit',
      submenu: [
        { role: 'undo' },
        { role: 'redo' },
        { type: 'separator' },
        { role: 'cut' },
        { role: 'copy' },
        { role: 'paste' },
        { role: 'selectAll' },
      ],
    },
    {
      label: 'View',
      submenu: [
        {
          label: 'Home / Dashboard',
          accelerator: 'CmdOrCtrl+Shift+H',
          click: () => mainWindow?.webContents.send('menu-home'),
        },
        {
          label: 'Refresh',
          accelerator: 'CmdOrCtrl+R',
          click: () => mainWindow?.webContents.reload(),
        },
        { type: 'separator' },
        {
          label: 'Toggle Developer Tools',
          accelerator: 'F12',
          click: () => mainWindow?.webContents.toggleDevTools(),
        },
        { type: 'separator' },
        { role: 'resetZoom' },
        { role: 'zoomIn' },
        { role: 'zoomOut' },
        { type: 'separator' },
        { role: 'togglefullscreen' },
      ],
    },
    {
      label: 'Navigate',
      submenu: [
        {
          label: 'Back',
          accelerator: 'CmdOrCtrl+[',
          click: () => mainWindow?.webContents.goBack(),
        },
        {
          label: 'Forward',
          accelerator: 'CmdOrCtrl+]',
          click: () => mainWindow?.webContents.goForward(),
        },
      ],
    },
    {
      label: 'Help',
      submenu: [
        {
          label: `${config.appName} Website`,
          click: () => shell.openExternal(APP_URL),
        },
      ],
    },
  ]

  const menu = Menu.buildFromTemplate(template)
  Menu.setApplicationMenu(menu)
}

// ── About Dialog ──
function showAboutDialog() {
  dialog.showMessageBox(mainWindow, {
    type: 'info',
    title: `About ${config.appName}`,
    message: config.appName,
    detail: `Version ${app.getVersion()}\n\nBusiness Management System\n\n${APP_URL}`,
    icon: getAppIcon(),
    buttons: ['OK'],
  })
}

// ── IPC Handlers ──
function setupIPC() {
  ipcMain.handle('get-app-version', () => app.getVersion())

  ipcMain.on('show-notification', (_, { title, body }) => {
    new Notification({ title, body, icon: getAppIcon() }).show()
  })

  ipcMain.on('minimize-window', () => mainWindow?.minimize())
  ipcMain.on('maximize-window', () => {
    if (mainWindow?.isMaximized()) {
      mainWindow.unmaximize()
    } else {
      mainWindow?.maximize()
    }
  })
  ipcMain.on('close-window', () => mainWindow?.close())
  ipcMain.handle('is-maximized', () => mainWindow?.isMaximized())
}

// ── App Lifecycle ──
app.whenReady().then(() => {
  createAppMenu()
  setupIPC()
  createMainWindow()

  if (config.enableTray) {
    createTray()
  }

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createMainWindow()
    } else {
      showMainWindow()
    }
  })
})

app.on('before-quit', () => {
  isQuitting = true
})

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit()
  }
})
