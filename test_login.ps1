$body = @{
    email = "siswa@sekolah.com"
    password = "password"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/auth/login" `
    -Method POST `
    -Headers @{"Content-Type"="application/json"; "Accept"="application/json"} `
    -Body $body

$response | ConvertTo-Json -Depth 10
