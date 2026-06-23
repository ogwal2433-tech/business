@echo off
setlocal

set WRAPPER_DIR=%~dp0
set ARGS=%*

set ARGS=%ARGS:-snld=-snl-%

"%WRAPPER_DIR%7z-real.exe" %ARGS%
if %ERRORLEVEL% EQU 2 exit /b 0
exit /b %ERRORLEVEL%