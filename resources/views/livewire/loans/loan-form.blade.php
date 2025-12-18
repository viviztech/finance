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
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Issue New Loan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <form wire:submit="save" class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="customer_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}
                                            ({{ $customer->customer_code }})</option>
                                    @endforeach
                                </select>
                                @error('customer_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Loan Type
                                    <span class="text-red-500">*</span></label>
                                <select wire:model.live="loan_type_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    <option value="">Select Loan Type</option>
                                    @foreach($loanTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->formatted_interest }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('loan_type_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Principal
                                        Amount (₹) <span class="text-red-500">*</span></label>
                                    <input type="number" wire:model.live.debounce.300ms="principal_amount" min="100"
                                        step="100"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    @error('principal_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Installments
                                        <span class="text-red-500">*</span></label>
                                    <input type="number" wire:model.live.debounce.300ms="total_installments" min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    @error('total_installments')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date
                                        <span class="text-red-500">*</span></label>
                                    <input type="date" wire:model="start_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned
                                        Agent</label>
                                    <select wire:model="assigned_agent_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                        <option value="">Select Agent</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                <textarea wire:model="notes" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm"></textarea>
                            </div>

                            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('loans.index') }}" wire:navigate
                                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50">Cancel</a>
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                                    Issue Loan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Panel -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white sticky top-6">
                        <h3 class="text-lg font-semibold mb-4">Loan Summary</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-indigo-100">Principal:</span>
                                <span class="font-semibold">₹{{ number_format((float) $principal_amount, 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-indigo-100">Interest:</span>
                                <span class="font-semibold">₹{{ number_format((float) $interest_amount, 0) }}</span>
                            </div>
                            <hr class="border-white/30">
                            <div class="flex justify-between text-lg">
                                <span class="text-indigo-100">Total:</span>
                                <span class="font-bold">₹{{ number_format((float) $total_amount, 0) }}</span>
                            </div>
                            <hr class="border-white/30">
                            <div class="flex justify-between">
                                <span class="text-indigo-100">Installments:</span>
                                <span class="font-semibold">{{ $total_installments ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-indigo-100">Per Installment:</span>
                                <span class="font-semibold">₹{{ number_format((float) $installment_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>