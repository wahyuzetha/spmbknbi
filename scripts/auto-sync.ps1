param(
    [string]$Path = "C:\Users\WinPro\Herd\spmbknbi",
    [int]$IntervalSeconds = 5
)

Set-Location $Path

Write-Host "==========================================" -ForegroundColor Green
Write-Host "  Auto-Sync GitHub untuk SPMB KNBI Aktif   " -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host "Repo: https://github.com/wahyuzetha/spmbknbi" -ForegroundColor Cyan
Write-Host "Setiap ada perubahan file di localhost, sistem akan otomatis melakukan push ke GitHub." -ForegroundColor Yellow

while ($true) {
    try {
        $status = git status --porcelain
        if ($status) {
            Start-Sleep -Seconds 3
            $timestamp = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
            Write-Host "[$timestamp] Perubahan terdeteksi, sinkronisasi ke GitHub..." -ForegroundColor Cyan
            git add .
            git commit -m "Auto-update: $timestamp"
            git push origin main
            Write-Host "[$timestamp] Berhasil di-push ke GitHub!" -ForegroundColor Green
        }
    } catch {
        Write-Warning "Gagal sinkronisasi: $_"
    }
    Start-Sleep -Seconds $IntervalSeconds
}
