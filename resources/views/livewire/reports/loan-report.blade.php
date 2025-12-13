<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Loan Report') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                        <input type="date" wire:model.live="dateFrom"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                        <input type="date" wire:model.live="dateTo"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loan Type</label>
                        <select wire:model.live="loanTypeFilter"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                            <option value="">All Types</option>
                            @foreach($loanTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Total Loans</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalLoans }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Principal</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        ₹{{ number_format($totalPrincipal, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Interest</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                        ₹{{ number_format($totalInterest, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">₹{{ number_format($totalAmount, 0) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Collected</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        ₹{{ number_format($totalCollected, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Pending</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                        ₹{{ number_format($totalPending, 0) }}</p>
                </div>
            </div>

            <!-- Loans Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Loan #</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Customer</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Type</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Principal</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Total</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Paid</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Pending</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($loans as $loan)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                        <a href="{{ route('loans.show', $loan) }}"
                                            wire:navigate>{{ $loan->loan_number }}</a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $loan->customer->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $loan->loanType->name }}</td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                        ₹{{ number_format($loan->principal_amount, 0) }}</td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                        ₹{{ number_format($loan->total_amount, 0) }}</td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                                        ₹{{ number_format($loan->amount_paid, 0) }}</td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-amber-600 dark:text-amber-400">
                                        ₹{{ number_format($loan->amount_pending, 0) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                            style="background-color: {{ $loan->status->color() }}20; color: {{ $loan->status->color() }};">
                                            {{ $loan->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $loan->start_date->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No loans
                                        found for the selected criteria</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">{{ $loans->links() }}</div>
            </div>
        </div>
    </div>
</div>