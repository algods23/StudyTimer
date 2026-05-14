# Study Timer Desktop Build

This project uses Electron to run the Laravel app as a Windows desktop application.

## Folder Structure

```text
StudyTimer/
├── electron/
│   ├── main.cjs              # Starts Laravel and opens Electron window
│   ├── loading.html          # Loading screen while Laravel boots
│   └── runtime/php/          # Generated PHP runtime copied during build
├── scripts/
│   ├── prepare-electron-build.cjs
│   └── build-electron.cjs
├── dist/
│   └── StudyTimerSetup.exe   # Generated Windows installer
├── app/
├── bootstrap/
├── public/
├── resources/
├── routes/
├── vendor/
└── package.json
```

## Commands

Install Electron dependencies:

```powershell
npm.cmd install --save-dev electron electron-builder
```

Build the Laravel frontend:

```powershell
npm.cmd run build
```

Copy the local PHP runtime into the Electron package:

```powershell
npm.cmd run electron:prepare
```

Build the Windows installer:

```powershell
npm.cmd run electron:build
```

The installer is created at:

```text
dist/StudyTimerSetup.exe
```

## Runtime Behavior

- Electron opens a 1200x800 desktop window.
- A loading screen appears while Laravel starts.
- The app finds a free local port and runs `php artisan serve`.
- On first launch, migrations run automatically against a SQLite database.
- Desktop data is stored in the user's app data directory, not in the install folder.
