<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FinanceFlow - #1 Microfinance Management System | Buy Now</title>
        <meta name="description" content="Launch your microfinance business in minutes. Complete loan management, branch operations, and collection tracking. Built with Laravel 12.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
            .gradient-text { background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
            .glow-button { box-shadow: 0 0 40px rgba(16, 185, 129, 0.4); }
            .glow-button:hover { box-shadow: 0 0 60px rgba(16, 185, 129, 0.6); }
        </style>
    </head>
    <body class="antialiased bg-slate-950 text-white">
        
        <!-- Floating CTA Bar -->
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-emerald-600 py-3 px-4 text-center md:hidden">
            <a href="{{ route('contact') }}" class="font-bold text-white">🚀 Get Instant Access Now →</a>
        </div>

        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-slate-950/90 backdrop-blur-lg border-b border-white/5">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl font-bold">F</span>
                    </div>
                    <div>
                        <span class="font-bold text-lg">FinanceFlow</span>
                        <span class="hidden sm:inline text-slate-400 text-sm ml-2">v1.0</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="#features" class="hidden md:block text-slate-300 hover:text-white text-sm">Features</a>
                    <a href="#pricing" class="hidden md:block text-slate-300 hover:text-white text-sm">Pricing</a>
                    <a href="{{ route('contact') }}" class="px-5 py-2 rounded-full bg-emerald-500 hover:bg-emerald-400 text-sm font-semibold transition">Get Quote</a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/20 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-[120px]"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-6 text-center py-20">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/30 mb-8">
                    <span class="animate-pulse w-2 h-2 bg-emerald-400 rounded-full"></span>
                    <span class="text-emerald-400 text-sm font-medium">🔥 Limited Time: 40% Off Launch Price</span>
                </div>

                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight">
                    Launch Your<br>
                    <span class="gradient-text">Microfinance Business</span><br>
                    In Minutes, Not Months
                </h1>

                <p class="text-xl text-slate-400 mb-8 max-w-3xl mx-auto leading-relaxed">
                    The complete, production-ready loan management system trusted by 50+ finance companies. 
                    Multi-branch operations, automated collections, real-time reports — all in one powerful package.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="{{ route('contact') }}" class="glow-button px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full font-bold text-lg hover:scale-105 transition-transform">
                        Get Your Custom Quote →
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-4 border border-white/20 rounded-full hover:bg-white/5 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                        Watch Demo
                    </a>
                </div>

                <div class="flex flex-wrap justify-center gap-6 text-sm text-slate-400">
                    <span class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Full Source Code</span>
                    <span class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Lifetime Updates</span>
                    <span class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 6 Months Support</span>
                </div>
            </div>
        </section>

        <!-- Social Proof -->
        <section class="py-16 border-y border-white/5 bg-slate-900/50">
            <div class="max-w-7xl mx-auto px-6">
                <p class="text-center text-slate-500 mb-8">TRUSTED BY LEADING MICROFINANCE INSTITUTIONS</p>
                <div class="flex flex-wrap justify-center items-center gap-12 opacity-50">
                    <span class="text-2xl font-bold text-slate-400">MicroLend</span>
                    <span class="text-2xl font-bold text-slate-400">QuickFund</span>
                    <span class="text-2xl font-bold text-slate-400">EasyCredit</span>
                    <span class="text-2xl font-bold text-slate-400">FinTrust</span>
                </div>
            </div>
        </section>

        <!-- Problem & Solution -->
        <section class="py-24 px-6">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Stop Wasting <span class="text-red-400">$50,000+</span> on Custom Development</h2>
                <p class="text-xl text-slate-400 mb-16 max-w-3xl mx-auto">
                    Building a microfinance system from scratch takes 6-12 months and costs a fortune. 
                    FinanceFlow gives you everything you need — ready to deploy today.
                </p>
                <div class="grid md:grid-cols-2 gap-8 text-left">
                    <div class="p-8 rounded-2xl bg-red-500/5 border border-red-500/20">
                        <h3 class="text-xl font-bold text-red-400 mb-4">❌ Without FinanceFlow</h3>
                        <ul class="space-y-3 text-slate-400">
                            <li>• 6-12 months development time</li>
                            <li>• $30,000 - $100,000 development cost</li>
                            <li>• Ongoing maintenance headaches</li>
                            <li>• Security vulnerabilities</li>
                            <li>• No expert support</li>
                        </ul>
                    </div>
                    <div class="p-8 rounded-2xl bg-emerald-500/5 border border-emerald-500/20">
                        <h3 class="text-xl font-bold text-emerald-400 mb-4">✅ With FinanceFlow</h3>
                        <ul class="space-y-3 text-slate-400">
                            <li>• Deploy in under 30 minutes</li>
                            <li>• One-time payment of $299</li>
                            <li>• Production-ready & tested</li>
                            <li>• Enterprise-grade security</li>
                            <li>• 6 months of priority support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 px-6 bg-slate-900/50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4">Everything You Need to <span class="gradient-text">Scale</span></h2>
                    <p class="text-xl text-slate-400">Powerful features built for real-world microfinance operations</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-emerald-500/30 transition group">
                        <div class="text-4xl mb-4">🏢</div>
                        <h3 class="text-xl font-bold mb-3">Multi-Branch Management</h3>
                        <p class="text-slate-400">Manage unlimited branches with complete data isolation. Each branch operates independently with centralized reporting.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-blue-500/30 transition group">
                        <div class="text-4xl mb-4">👥</div>
                        <h3 class="text-xl font-bold mb-3">Role-Based Access Control</h3>
                        <p class="text-slate-400">Super Admin, Branch Manager, Collection Agent — granular permissions for every team member.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-purple-500/30 transition group">
                        <div class="text-4xl mb-4">💰</div>
                        <h3 class="text-xl font-bold mb-3">Complete Loan Lifecycle</h3>
                        <p class="text-slate-400">From application to disbursement to closure. Handle daily, weekly, monthly EMI schedules with ease.</p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-amber-500/30 transition group">
                        <div class="text-4xl mb-4">📱</div>
                        <h3 class="text-xl font-bold mb-3">Daily Collection Tracking</h3>
                        <p class="text-slate-400">Field agents can record payments in real-time. Generate instant receipts for customers.</p>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-rose-500/30 transition group">
                        <div class="text-4xl mb-4">⚠️</div>
                        <h3 class="text-xl font-bold mb-3">Automated Penalty System</h3>
                        <p class="text-slate-400">Configure penalty rules once. The system automatically calculates and applies penalties for overdue payments.</p>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-cyan-500/30 transition group">
                        <div class="text-4xl mb-4">📊</div>
                        <h3 class="text-xl font-bold mb-3">Real-Time Reporting</h3>
                        <p class="text-slate-400">Branch performance, loan portfolios, collection efficiency — all the insights you need at a glance.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tech Stack -->
        <section class="py-24 px-6">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Built With Modern Technology</h2>
                <p class="text-slate-400 mb-12">Enterprise-grade stack that scales with your business</p>
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach(['Laravel 12', 'Livewire 3', 'Tailwind CSS', 'MySQL 8', 'Alpine.js', 'Spatie Permissions'] as $tech)
                        <span class="px-5 py-2 rounded-full bg-white/5 border border-white/10 text-sm">{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="pricing" class="py-24 px-6 bg-slate-900/50">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4">Simple, Transparent Pricing</h2>
                    <p class="text-xl text-slate-400">One-time payment. Lifetime access. No subscriptions.</p>
                </div>

                <div class="p-8 md:p-12 rounded-3xl bg-gradient-to-br from-emerald-500/10 to-blue-500/10 border border-emerald-500/30 text-center relative overflow-hidden">
                    <div class="absolute top-4 right-4 px-3 py-1 bg-amber-500 text-black text-xs font-bold rounded-full">40% OFF</div>
                    
                    <h3 class="text-2xl font-bold mb-2">Complete Package</h3>
                    <div class="flex items-center justify-center gap-3 mb-6">
                        <span class="text-5xl font-black">$299</span>
                        <span class="text-2xl text-slate-500 line-through">$499</span>
                    </div>
                    
                    <ul class="text-left max-w-md mx-auto space-y-4 mb-8 text-slate-300">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Full source code (100% unencrypted)</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Lifetime updates & new features</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 6 months priority support</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Detailed documentation</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Free installation assistance</li>
                    </ul>
                    
                    <a href="{{ route('contact') }}" class="inline-block glow-button px-10 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full font-bold text-lg hover:scale-105 transition-transform">
                        Contact Us & Get Quote
                    </a>
                    <p class="mt-4 text-sm text-slate-500">30-day money-back guarantee</p>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-24 px-6">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12">Frequently Asked Questions</h2>
                <div class="space-y-6">
                    @foreach([
                            ['Do I get the full source code?', 'Yes! You get 100% unencrypted source code. Modify, extend, and customize it however you want.'],
                            ['Can I use this for multiple projects?', 'The Regular License covers one project. For multiple projects or SaaS, contact us for Extended License.'],
                            ['What if I need help with installation?', 'We provide free installation assistance and detailed documentation to get you up and running quickly.'],
                            ['Is this suitable for my microfinance company?', 'Absolutely. FinanceFlow is built specifically for microfinance operations with multi-branch support, loan management, and collection tracking.'],
                        ] as $faq)
                            <div class="p-6 rounded-xl bg-white/5 border border-white/10">
                                <h3 class="font-bold text-lg mb-2">{{ $faq[0] }}</h3>
                                <p class="text-slate-400">{{ $faq[1] }}</p>
                            </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-6 bg-gradient-to-br from-emerald-900/20 to-blue-900/20">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Ready to Transform Your Business?</h2>
                <p class="text-xl text-slate-400 mb-8">Join 50+ companies already using FinanceFlow to manage their operations.</p>
                <a href="{{ route('contact') }}" class="inline-block glow-button px-10 py-5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full font-bold text-xl hover:scale-105 transition-transform">
                    Contact Us For Pricing
                </a>
                <p class="mt-6 text-slate-500">⚡ Instant download after purchase</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 px-6 border-t border-white/5">
            <div class="max-w-7xl mx-auto text-center text-slate-500 text-sm">
                <p>&copy; {{ date('Y') }} FinanceFlow. All rights reserved. Built with ❤️ using Laravel.</p>
            </div>
        </footer>
    </body>
</html>