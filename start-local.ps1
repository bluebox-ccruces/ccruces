$port = 8000
Write-Host "Iniciando servidor PHP en http://localhost:$port ..."
Start-Process "http://localhost:$port/index.php"
php -S "localhost:$port" -t .
