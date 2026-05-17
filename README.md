# ⏱️ Study Timer — Premium Desktop App

Welcome to **Study Timer**, a gorgeous, modern, state-of-the-art study productivity and time-management companion built specifically for Windows. This application helps you stay organized, set smart cascading targets, plan subject routines, and queue up productive intervals seamlessly from your desktop.

---

## ✨ Features Built For Success

- **⏱️ Advanced Multi-Segment Study Playlist:**
  *Construct custom interval routines (e.g., 10m Math ➔ 5m Break ➔ 10m Science ➔ 5m Break ➔ 15m English). The timer chimes between slots, tracks progress on a gorgeous horizontal timeline, and logs finished subject sessions automatically.*
- **📅 Weekly Schedule Planner:**
  *Plan your study hours across all 7 days of the week. Supports subject categorizations, color-coded tags, and goal trackers.*
- **🛡️ Overlapping Conflict Prevention:**
  *Never double-book your time. The database-level conflict checker blocks overlapping schedule slots and guides you to perfect time-blocking.*
- **📊 Bi-directional Cascading Goals:**
  *Type into Daily, Weekly, or Monthly goal inputs, and the system instantly cascades and aligns all targets across your statistics and progress bars.*
- **📈 Rich Analytics & Subject Balance:**
  *Interactive charts, completion percentages, and subject breakdown statistics keep you accountable.*

---

## 💾 Quick Installation Guide (Using Setup Executable)

Follow these simple steps to install the desktop application on any Windows computer:

### 1️⃣ Locate the Installer
The pre-compiled Windows installer is saved directly inside your project directory at:
```text
dist/StudyTimerSetup.exe
```

### 2️⃣ Start the Installation
1. Double-click the **`StudyTimerSetup.exe`** file.
2. If Windows Defender SmartScreen asks for confirmation, click **"More Info"** and then select **"Run anyway"** (this is normal for self-signed developer executables).

### 3️⃣ Configure Options
1. The custom NSIS installer window will appear.
2. Select whether to install the application for all users or just your local Windows account.
3. Click **Next**.
4. (Optional) Choose the destination folder path (defaults to your program files directory).

### 4️⃣ Complete Installation & Launch
1. Ensure **"Create Desktop Shortcut"** is checked.
2. Click **Install**.
3. Once the progress bar fills up, click **Finish**.
4. **Study Timer** will launch instantly, and a shortcut will be pinned to your Windows desktop/Start Menu!

---

## 🛠️ Developer Rebuild & Compilation Guide

If you modify the source code and want to compile a fresh, updated `StudyTimerSetup.exe` installer, open your terminal in the project root folder and execute:

### 1. Install Node Dependencies
```powershell
npm.cmd install
```

### 2. Package and Build Executable
```powershell
npm.cmd run dist
```
This automated developer pipeline will:
- Re-compile the Vite production assets.
- Prepare and bundle the local PHP runtime.
- Run `electron-builder` to package, compress, sign, and output a new **`StudyTimerSetup.exe`** installer in the `dist/` directory.

---

*Enjoy distraction-free, focused study sessions with **Study Timer**!*
