# Gaming Community Microservices

Project tugas besar microservices Laravel dengan tema Gaming Community Hub.

## Deskripsi Aplikasi

Gaming Community Hub adalah aplikasi untuk mengelola komunitas gaming, seperti tim esport, roster pemain, jadwal pertandingan, statistik KDA, dan data pendukung lainnya.

## Arsitektur Microservices

Aplikasi ini menggunakan tiga service utama:

1. auth-service  
   Digunakan untuk login, register, JWT token, dan role user.

2. project-service  
   Digunakan untuk mengelola data utama seperti tim esport, roster pemain, jadwal pertandingan, dan statistik KDA.

3. gateway-frontend  
   Digunakan sebagai tampilan utama aplikasi yang diakses melalui browser.

## Port Service

- Gateway Frontend: 8080
- Auth Service: 8001
- Project Service: 8002

## Deployment

Aplikasi dijalankan di Ubuntu Server menggunakan Docker Compose.

## Cara Menjalankan

```bash
docker compose build
docker compose up -d
docker compose ps
