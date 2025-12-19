<!DOCTYPE html>
@inject('settings', 'App\Services\SettingsService')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - {{ $settings->get('site_name', 'FinanceFlow') }} | Get a Quote</title>
    <meta name="description"
        content="Contact us to learn more about FinanceFlow microfinance management system. Get a personalized quote today.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-slate-950 text-white min-h-screen">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-slate-950/90 backdrop-blur-lg border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-xl flex items-center justify-center">
                    <span class="text-white text-xl font-bold">F</span>
                </div>
                <span class="font-bold text-lg">{{ $settings->get('site_name', 'FinanceFlow') }}</span>
            </a>
            <a href="/" class="text-slate-400 hover:text-white text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>
        </div>
    </nav>

    <!-- Background -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-[120px]"></div>
    </div>

    <!-- Main Content -->
    <main class="relative pt-32 pb-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-16 items-start">

                <!-- Left Side - Info -->
                <div>
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/30 mb-6">
                        <span class="text-emerald-400 text-sm font-medium">💬 Let's Talk</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        Ready to Get <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-blue-400">Started?</span>
                    </h1>

                    <p class="text-xl text-slate-400 mb-10 leading-relaxed">
                        Fill out the form and our team will reach out within 24 hours to discuss your requirements and
                        provide a customized quote.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Email Us</h3>
                                <p class="text-slate-400">{{ $settings->get('contact_email',
                                    'info@nkbbtechnologies.com') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Response Time</h3>
                                <p class="text-slate-400">Within 24 hours</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">100% Secure</h3>
                                <p class="text-slate-400">Your data is never shared</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-12 p-6 bg-white/5 rounded-2xl border border-white/10">
                        <p class="text-sm text-slate-500 mb-4">WHAT YOU'LL GET:</p>
                        <ul class="space-y-3 text-slate-300">
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Personalized demo of {{ $settings->get('site_name', 'FinanceFlow') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Custom pricing based on your needs
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Free consultation with our experts
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right Side - Form -->
                <div class="bg-slate-900/50 border border-white/10 rounded-3xl p-8 md:p-10 backdrop-blur-xl">
                    <h2 class="text-2xl font-bold mb-2">Get Your Quote</h2>
                    <p class="text-slate-400 mb-8">Fill out the form below and we'll be in touch.</p>

                    <livewire:contact-form />
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-8 px-6 border-t border-white/5">
        <div class="max-w-7xl mx-auto text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ $settings->get('site_name', 'FinanceFlow') }}. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>