@echo off
title SPMB KNBI - Auto Sync to GitHub
echo Memulai Auto-Sync ke GitHub repository: wahyuzetha/spmbknbi...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0scripts\auto-sync.ps1"
pause
