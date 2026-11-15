@echo off
echo =========================================================
echo Compiling EasyPrint Windows Standalone Executable
echo =========================================================

cd /d "%~dp0"

echo [1/3] Installing requirements...
pip install -r requirements.txt

echo [2/3] Building EasyPrint.exe with PyInstaller...
pyinstaller --clean EasyPrint.spec

echo [3/3] Deploying executable to Web Public Downloads folder...
if not exist "..\public\downloads" mkdir "..\public\downloads"
copy /y "dist\EasyPrint.exe" "..\public\downloads\EasyPrint.exe"

echo =========================================================
echo BUILD SUCCESSFUL!
echo Output file: dist\EasyPrint.exe
echo Web download: public\downloads\EasyPrint.exe
echo =========================================================
pause

