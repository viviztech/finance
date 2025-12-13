<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Dashboard</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Cards - Wavepay Style -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Today's Collection -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Today's Collection</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['todayCollection'], 0) }}</p>
                            <span class="text-xs font-medium px-1.5 py-0.5 rounded bg-green-100 text-green-700">
                                ↑ {{ $stats['collectionPercentage'] }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Loans -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Active Loans</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['activeLoans'] }}</p>
                            <span class="text-xs text-gray-400">of {{ $stats['totalLoans'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pending -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Pending</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['totalPending'], 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow {{ $stats['overdueCount'] > 0 ? 'border-red-200' : '' }}">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 {{ $stats['overdueCount'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 {{ $stats['overdueCount'] > 0 ? 'text-red-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Overdue</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-2xl font-bold {{ $stats['overdueCount'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['overdueCount'] }}</p>
                            @if($stats['overdueCount'] > 0)
                                <span class="text-xs font-medium px-1.5 py-0.5 rounded bg-red-100 text-red-700">Alert</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                    @can('loans.create')
                        <a href="{{ route('loans.create') }}" wire:navigate
                            class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-700">New Loan</span>
                        </a>
                    @endcan
                    @can('payments.create')
                        <a href="{{ route('payments.collect') }}" wire:navigate
                            class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-700">Collect</span>
                        </a>
                    @endcan
                    @can('customers.create')
                        <a href="{{ route('customers.create') }}" wire:navigate
                            class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-700">Customer</span>
                        </a>
                    @endcan
                    @can('reports.view-branch')
                        <a href="{{ route('reports.branch') }}" wire:navigate
                            class="flex flex-col items-center p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors group">
                            <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-700">Reports</span>
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Recent Loans -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Recent Loans</h3>
                    <a href="{{ route('loans.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700 font-medium">See all</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentLoans->take(4) as $loan)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-semibold text-xs">
                                    {{ substr($loan->customer->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $loan->customer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $loan->loan_number }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">₹{{ number_format($loan->principal_amount, 0) }}</p>
                                <p class="text-xs text-gray-500">{{ $loan->start_date->format('d M') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4 text-sm">No recent loans</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Recent Payments</h3>
                    <a href="{{ route('payments.index') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700 font-medium">See all</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentPayments->take(4) as $payment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-semibold text-xs">
                                    {{ substr($payment->loan->customer->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $payment->loan->customer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->receipt_number }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-green-600">+₹{{ number_format($payment->total_amount, 0) }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->collected_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4 text-sm">No recent payments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Overdue Section -->
        @if($overdueSchedules->count() > 0)
            <div class="bg-red-50 rounded-2xl p-6 border border-red-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-semibold text-red-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Overdue Payments
                    </h3>
                    <span class="px-2 py-1 bg-red-200 text-red-800 text-xs font-medium rounded-lg">{{ $overdueSchedules->count() }} items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-red-700 uppercase border-b border-red-200">
                                <th class="pb-2 pr-4">Customer</th>
                                <th class="pb-2 pr-4">Loan</th>
                                <th class="pb-2 pr-4">Due Date</th>
                                <th class="pb-2 pr-4">Days</th>
                                <th class="pb-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-100">
                            @foreach($overdueSchedules->take(5) as $schedule)
                                <tr>
                                    <td class="py-2 pr-4 text-sm font-medium text-red-900">{{ $schedule->loan->customer->name }}</td>
                                    <td class="py-2 pr-4 text-sm text-red-700">{{ $schedule->loan->loan_number }}</td>
                                    <td class="py-2 pr-4 text-sm text-red-700">{{ $schedule->due_date->format('d M Y') }}</td>
                                    <td class="py-2 pr-4">
                                        <span class="px-2 py-0.5 bg-red-200 text-red-800 text-xs font-medium rounded">{{ $schedule->days_overdue }}d</span>
                                    </td>
                                    <td class="py-2 text-sm font-semibold text-red-900 text-right">₹{{ number_format($schedule->remaining_amount, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>