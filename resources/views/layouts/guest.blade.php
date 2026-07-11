<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Pengajuan Transaksi Pengeluaran') }} - Login</title>

    <link rel="icon" href="{{ asset('images/logo2.jpg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Custom CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Blobs */
        .blob-1, .blob-2 {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 8s ease-in-out infinite;
        }

        .blob-1 {
            background: rgba(43, 210, 255, 0.4);
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
        }

        .blob-2 {
            background: rgba(255, 65, 108, 0.4);
            width: 500px;
            height: 500px;
            bottom: -150px;
            right: -100px;
            animation-delay: 2s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(20px) scale(1.05); }
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            z-index: 1;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
        }

        /* Custom Input Styling */
        .form-control-custom {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }

        .form-control-custom:focus {
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Custom Button */
        .btn-primary-custom {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-custom::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.5);
            color: white;
        }

        .btn-primary-custom:hover::after {
            left: 100%;
        }

        /* Side Banner */
        .auth-banner {
            background: url('https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80') center/cover no-repeat;
            position: relative;
        }

        .auth-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 60, 114, 0.8) 0%, rgba(42, 82, 152, 0.8) 100%);
        }
    </style>
</head>
<body>

    <!-- Background Blobs -->
    <div class="blob-1"></div>
    <div class="blob-2"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card glass-card border-0">
                    <div class="row g-0">
                        <!-- Banner Side -->
                        <div class="col-md-5 d-none d-md-flex auth-banner align-items-center justify-content-center text-center p-5">
                            <div class="position-relative text-white" style="z-index: 1;">
                                <img src="{{ asset('images/logo.png') }}" alt="Lavanaya Madinah Travel" class="mb-4" style="max-height: 80px; filter: brightness(0) invert(1);">
                                <h2 class="fw-bold mb-3 text-white">Sistem Pengajuan Transaksi Pengeluaran</h2>
                                <p class="lead opacity-75 text-white">Platform Manajemen Pengeluaran & Persetujuan Keuangan Perusahaan</p>
                            </div>
                        </div>

                        <!-- Form Side -->
                        <div class="col-md-7 p-4 p-md-5">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
