@php
    $footerBg = $settings->footer_bg_color ?? '#4CAF50';
    $footerText = $settings->footer_text_color ?? '#ffffff';
    $footerBottomBg = $settings->footer_bottom_bg_color ?? '#1B5E20';
    $footerBottomText = $settings->footer_bottom_text_color ?? '#ffffff';
@endphp

<footer style="background-color: {{ $footerBg }}; color: {{ $footerText }};" class="pt-20 pb-16 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-12">
        <div>
            <h4 class="text-2xl font-black mb-6">About us</h4>
            <p class="text-sm leading-relaxed mb-6 opacity-90 font-medium">{{ $settings->footer_about ?? 'Empowering your health naturally with safe and effective herbal supplements. Trusted by thousands.' }}</p>
            @if($settings->footer_email) <p class="text-sm mb-3"><strong class="font-black opacity-100">Mail:</strong> <span class="opacity-90">{{ $settings->footer_email }}</span></p> @endif
            @if($settings->footer_phone) <p class="text-sm mb-3"><strong class="font-black opacity-100">Contact:</strong><br><span class="opacity-90">{{ $settings->footer_phone }}</span></p> @endif
            @if($settings->footer_whatsapp) <p class="text-sm mb-3"><strong class="font-black opacity-100">WhatsApp:</strong><br><span class="opacity-90">{{ $settings->footer_whatsapp }}</span></p> @endif
            @if($settings->footer_address) <p class="text-sm mb-3 mt-5"><strong class="font-black opacity-100">Address:</strong> <span class="opacity-90">{{ $settings->footer_address }}</span></p> @endif
        </div>
        <div>
            <h4 class="text-2xl font-black mb-6">Quick links</h4>
            <ul class="space-y-4 text-sm font-bold opacity-90">
                @if(is_array($settings->footer_quick_links))
                    @foreach($settings->footer_quick_links as $link)
                        @php
                            $footerUrl = $link['url'] ?? '/';
                            if (str_starts_with($footerUrl, '/')) {
                                $footerUrl = tenant_store_url($footerUrl);
                            }
                        @endphp
                        <li><a href="{{ $footerUrl }}" class="hover:opacity-100 hover:underline transition">{{ $link['label'] ?? '' }}</a></li>
                    @endforeach
                @else
                    <li><a href="{{ tenant_store_url('/') }}" class="hover:opacity-100 hover:underline transition">Home</a></li>
                    <li><a href="{{ tenant_store_url('/collection') }}" class="hover:opacity-100 hover:underline transition">Shop</a></li>
                @endif
            </ul>
        </div>
        <div>
            <h4 class="text-2xl font-black mb-6">Policies</h4>
            <ul class="space-y-4 text-sm font-bold opacity-90">
                @if(is_array($settings->footer_policies_links))
                    @foreach($settings->footer_policies_links as $link)
                        @php
                            $footerUrl = $link['url'] ?? '#';
                            if (str_starts_with($footerUrl, '/')) {
                                $footerUrl = tenant_store_url($footerUrl);
                            }
                        @endphp
                        <li><a href="{{ $footerUrl }}" class="hover:opacity-100 hover:underline transition">{{ $link['label'] ?? '' }}</a></li>
                    @endforeach
                @else
                    <li><a href="#" class="hover:opacity-100 hover:underline transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:opacity-100 hover:underline transition">Refund Policy</a></li>
                @endif
            </ul>
        </div>
        <div>
            <h4 class="text-2xl font-black mb-6 leading-tight">Subscribe to our emails</h4>
            <p class="text-sm mb-6 opacity-90 leading-relaxed font-medium">{{ $settings->footer_newsletter_text ?? 'Join our email list for exclusive offers and the latest news.' }}</p>
            <form class="newsletter-signup-form flex flex-col gap-3">
                <input type="email" placeholder="Email Address" required class="w-full bg-black/10 border-0 rounded-xl py-4 px-5 text-sm placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white transition font-bold">
                <button type="submit" style="color: {{ $footerBg }};" class="w-full bg-white font-black py-4 px-5 rounded-xl hover:bg-gray-100 transition shadow-lg transform hover:-translate-y-1">Subscribe Now</button>
            </form>
        </div>
    </div>
</footer>
<div style="background-color: {{ $footerBottomBg }}; color: {{ $footerBottomText }};" class="py-6 text-center text-xs font-bold">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 opacity-90">
        &copy; {{ date('Y') }}, {{ $settings->footer_copyright ?? strtoupper($tenantId) . ' All rights reserved' }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.newsletter-signup-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = form.querySelector('input[type="email"]').value;
            var btn = form.querySelector('button');
            var origText = btn.textContent;
            btn.textContent = 'Subscribing...';
            btn.disabled = true;

            fetch('{{ tenant_store_url("/newsletter-subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ email: email }),
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btn.textContent = 'Subscribed!';
                btn.style.backgroundColor = '#10b981';
                btn.style.color = '#ffffff';
                form.querySelector('input[type="email"]').value = '';
                setTimeout(function() {
                    btn.textContent = origText;
                    btn.style.backgroundColor = '';
                    btn.style.color = '';
                    btn.disabled = false;
                }, 3000);
            })
            .catch(function() {
                btn.textContent = 'Error - Try again';
                btn.disabled = false;
                setTimeout(function() {
                    btn.textContent = origText;
                }, 3000);
            });
        });
    });
});
</script>
