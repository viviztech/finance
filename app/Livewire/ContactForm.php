<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $message = '';
    public bool $submitted = false;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:100',
        'phone' => 'required|max:20',
        'company' => 'nullable|max:100',
        'message' => 'required|min:10|max:1000',
    ];

    public function submit()
    {
        $this->validate();

        // Send email to admin
        $adminEmail = config('mail.from.address', 'admin@financeflow.com');

        Mail::raw(
            "New Contact Form Submission\n\n" .
            "Name: {$this->name}\n" .
            "Email: {$this->email}\n" .
            "Phone: {$this->phone}\n" .
            "Company: {$this->company}\n\n" .
            "Message:\n{$this->message}",
            function ($mail) use ($adminEmail) {
                $mail->to($adminEmail)
                    ->from($this->email, $this->name)
                    ->subject('New FinanceFlow Inquiry from ' . $this->name);
            }
        );

        // Send confirmation to customer
        Mail::raw(
            "Hi {$this->name},\n\n" .
            "Thank you for your interest in FinanceFlow!\n\n" .
            "We have received your inquiry and will get back to you within 24 hours.\n\n" .
            "Best regards,\nThe FinanceFlow Team",
            function ($mail) {
                $mail->to($this->email)
                    ->subject('Thank you for contacting FinanceFlow');
            }
        );

        $this->submitted = true;
        $this->reset(['name', 'email', 'phone', 'company', 'message']);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
