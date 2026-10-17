# Auto-Sync script for SPMB KNBI to GitHub
param(
    [string]$Path = "C:\Users\WinPro\Herd\spmbknbi",
    [int]$DebounceSeconds = 5
)

Set-Location $Path

Write-Host "==========================================" -ForegroundColor Green
Write-Host "  Auto-Sync GitHub untuk SPMB KNBI Aktif   " -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host "Repo: https://github.com/wahyuzetha/spmbknbi" -ForegroundColor Cyan
Write-Host "Setiap kali Anda mengubah file di localhost, perubahan akan otomatis di-push ke GitHub." -ForegroundColor Yellow

$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = $Path
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

$script:lastChange = [DateTime]::MinValue
$script:pendingPush = $false

$action = {
    param($source, $e)
    $name = $e.FullPath
    if ($name -match '(\.git|vendor|node_modules|storage\\framework|storage\\logs|\.env)') {
        return
    }
    $script:lastChange = [DateTime]::Now
    $script:pendingPush = $true
}

Register-ObjectEvent $watcher "Changed" -Action $action | Out-Null
Register-ObjectEvent $watcher "Created" -Action $action | Out-Null
Register-ObjectEvent $watcher "Deleted" -Action $action | Out-Null
Register-ObjectEvent $watcher "Renamed" -Action $action | Out-Null

while ($true) {
    Start-Sleep -Seconds 2
    if ($script:pendingPush -and (([DateTime]::Now - $script:lastChange).TotalSeconds -ge $DebounceSeconds)) {
        $script:pendingPush = $false
        
        $status = git status --porcelain
        if ($status) {
            $timestamp = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
            Write-Host "[$timestamp] Perubahan terdeteksi, melakukan sync ke GitHub..." -ForegroundColor Cyan
            git add .
            git commit -m "Auto-update: $timestamp"
            git push origin main
            Write-Host "[$timestamp] Berhasil di-push ke GitHub!" -ForegroundColor Green
        }
    }
}
