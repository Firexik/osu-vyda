@echo off
setlocal
cd /d "%~dp0"
where php >nul 2>nul
if errorlevel 1 if exist "%USERPROFILE%\xampp\php\php.exe" set "PATH=%USERPROFILE%\xampp\php;%PATH%"
where php >nul 2>nul
if errorlevel 1 (
    echo PHP nebylo nalezeno. Pridejte PHP 8.2 nebo novejsi do PATH.
    pause
    exit /b 1
)
if not exist "vendor\autoload.php" (
    echo Nejdrive spustte composer install a importujte SQL podle README.md.
    pause
    exit /b 1
)
echo Terminy - http://127.0.0.1:8000
echo Nechte toto okno otevrene. Ukonceni serveru: Ctrl+C.
php -S 127.0.0.1:8000 -t www www/router.php
endlocal
