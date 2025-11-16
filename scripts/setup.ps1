param(
    [string]$TargetDir = "contacts-app",
    [string]$LaravelVersion = "11.*"
)

$ErrorActionPreference = "Stop"

function Assert-Command {
    param([string]$Name)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Command '$Name' is required but was not found in PATH."
    }
}

Assert-Command -Name "composer"
Assert-Command -Name "php"

if (Test-Path $TargetDir) {
    throw "Target directory '$TargetDir' already exists."
}

composer create-project laravel/laravel $TargetDir $LaravelVersion

$repoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$folders = @("app", "database", "resources", "routes")
foreach ($folder in $folders) {
    $source = Join-Path $repoRoot $folder
    if (Test-Path $source) {
        Copy-Item -Path $source -Destination (Join-Path $TargetDir $folder) -Recurse -Force
    }
}

Copy-Item (Join-Path $repoRoot ".env.example") -Destination (Join-Path $TargetDir ".env.example") -Force
Copy-Item (Join-Path $repoRoot ".env.example") -Destination (Join-Path $TargetDir ".env") -Force

$sqliteFile = Join-Path $TargetDir "database/database.sqlite"
if (-not (Test-Path $sqliteFile)) {
    New-Item -Path $sqliteFile -ItemType File | Out-Null
}

Push-Location $TargetDir
php artisan key:generate --ansi
php artisan migrate --force
php artisan db:seed --force
Pop-Location

Write-Host "[setup] All done. Run 'cd $TargetDir; php artisan serve' to start the app."
