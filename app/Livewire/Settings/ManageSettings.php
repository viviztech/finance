<?php

namespace App\Livewire\Settings;

use App\Services\SettingsService;
use Livewire\Component;

class ManageSettings extends Component
{
    public $settings = [];
    public $activeTab = 'general';

    protected $rules = [
        'settings.site_name' => 'required|string',
        'settings.contact_email' => 'required|email',
        'settings.currency_symbol' => 'required|string',

        'settings.min_loan_principal' => 'required|integer|min:1',
        'settings.max_loan_principal' => 'required|integer|gte:settings.min_loan_principal',
        'settings.default_interest_rate' => 'required|numeric|min:0',

        'settings.customer_require_email' => 'boolean',
        'settings.customer_require_phone' => 'boolean',
        'settings.customer_require_address' => 'boolean',
    ];

    public function mount(SettingsService $settingsService)
    {
        // Load existing settings or defaults
        $this->settings = [
            'site_name' => $settingsService->get('site_name', 'Finance App'),
            'contact_email' => $settingsService->get('contact_email', 'info@nkbbtechnologies.com'),
            'currency_symbol' => $settingsService->get('currency_symbol', '₹'),

            'min_loan_principal' => $settingsService->get('min_loan_principal', 500),
            'max_loan_principal' => $settingsService->get('max_loan_principal', 1000000),
            'default_interest_rate' => $settingsService->get('default_interest_rate', 10),

            'customer_require_email' => $settingsService->get('customer_require_email', false),
            'customer_require_phone' => $settingsService->get('customer_require_phone', true),
            'customer_require_address' => $settingsService->get('customer_require_address', true),
        ];
    }

    public function save(SettingsService $settingsService)
    {
        $this->validate();

        // General
        $settingsService->set('site_name', $this->settings['site_name'], 'general');
        $settingsService->set('contact_email', $this->settings['contact_email'], 'general');
        $settingsService->set('currency_symbol', $this->settings['currency_symbol'], 'general');

        // Loans
        $settingsService->set('min_loan_principal', $this->settings['min_loan_principal'], 'loans', 'integer');
        $settingsService->set('max_loan_principal', $this->settings['max_loan_principal'], 'loans', 'integer');
        $settingsService->set('default_interest_rate', $this->settings['default_interest_rate'], 'loans', 'integer'); // Stored as integer percentage ideally, or float

        // Customers
        $settingsService->set('customer_require_email', $this->settings['customer_require_email'], 'customers', 'boolean');
        $settingsService->set('customer_require_phone', $this->settings['customer_require_phone'], 'customers', 'boolean');
        $settingsService->set('customer_require_address', $this->settings['customer_require_address'], 'customers', 'boolean');

        session()->flash('success', 'Settings saved successfully!');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings.manage-settings')
            ->layout('layouts.app', ['header' => 'Settings']);
    }
}
