param(
    [Parameter(Mandatory=$false)]
    [string]$ProjectPath = "E:\Side_Hustles\LoyaltyServiceClub\aleksa",
    
    [Parameter(Mandatory=$false)]
    [string]$OutputFile = "LaravelCodebase.txt",
    
    [Parameter(Mandatory=$false)]
    [switch]$IncludeTests = $true
)

# Function to add content to output file with section headers
function Add-ContentWithHeader {
    param (
        [string]$FilePath,
        [string]$HeaderText,
        [string]$OutputPath
    )
    
    if (Test-Path $FilePath) {
        Add-Content -Path $OutputPath -Value "`n`n# ==================== $HeaderText ===================="
        Add-Content -Path $OutputPath -Value "# File: $FilePath"
        Add-Content -Path $OutputPath -Value (Get-Content -Path $FilePath -Raw)
    }
}

# Function to process directory recursively
function Add-DirectoryContent {
    param (
        [string]$DirectoryPath,
        [string]$HeaderText,
        [string]$OutputPath,
        [string[]]$ExcludePatterns = @()
    )
    
    if (Test-Path $DirectoryPath) {
        Add-Content -Path $OutputPath -Value "`n`n# ==================== $HeaderText ===================="
        
        $files = Get-ChildItem -Path $DirectoryPath -Recurse -File | 
                Where-Object {
                    $file = $_
                    $exclude = $false
                    foreach ($pattern in $ExcludePatterns) {
                        if ($file.FullName -like $pattern) {
                            $exclude = $true
                            break
                        }
                    }
                    -not $exclude
                }
        
        foreach ($file in $files) {
            Add-Content -Path $OutputPath -Value "`n# File: $($file.FullName.Substring($ProjectPath.Length + 1))"
            Add-Content -Path $OutputPath -Value (Get-Content -Path $file.FullName -Raw)
        }
    }
}

# Create or clear the output file
Set-Content -Path $OutputFile -Value "# Laravel Codebase Export - $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"

# Essential configuration files
$configFiles = @(
    "composer.json",
    "package.json",
    "tailwind.config.js",
    "vite.config.js"
)

foreach ($file in $configFiles) {
    Add-ContentWithHeader -FilePath (Join-Path $ProjectPath $file) -HeaderText "Configuration - $file" -OutputPath $OutputFile
}

# Core application code
$directories = @{
    "app" = @{
        "header" = "Application Code"
        "exclude" = @()
    }
    "config" = @{
        "header" = "Configuration Files"
        "exclude" = @("*.cache")
    }
    "database" = @{
        "header" = "Database Files"
        "exclude" = @("*.sqlite")
    }
    "routes" = @{
        "header" = "Routes"
        "exclude" = @()
    }
    "resources\views" = @{
        "header" = "Views"
        "exclude" = @()
    }
    "resources\js" = @{
        "header" = "JavaScript"
        "exclude" = @("node_modules\*")
    }
    "resources\css" = @{
        "header" = "CSS"
        "exclude" = @()
    }
    "lang" = @{
        "header" = "Language Files"
        "exclude" = @()
    }
}

# Process each directory
foreach ($dir in $directories.Keys) {
    $dirPath = Join-Path $ProjectPath $dir
    Add-DirectoryContent -DirectoryPath $dirPath `
                        -HeaderText $directories[$dir]["header"] `
                        -OutputPath $OutputFile `
                        -ExcludePatterns $directories[$dir]["exclude"]
}

# Include tests if specified
if ($IncludeTests) {
    Add-DirectoryContent -DirectoryPath (Join-Path $ProjectPath "tests") `
                        -HeaderText "Tests" `
                        -OutputPath $OutputFile
}

# Add essential bootstrap files
Add-DirectoryContent -DirectoryPath (Join-Path $ProjectPath "bootstrap") `
                    -HeaderText "Bootstrap Files" `
                    -OutputPath $OutputFile `
                    -ExcludePatterns @("*cache*")

Write-Output "Laravel codebase has been exported to: $OutputFile"