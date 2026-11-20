# Enables WSL, imports Ubuntu 24.04, and runs deploy/ubuntu/install.sh.
# Requires an elevated PowerShell. Logs to %USERPROFILE%\miuwish-wsl-setup.log.
$ErrorActionPreference = 'Continue'
$log = Join-Path $env:USERPROFILE 'miuwish-wsl-setup.log'

function Log([string]$Message) {
    $line = '{0} {1}' -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $Message
    Add-Content -Path $log -Value $line -Encoding utf8
}

Log "start $(whoami)"
$identity = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($identity)
$admin = $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
Log "isAdmin=$admin"
if (-not $admin) {
    Log 'NOT_ADMIN'
    exit 1
}

$reboot = $false
foreach ($feature in @('Microsoft-Windows-Subsystem-Linux', 'VirtualMachinePlatform')) {
    $info = & dism.exe /online /Get-FeatureInfo "/FeatureName:$feature" | Out-String
    Log $info.Trim()
    if ($info -notmatch 'State : Enabled') {
        $enable = & dism.exe /online /Enable-Feature "/FeatureName:$feature" /All /NoRestart | Out-String
        Log $enable.Trim()
        if ($LASTEXITCODE -eq 3010) { $reboot = $true }
    }
}

Log 'wsl --install --no-distribution'
& wsl.exe --install --no-distribution *>&1 | ForEach-Object { Log "$_" }
Log "wsl --install exit $LASTEXITCODE"
& wsl.exe --set-default-version 2 *>&1 | ForEach-Object { Log "$_" }
Log "set-default-version exit $LASTEXITCODE"
& wsl.exe --status *>&1 | ForEach-Object { Log "$_" }
if ($reboot) { Log 'REBOOT_REQUIRED' }

Log 'start vmcompute'
& sc.exe start vmcompute *>&1 | ForEach-Object { Log "$_" }
& sc.exe start hns *>&1 | ForEach-Object { Log "$_" }

$tar = Join-Path $env:USERPROFILE 'ubuntu-noble-wsl-amd64-wsl.rootfs.tar.gz'
$url = 'https://cloud-images.ubuntu.com/wsl/releases/24.04/current/ubuntu-noble-wsl-amd64-wsl.rootfs.tar.gz'
if (-not (Test-Path $tar) -or (Get-Item $tar).Length -lt 100MB) {
    Log "downloading $url"
    & curl.exe -L --fail --retry 3 -o $tar $url
    Log "curl exit $LASTEXITCODE size $((Get-Item $tar).Length)"
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
}

$name = 'Ubuntu-24.04'
$dest = Join-Path $env:USERPROFILE 'Ubuntu-24.04'
& wsl.exe -d $name -u root -- true *>$null
$haveDistro = ($LASTEXITCODE -eq 0)
Log "distro-exists=$haveDistro"

if (-not $haveDistro) {
    if (Test-Path $dest) { Remove-Item -Recurse -Force $dest }
    New-Item -ItemType Directory -Force -Path $dest | Out-Null
    Log 'import version 2'
    & wsl.exe --import $name $dest $tar --version 2 *>&1 | ForEach-Object { Log "$_" }
    Log "import v2 exit $LASTEXITCODE"
    if ($LASTEXITCODE -ne 0) {
        Log 'import version 1'
        if (Test-Path $dest) { Remove-Item -Recurse -Force $dest }
        New-Item -ItemType Directory -Force -Path $dest | Out-Null
        & wsl.exe --import $name $dest $tar --version 1 *>&1 | ForEach-Object { Log "$_" }
        Log "import v1 exit $LASTEXITCODE"
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    }
}

$confPath = Join-Path $env:USERPROFILE 'miuwish-wsl.conf'
[System.IO.File]::WriteAllText($confPath, "[boot]`nsystemd=true`n`n[user]`ndefault=root`n")
$wslConf = ((& wsl.exe -d $name -u root -- wslpath -a $confPath) -replace "`0", '').Trim()
$installPath = Join-Path $env:USERPROFILE 'miuwish-install.sh'
$wslInstall = ((& wsl.exe -d $name -u root -- wslpath -a $installPath) -replace "`0", '').Trim()
Log "wslConf=$wslConf"
Log "wslInstall=$wslInstall"
& wsl.exe -d $name -u root -- cp $wslConf /etc/wsl.conf
Log "wsl.conf exit $LASTEXITCODE"
Log 'wsl --shutdown'
& wsl.exe --shutdown
Start-Sleep -Seconds 3

Log 'install.sh'
& wsl.exe -d $name -u root -- bash $wslInstall *>&1 | ForEach-Object { Log "$_" }
Log "install.sh exit $LASTEXITCODE"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

$crt = '\\wsl$\Ubuntu-24.04\etc\nginx\ssl\miuwish.crt'
if (Test-Path $crt) {
    & certutil.exe -addstore -f Root $crt *>&1 | ForEach-Object { Log "$_" }
    Log "certutil exit $LASTEXITCODE"
} else {
    Log 'CERT_MISSING'
}

Unregister-ScheduledTask -TaskName 'MiuWishUbuntuSetup' -Confirm:$false -ErrorAction SilentlyContinue
Log 'DONE'
Set-Content -Path (Join-Path $env:USERPROFILE 'miuwish-ready.txt') -Value 'https://localhost' -Encoding ascii
exit 0
