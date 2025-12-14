<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Overdue Payments') }}
            </h2>
            @can('payments.create')
                <a href="{{ route('payments.collect') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    {{ __('Collect Payment') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total Overdue Count -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-100">Total Overdue Installments</p>
                            <p class="text-4xl font-bold mt-2">{{ number_format($totalOverdue) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Overdue Amount -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-100">Total Overdue Amount</p>
                            <p class="text-4xl font-bold mt-2">₹{{ number_format($totalOverdueAmount, 0) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filters Row -->
                    <div class="mb-6 flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Search by customer, phone, or loan number..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div class="sm:w-48">
                            <select wire:model.live="daysOverdueFilter"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">All Overdue</option>
                                <option value="7">1-7 Days Overdue</option>
                                <option value="15">8-15 Days Overdue</option>
                                <option value="30">16-30 Days Overdue</option>
                                <option value="31">30+ Days Overdue</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th wire:click="sortBy('customer')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <div class="flex items-center gap-1">
                                            Customer
                                            @if ($sortField === 'customer')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if ($sortDirection === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    @endif
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Loan #
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Installment
                                    </th>
                                    <th wire:click="sortBy('due_date')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <div class="flex items-center gap-1">
                                            Due Date
                                            @if ($sortField === 'due_date')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if ($sortDirection === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    @endif
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Days Overdue
                                    </th>
                                    <th wire:click="sortBy('amount')"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <div class="flex items-center gap-1 justify-end">
                                            Amount Due
                                            @if ($sortField === 'amount')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if ($sortDirection === 'asc')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    @endif
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Agent
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($overdueSchedules as $schedule)
                                    @php
                                        $daysOverdue = $schedule->due_date->diffInDays(today());
                                        $remainingAmount = $schedule->amount_due + $schedule->penalty_amount - $schedule->amount_paid;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                    {{ substr($schedule->loan->customer->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $schedule->loan->customer->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $schedule->loan->customer->phone }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('loans.show', $schedule->loan->id) }}" wire:navigate
                                                class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                {{ $schedule->loan->loan_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                {{ $schedule->installment_number }} / {{ $schedule->loan->total_installments }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $schedule->due_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $badgeColor = match(true) {
                                                    $daysOverdue > 30 => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border-red-200',
                                                    $daysOverdue > 15 => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 border-orange-200',
                                                    $daysOverdue > 7 => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 border-yellow-200',
                                                    default => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 border-amber-200'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeColor }}">
                                                {{ $daysOverdue }} days
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div>
                                                <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                                    ₹{{ number_format($remainingAmount, 0) }}
                                                </span>
                                                @if ($schedule->penalty_amount > 0)
                                                    <div class="text-xs text-orange-500">
                                                        +₹{{ number_format($schedule->penalty_amount, 0) }} penalty
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $schedule->status->color() === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                                {{ $schedule->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $schedule->loan->assignedAgent?->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @can('payments.create')
                                                <a href="{{ route('payments.create', $schedule->loan->id) }}" wire:navigate
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-lg font-medium text-xs text-white hover:bg-green-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Collect
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No Overdue Payments!</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All payments are up to date. Great job!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $overdueSchedules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
