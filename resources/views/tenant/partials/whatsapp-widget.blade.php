@if(!empty($settings->footer_whatsapp) || !empty($settings->footer_phone))
    @php
        $widgetPhone = preg_replace('/[^0-9]/', '', $settings->footer_whatsapp ?? $settings->footer_phone ?? '');
        if (str_starts_with($widgetPhone, '03')) {
            $widgetPhone = '92' . substr($widgetPhone, 1);
        }
        $helloText = urlencode("السلام علیکم! میں آپ کے سٹور سے کچھ معلومات حاصل کرنا چاہتا/چاہتی ہوں۔\nHello! I would like to get some information from your store.");
    @endphp
    
    <!-- Floating WhatsApp Widget -->
    <a href="https://wa.me/{{ $widgetPhone }}?text={{ $helloText }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="fixed bottom-6 right-6 z-[9999] flex items-center justify-center w-14 h-14 bg-[#25D366] text-white rounded-full shadow-2xl transition-all duration-300 hover:scale-110 hover:bg-[#20ba5a] active:scale-95 group focus:outline-none focus:ring-4 focus:ring-green-300"
       title="Chat on WhatsApp"
       id="whatsapp-floating-widget">
        
        <!-- Pulsing Ring -->
        <span class="absolute inset-0 rounded-full bg-[#25D366] opacity-40 animate-ping -z-10 group-hover:animate-none"></span>
        
        <!-- WhatsApp SVG Icon -->
        <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008 0c3.202.001 6.212 1.248 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.498 1.452 5.431 1.453 5.414 0 9.82-4.397 9.823-9.799.002-2.617-1.01-5.078-2.852-6.924C17.207 2.04 14.743.996 12.13.994c-5.42 0-9.825 4.399-9.828 9.802-.001 1.84.484 3.636 1.406 5.21L2.68 21.37l5.02-1.316-.053-.03zM16.9 14.077c-.26-.13-1.539-.759-1.778-.847-.238-.087-.412-.13-.586.13-.173.26-.67.847-.82 1.02-.153.173-.305.195-.565.065-.26-.13-1.097-.404-2.09-1.288-.772-.687-1.293-1.537-1.444-1.797-.152-.26-.016-.4-.147-.53-.117-.117-.26-.305-.39-.456-.13-.152-.173-.26-.26-.434-.087-.175-.044-.326-.022-.456.022-.13.173-.413.26-.587.087-.174.13-.297.196-.427.065-.13.032-.24-.011-.37-.043-.13-.413-1.02-.565-1.39-.148-.36-.297-.31-.412-.317-.107-.005-.23-.005-.35-.005-.12 0-.315.045-.48.22-.165.176-.63.616-.63 1.503 0 .888.647 1.748.737 1.878.09.13 1.274 1.945 3.086 2.724.43.185.767.296 1.028.379.432.137.825.118 1.135.072.347-.05 1.097-.449 1.252-.88.156-.431.156-.8.11-.88-.045-.08-.166-.13-.427-.26z"/>
        </svg>
        
    </a>
@endif
