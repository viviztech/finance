<div class="w-full">
    @if($submitted)
        <div class="text-center py-12 px-6 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl">
            <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Message Sent Successfully!</h3>
            <p class="text-slate-400 mb-6">We'll get back to you within 24 hours.</p>
            <button wire:click="$set('submitted', false)" class="text-emerald-400 hover:text-emerald-300 font-medium">
                Send another message →
            </button>
        </div>
    @else
        <form wire:submit="submit" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Full Name *</label>
                    <input type="text" wire:model="name" placeholder="John Doe"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    @error('name') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email Address *</label>
                    <input type="email" wire:model="email" placeholder="john@company.com"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    @error('email') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Phone Number *</label>
                    <input type="tel" wire:model="phone" placeholder="+1 234 567 8900"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    @error('phone') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Company Name</label>
                    <input type="text" wire:model="company" placeholder="Your Company Ltd."
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    @error('company') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Your Message *</label>
                <textarea wire:model="message" rows="5"
                    placeholder="Tell us about your requirements, number of branches, expected loan volume, etc."
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition resize-none"></textarea>
                @error('message') <span class="text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit"
                class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl font-bold text-lg hover:scale-[1.02] active:scale-[0.98] transition-transform shadow-[0_0_40px_rgba(16,185,129,0.3)] flex items-center justify-center gap-3"
                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                <span wire:loading.remove>Send Message & Get Quote</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Sending...
                </span>
            </button>

            <p class="text-center text-slate-500 text-sm">
                🔒 Your information is secure and will never be shared.
            </p>
        </form>
    @endif
</div>