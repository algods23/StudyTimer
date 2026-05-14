const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

const root = path.join(__dirname, '..');
const runtimeDir = path.join(root, 'electron', 'runtime');
const targetPhpDir = path.join(runtimeDir, 'php');

function findPhpBinary() {
    if (process.env.PHP_BINARY && fs.existsSync(process.env.PHP_BINARY)) {
        return process.env.PHP_BINARY;
    }

    const command = process.platform === 'win32' ? 'where' : 'which';
    const output = execFileSync(command, ['php'], { encoding: 'utf8' });

    return output.split(/\r?\n/).find(Boolean);
}

function copyDirectory(source, target) {
    fs.rmSync(target, { recursive: true, force: true });
    fs.mkdirSync(target, { recursive: true });

    for (const entry of fs.readdirSync(source, { withFileTypes: true })) {
        const sourcePath = path.join(source, entry.name);
        const targetPath = path.join(target, entry.name);

        if (entry.isDirectory()) {
            copyDirectory(sourcePath, targetPath);
        } else {
            fs.copyFileSync(sourcePath, targetPath);
        }
    }
}

const phpBinary = findPhpBinary();
const phpDir = path.dirname(phpBinary);

console.log(`Bundling PHP runtime from ${phpDir}`);
fs.rmSync(targetPhpDir, { recursive: true, force: true });
fs.mkdirSync(targetPhpDir, { recursive: true });

for (const entry of fs.readdirSync(phpDir, { withFileTypes: true })) {
    const sourcePath = path.join(phpDir, entry.name);
    const targetPath = path.join(targetPhpDir, entry.name);

    if (entry.isDirectory()) {
        if (entry.name === 'ext') {
            copyDirectory(sourcePath, targetPath);
        }

        continue;
    }

    fs.copyFileSync(sourcePath, targetPath);
}

fs.writeFileSync(path.join(targetPhpDir, 'php.ini'), [
    'extension_dir = "ext"',
    'extension=curl',
    'extension=fileinfo',
    'extension=mbstring',
    'extension=openssl',
    'extension=pdo_sqlite',
    'extension=sqlite3',
    'date.timezone=UTC',
    'memory_limit=256M',
    '',
].join('\n'));
console.log(`PHP runtime copied to ${targetPhpDir}`);
