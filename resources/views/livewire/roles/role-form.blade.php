<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">
            {{ $isEditing ? __('Edit Role') : __('Create Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form wire:submit="save" class="space-y-6">
                    <!-- Role Name -->
                    <div>
                        <x-input-label for="name" :value="__('Role Name')" />
                        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required
                            autofocus placeholder="e.g. Branch Manager" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions</h3>
                        <p class="text-sm text-gray-500 mb-6">Select the permissions this role needs to have.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($groupedPermissions as $group => $permissions)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <h4
                                        class="font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2 flex items-center justify-between">
                                        {{ $group }}
                                        <span
                                            class="bg-white border border-gray-200 text-xs text-gray-500 py-0.5 px-2 rounded-full">{{ $permissions->count() }}</span>
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($permissions as $permission)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" wire:model="selectedPermissions"
                                                    value="{{ $permission->name }}"
                                                    class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                                                <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
                                                    {{ str_replace('.', ' ', $permission->name) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('selectedPermissions')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('roles.index') }}" wire:navigate
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center gap-2">
                            <span wire:loading.remove wire:target="save">
                                {{ $isEditing ? 'Update Role' : 'Create Role' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>