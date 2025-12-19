<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex">

                <!-- Sidebar Tabs -->
                <div class="w-1/4 border-r border-gray-200 dark:border-gray-700 p-6 space-y-2">
                    <button wire:click="setTab('general')"
                        class="w-full text-left px-4 py-2 rounded-md flex justify-between items-center {{ $activeTab === 'general' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <span>General</span>
                        @if($errors->has('settings.site_name') || $errors->has('settings.currency_symbol'))
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        @endif
                    </button>
                    <button wire:click="setTab('loans')"
                        class="w-full text-left px-4 py-2 rounded-md flex justify-between items-center {{ $activeTab === 'loans' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <span>Loans</span>
                        @if($errors->has('settings.min_loan_principal') || $errors->has('settings.max_loan_principal') || $errors->has('settings.default_interest_rate'))
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        @endif
                    </button>
                    <button wire:click="setTab('customers')"
                        class="w-full text-left px-4 py-2 rounded-md flex justify-between items-center {{ $activeTab === 'customers' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <span>Customers</span>
                        @if($errors->has('settings.customer_require_email') || $errors->has('settings.customer_require_phone') || $errors->has('settings.customer_require_address'))
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        @endif
                    </button>
                </div>

                <!-- Content Area -->
                <div class="w-3/4 p-6">
                    <form wire:submit="save">
                        <!-- Global Error Message -->
                        @if ($errors->any())
                            <div
                                class="mb-4 p-4 rounded-md bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                            There were errors with your submission
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- General Settings -->
                        @if($activeTab === 'general')
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">General Settings</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site
                                        Name</label>
                                    <input type="text" wire:model="settings.site_name"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    @error('settings.site_name') <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact
                                        Email</label>
                                    <input type="email" wire:model="settings.contact_email"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    @error('settings.contact_email') <span
                                        class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency
                                        Symbol</label>
                                    <input type="text" wire:model="settings.currency_symbol"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    @error('settings.currency_symbol') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Loan Settings -->
                        @if($activeTab === 'loans')
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Loan Configuration</h3>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min
                                            Principal Amount</label>
                                        <input type="number" wire:model="settings.min_loan_principal"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                        @error('settings.min_loan_principal') <span
                                        class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max
                                            Principal Amount</label>
                                        <input type="number" wire:model="settings.max_loan_principal"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                        @error('settings.max_loan_principal') <span
                                        class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default
                                        Interest Rate (%)</label>
                                    <input type="number" step="0.01" wire:model="settings.default_interest_rate"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    @error('settings.default_interest_rate') <span
                                    class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Customer Settings -->
                        @if($activeTab === 'customers')
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Customer Validation</h3>
                                <div class="space-y-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="settings.customer_require_email"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Require Email Address</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="settings.customer_require_phone"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Require Phone Number</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="settings.customer_require_address"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Require Address</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>