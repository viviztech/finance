<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FinanceFlow') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .auth-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .brand-blue {
            color: #2563eb;
        }

        .btn-brand {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-brand:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
        }

        .card-shadow {
            box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.1);
        }

        .input-focus:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen auth-gradient flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 to-blue-800 p-12 flex-col justify-between">
            <div>
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">FinanceFlow</span>
                </div>
            </div>

            <!-- Hero Content -->
            <div class="space-y-6">
                <h1 class="text-4xl font-bold text-white leading-tight">
                    Manage Your Finances<br>with Confidence
                </h1>
                <p class="text-blue-100 text-lg">
                    Streamline your loan management, track payments, and grow your business with our comprehensive
                    finance platform.
                </p>

                <!-- Stats -->
                <div class="flex gap-8 pt-8">
                    <div>
                        <p class="text-3xl font-bold text-white">₹50Cr+</p>
                        <p class="text-blue-200 text-sm">Loans Processed</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">10K+</p>
                        <p class="text-blue-200 text-sm">Active Customers</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">99.9%</p>
                        <p class="text-blue-200 text-sm">Uptime</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-blue-200 text-sm">© 2024 FinanceFlow. All rights reserved.</p>
        </div>

        <!-- Right Side - Auth Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">FinanceFlow</span>
                </div>

                <!-- Auth Card -->
                <div class="bg-white rounded-2xl card-shadow p-8">
                    {{ $slot }}
                </div>

                <!-- Footer Links -->
                <div class="mt-8 text-center text-sm text-gray-500">
                    <a href="#" class="hover:text-blue-600">Terms of Service</a>
                    <span class="mx-2">•</span>
                    <a href="#" class="hover:text-blue-600">Privacy Policy</a>
                    <span class="mx-2">•</span>
                    <a href="#" class="hover:text-blue-600">Support</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>