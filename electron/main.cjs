const { app, BrowserWindow, dialog } = require('electron');
const { spawn } = require('node:child_process');
const fs = require('node:fs');
const net = require('node:net');
const path = require('node:path');

let mainWindow;
let laravelProcess;

const isPackaged = app.isPackaged;

function appRoot() {
    return isPackaged ? app.getAppPath() : path.join(__dirname, '..');
}

function phpBinary() {
    if (!isPackaged) {
        return process.env.PHP_BINARY || 'php';
    }

    return path.join(appRoot(), 'electron', 'runtime', 'php', 'php.exe');
}

function desktopEnvironment(port) {
    const dataPath = app.getPath('userData');
    const databasePath = path.join(dataPath, 'database.sqlite');
    const storagePath = path.join(dataPath, 'storage');

    if (!fs.existsSync(databasePath)) {
        fs.mkdirSync(path.dirname(databasePath), { recursive: true });
        fs.writeFileSync(databasePath, '');
    }

    for (const directory of [
        path.join(storagePath, 'app'),
        path.join(storagePath, 'framework', 'cache', 'data'),
        path.join(storagePath, 'framework', 'sessions'),
        path.join(storagePath, 'framework', 'testing'),
        path.join(storagePath, 'framework', 'views'),
        path.join(storagePath, 'logs'),
    ]) {
        fs.mkdirSync(directory, { recursive: true });
    }

    return {
        ...process.env,
        APP_NAME: 'Study Timer',
        APP_ENV: 'production',
        APP_KEY: process.env.APP_KEY || 'base64:uWxhYJnTzLw+/zfdyDVjPOG522wu8quAlRofQ/DKvGc=',
        APP_DEBUG: 'false',
        APP_URL: `http://127.0.0.1:${port}`,
        APP_STORAGE_PATH: storagePath,
        DB_CONNECTION: 'sqlite',
        DB_DATABASE: databasePath,
        LOG_CHANNEL: 'single',
        SESSION_DRIVER: 'database',
        CACHE_STORE: 'database',
        QUEUE_CONNECTION: 'database',
    };
}

function findFreePort() {
    return new Promise((resolve, reject) => {
        const server = net.createServer();
        server.listen(0, '127.0.0.1', () => {
            const { port } = server.address();
            server.close(() => resolve(port));
        });
        server.on('error', reject);
    });
}

function runArtisan(args, env) {
    return new Promise((resolve, reject) => {
        const child = spawn(phpBinary(), ['artisan', ...args], {
            cwd: appRoot(),
            env,
            windowsHide: true,
        });

        let output = '';
        child.stdout.on('data', (data) => output += data.toString());
        child.stderr.on('data', (data) => output += data.toString());
        child.on('error', reject);
        child.on('close', (code) => {
            code === 0 ? resolve(output) : reject(new Error(output || `artisan ${args.join(' ')} failed with code ${code}`));
        });
    });
}

function waitForLaravel(url, timeoutMs = 30000) {
    const startedAt = Date.now();

    return new Promise((resolve, reject) => {
        const check = () => {
            fetch(url)
                .then((response) => response.ok ? resolve() : retry())
                .catch(retry);
        };

        const retry = () => {
            if (Date.now() - startedAt > timeoutMs) {
                reject(new Error('Laravel did not start before the timeout.'));
                return;
            }

            setTimeout(check, 500);
        };

        check();
    });
}

async function startLaravel() {
    const port = await findFreePort();
    const env = desktopEnvironment(port);

    // Migrations make the desktop SQLite database ready on first launch.
    await runArtisan(['migrate', '--force'], env);

    laravelProcess = spawn(phpBinary(), ['artisan', 'serve', '--host=127.0.0.1', `--port=${port}`], {
        cwd: appRoot(),
        env,
        windowsHide: true,
    });

    laravelProcess.on('exit', () => {
        laravelProcess = null;
    });

    const url = `http://127.0.0.1:${port}`;
    await waitForLaravel(url);

    return url;
}

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1200,
        height: 800,
        minWidth: 900,
        minHeight: 650,
        show: false,
        backgroundColor: '#fff1f2',
        webPreferences: {
            contextIsolation: true,
            nodeIntegration: false,
        },
    });

    mainWindow.once('ready-to-show', () => mainWindow.show());
    mainWindow.loadFile(path.join(__dirname, 'loading.html'));

    startLaravel()
        .then((url) => mainWindow.loadURL(url))
        .catch((error) => {
            dialog.showErrorBox('Study Timer could not start', error.message);
            app.quit();
        });
}

app.whenReady().then(createWindow);

app.on('before-quit', () => {
    if (laravelProcess) {
        laravelProcess.kill();
    }
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
        createWindow();
    }
});
