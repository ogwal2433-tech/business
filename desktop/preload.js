const { contextBridge, ipcRenderer } = require('electron')

contextBridge.exposeInMainWorld('electronAPI', {
  // App info
  getAppVersion: () => ipcRenderer.invoke('get-app-version'),

  // Notifications
  showNotification: (title, body) => ipcRenderer.send('show-notification', { title, body }),
  notification: (body) => ipcRenderer.send('show-notification', { title: 'SmartBiz', body }),

  // Window controls
  minimizeWindow: () => ipcRenderer.send('minimize-window'),
  maximizeWindow: () => ipcRenderer.send('maximize-window'),
  closeWindow: () => ipcRenderer.send('close-window'),
  isMaximized: () => ipcRenderer.invoke('is-maximized'),
  onMaximizeChange: (callback) => {
    ipcRenderer.on('window-maximized', () => callback(true))
    ipcRenderer.on('window-unmaximized', () => callback(false))
  },

  // System tray
  onTrayAction: (callback) => {
    ipcRenderer.on('tray-show-window', () => callback('show'))
    ipcRenderer.on('tray-quit', () => callback('quit'))
  },

  // Menu actions
  onMenuAction: (callback) => {
    ipcRenderer.on('menu-refresh', () => callback('refresh'))
    ipcRenderer.on('menu-dev-tools', () => callback('dev-tools'))
    ipcRenderer.on('menu-home', () => callback('home'))
  },

  // Platform info
  platform: process.platform,
})
