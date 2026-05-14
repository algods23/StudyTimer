const { spawnSync } = require('node:child_process');

process.env.CSC_IDENTITY_AUTO_DISCOVERY = 'false';

const result = spawnSync('npx', ['electron-builder', '--win', 'nsis', '--x64'], {
    env: process.env,
    shell: true,
    stdio: 'inherit',
});

process.exit(result.status ?? 1);
