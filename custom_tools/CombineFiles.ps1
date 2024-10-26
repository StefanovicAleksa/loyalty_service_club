param(
    [Parameter(Mandatory=$true)]
    [string]$FolderPath,
    
    [Parameter(Mandatory=$false)]
    [string]$OutputFile = "CombinedOutput.txt"
)

# Ensure the folder path exists
if (-not (Test-Path -Path $FolderPath -PathType Container)) {
    Write-Error "The specified folder does not exist: $FolderPath"
    exit 1
}

# Get all files in the folder and subfolders
$files = Get-ChildItem -Path $FolderPath -Recurse -File

# Create or clear the output file
Set-Content -Path $OutputFile -Value $null

# Get the full path of the main folder and ensure it ends with a backslash
$fullFolderPath = (Resolve-Path $FolderPath).Path
if (-not $fullFolderPath.EndsWith('\')) {
    $fullFolderPath += '\'
}

foreach ($file in $files) {
    # Get the relative path of the file
    $relativePath = $file.FullName.Substring($fullFolderPath.Length)
    
    # Add the relative file path as a comment
    Add-Content -Path $OutputFile -Value "# File: $relativePath"
    
    # Add the file content
    Get-Content -Path $file.FullName | Add-Content -Path $OutputFile
    
    # Add a blank line for readability
    Add-Content -Path $OutputFile -Value ""
}

Write-Output "Combined file created: $OutputFile"