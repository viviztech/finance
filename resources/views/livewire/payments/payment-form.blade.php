<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('payments.index') }}" wire:navigate
                class="text-gray-500 dark:text-gray-400 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Record Payment') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form wire:submit="save" class="p-6 space-y-6">
                    @if(!$loan)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Loan <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="loan_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                <option value="">Select a loan</option>
                                @foreach($loans as $l)
                                    <option value="{{ $l->id }}">{{ $l->loan_number }} - {{ $l->customer->name }} (Pending:
                                        ₹{{ number_format($l->amount_pending, 0) }})</option>
                                @endforeach
                            </select>
                            @error('loan_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @else
                        <div class="p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                            <p class="text-sm text-indigo-600 dark:text-indigo-400">Loan: <span
                                    class="font-semibold">{{ $loan->loan_number }}</span></p>
                            <p class="text-sm text-indigo-600 dark:text-indigo-400">Customer: <span
                                    class="font-semibold">{{ $loan->customer->name }}</span></p>
                            <p class="text-sm text-indigo-600 dark:text-indigo-400">Pending: <span
                                    class="font-semibold">₹{{ number_format($loan->amount_pending, 0) }}</span></p>
                        </div>
                    @endif

                    @if($loan && $loan->schedules->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Schedule
                                (Optional)</label>
                            <select wire:model.live="schedule_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                <option value="">General Payment</option>
                                @foreach($loan->schedules as $s)
                                    <option value="{{ $s->id }}">
                                        #{{ $s->installment_number }} - Due: {{ $s->due_date->format('d M') }} - Remaining:
                                        ₹{{ number_format($s->remaining_amount, 0) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (₹) <span
                                class="text-red-500">*</span></label>
                        <input type="number" wire:model="amount" min="1" step="0.01"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm text-2xl font-bold">
                        @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method <span
                                class="text-red-500">*</span></label>
                        <div class="mt-2 grid grid-cols-3 gap-3">
                            @foreach($paymentMethods as $method)
                                <label
                                    class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none
                                        {{ $payment_method === $method->value ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-500' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600' }}">
                                    <input type="radio" wire:model="payment_method" value="{{ $method->value }}"
                                        class="sr-only">
                                    <span class="flex flex-1 flex-col items-center">
                                        <span class="text-2xl">{{ $method->icon() }}</span>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">{{ $method->label() }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    @if($payment_method !== 'cash')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction
                                Reference</label>
                            <input type="text" wire:model="transaction_reference"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm"
                                placeholder="UPI ID, Cheque #, etc.">
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea wire:model="notes" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('payments.index') }}" wire:navigate
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50">Cancel</a>
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700">
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>