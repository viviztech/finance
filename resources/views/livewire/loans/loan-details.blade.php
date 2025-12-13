<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('loans.index') }}" wire:navigate
                class="text-gray-500 dark:text-gray-400 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Loan: {{ $loan->loan_number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Principal</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        ₹{{ number_format($loan->principal_amount, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        ₹{{ number_format($loan->total_amount, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Paid</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        ₹{{ number_format($loan->amount_paid, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Pending</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                        ₹{{ number_format($loan->amount_pending, 0) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Loan Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Loan Details</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Customer:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-medium">{{ $loan->customer->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Loan Type:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $loan->loanType->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Start Date:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $loan->start_date->format('d M Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">End Date:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $loan->end_date->format('d M Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Status:</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    style="background-color: {{ $loan->status->color() }}20; color: {{ $loan->status->color() }};">
                                    {{ $loan->status->label() }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Issued By:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $loan->issuer?->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Agent:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $loan->assignedAgent?->name ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Schedule -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Schedule</h3>
                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        #</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Due Date</th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Amount</th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Paid</th>
                                    <th
                                        class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($loan->schedules as $schedule)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $schedule->installment_number }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $schedule->due_date->format('d M Y') }}</td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">
                                            ₹{{ number_format($schedule->amount_due, 0) }}</td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">
                                            ₹{{ number_format($schedule->amount_paid, 0) }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                style="background-color: {{ $schedule->status->color() }}20; color: {{ $schedule->status->color() }};">
                                                {{ $schedule->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payment History</h3>
                    @can('payments.create')
                        <a href="{{ route('payments.create', $loan) }}" wire:navigate
                            class="inline-flex items-center px-3 py-1.5 bg-green-600 rounded-md text-sm font-medium text-white hover:bg-green-700">
                            Record Payment
                        </a>
                    @endcan
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Receipt #</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Date</th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Amount</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Method</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Collected By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($loan->payments as $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                        {{ $payment->receipt_number }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->collected_at->format('d M Y H:i') }}</td>
                                    <td
                                        class="px-4 py-2 text-sm text-right font-semibold text-green-600 dark:text-green-400">
                                        ₹{{ number_format($payment->total_amount, 0) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->payment_method->label() }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->collector?->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No
                                        payments recorded yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>