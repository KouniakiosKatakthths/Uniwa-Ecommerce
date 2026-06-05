@extends("layouts.cinema")
@section("content")
<div class="sm:px-6 max-w-7xl mx-auto lg:px-8 py-10 flex flex-col gap-10">

  {{-- Header --}}
  <div class="flex flex-col gap-2">
    <h1 class="text-4xl font-bold text-gray-100">Find Us</h1>
    <p class="text-gray-400 max-w-xl">We'd love to hear from you. Visit us, give us a call, or reach out on social media.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    {{-- Left — Contact info --}}
    <div class="flex flex-col gap-6">

      {{-- Address --}}
      <x-card variant="normal" class="mt-0">
        <div class="flex flex-col gap-5">

          <div class="flex items-start gap-4">
            <div class="p-2 rounded-lg bg-accent/10 shrink-0">
              <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Address</p>
              <p class="text-gray-200">Agiou Spiridonos Str</p>
              <p class="text-gray-400 text-sm">Athens, 12243, Greece</p>
            </div>
          </div>

          <div class="border-t border-white/10"></div>

          {{-- Phone --}}
          <div class="flex items-start gap-4">
            <div class="p-2 rounded-lg bg-accent/10 shrink-0">
              <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Phone</p>
              <a href="tel:+302101234567" class="text-gray-200 hover:text-accent transition">+30 210 123 4567</a>
            </div>
          </div>

          <div class="border-t border-white/10"></div>

          {{-- Email --}}
          <div class="flex items-start gap-4">
            <div class="p-2 rounded-lg bg-accent/10 shrink-0">
              <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Email</p>
              <a href="mailto:info@ecommerce.com" class="text-gray-200 hover:text-accent transition">info@ecommerce.com</a>
            </div>
          </div>

          <div class="border-t border-white/10"></div>

          {{-- Opening hours --}}
          <div class="flex items-start gap-4">
            <div class="p-2 rounded-lg bg-accent/10 shrink-0">
              <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="w-full">
              <p class="text-xs uppercase tracking-widest text-gray-500 mb-2">Opening Hours</p>
              <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                <span class="text-gray-400">Monday – Friday</span>
                <span class="text-gray-200">14:00 – 24:00</span>
                <span class="text-gray-400">Saturday – Sunday</span>
                <span class="text-gray-200">11:00 – 24:00</span>
              </div>
            </div>
          </div>

        </div>
      </x-card>

      {{-- Social media --}}
      <x-card variant="normal" class="mt-0">
        <p class="text-xs uppercase tracking-widest text-gray-500 mb-4">Follow Us</p>
        <div class="flex gap-3">

          {{-- Facebook --}}
          <a href="" target="_blank"
             class="flex items-center gap-2 px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-accent hover:border-accent/40 transition text-sm">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
            </svg>
            Facebook
          </a>

          {{-- Instagram --}}
          <a href="" target="_blank"
             class="flex items-center gap-2 px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-accent hover:border-accent/40 transition text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
              <circle cx="12" cy="12" r="4"/>
              <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
            </svg>
            Instagram
          </a>

          {{-- X / Twitter --}}
          <a href="" target="_blank"
             class="flex items-center gap-2 px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-accent hover:border-accent/40 transition text-sm">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            X
          </a>

        </div>
      </x-card>

    </div>

    {{-- Right — Google Maps --}}
    <div class="rounded-xl overflow-hidden border border-white/10 h-full min-h-96">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1714.2353497669205!2d23.675952085851925!3d38.002293119113666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14a13ba48d426f81%3A0xbe82f5519cbcb53d!2zzqDOsc69zrXPgM65z4PPhM6uzrzOuc6_IM6Uz4XPhM65zrrOrs-CIM6Rz4TPhM65zrrOrs-C!5e0!3m2!1sel!2sgr!4v1780620260287!5m2!1sel!2sgr"
        width="100%"
        height="100%"
        style="border:0; filter: invert(90%) hue-rotate(180deg);"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>

  </div>
</div>
@endsection