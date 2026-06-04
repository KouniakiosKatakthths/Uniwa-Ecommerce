@extends("layouts.cinema")
@section("content")
<div class="max-w-xl mx-auto px-4 py-10 flex flex-col gap-6">
  <h1 class="text-2xl font-bold text-gray-100">Ticket Validation</h1>

  {{-- Manual / barcode scanner input --}}
  <form method="POST" action="{{ route('tickets.validate.submit') }}" class="flex flex-col gap-3">
    @csrf
    <label class="text-sm text-gray-400 uppercase tracking-widest">Scan or enter code</label>
    <div class="flex gap-2">
      <input
        type="text"
        name="code"
        id="code-input"
        autofocus
        value="{{ old('code') }}"
        placeholder="Scan barcode or enter QR code..."
        class="flex-1 px-4 py-3 rounded-lg bg-white/10 border border-white/10 text-gray-200 placeholder-gray-600 focus:outline-none focus:border-accent font-mono">
      <x-button type="submit">Validate</x-button>
    </div>
    @error('code')
      <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
  </form>

  {{-- QR Camera scanner --}}
  <div class="flex flex-col gap-3" x-data="qrScanner()">
    <div class="flex items-center justify-between">
      <label class="text-sm text-gray-400 uppercase tracking-widest">Camera QR scan</label>
      <button type="button" @click="toggle"
        class="text-xs text-accent underline"
        x-text="active ? 'Stop camera' : 'Start camera'">
      </button>
    </div>

    <div x-show="active" class="relative rounded-xl overflow-hidden border border-white/10">
      <video id="qr-video" class="w-full rounded-xl" playsinline></video>
      {{-- Targeting overlay --}}
      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="w-48 h-48 border-2 border-accent rounded-lg opacity-60"></div>
      </div>
    </div>

    <p x-show="active" class="text-xs text-gray-500 text-center">
      Point the camera at a QR code — it will submit automatically
    </p>

    {{-- Hidden form that submits on scan --}}
    <form id="qr-form" method="POST" action="{{ route('tickets.validate.submit') }}">
      @csrf
      <input type="hidden" name="code" id="qr-result">
    </form>
  </div>

  {{-- Result --}}
  @if(session('success') && session('ticket'))
    @php $ticket = session('ticket'); @endphp
    <div class="rounded-xl border border-green-500/30 bg-green-500/10 p-5 flex flex-col gap-3">
      <div class="flex items-center gap-2 text-green-400 font-semibold">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
      </div>
      <div class="grid grid-cols-2 gap-2 text-sm">
        <span class="text-gray-500">Movie</span>
        <span class="text-gray-200">{{ $ticket->showtime->movie->title }}</span>
        <span class="text-gray-500">Showtime</span>
        <span class="text-gray-200">{{ $ticket->showtime->starts_at->format('d M Y, H:i') }}</span>
        <span class="text-gray-500">Room</span>
        <span class="text-gray-200">{{ $ticket->showtime->room }}</span>
        <span class="text-gray-500">Seat</span>
        <span class="text-gray-200">{{ $ticket->seat }}</span>
        <span class="text-gray-500">Guest</span>
        <span class="text-gray-200">{{ $ticket->user->name }}</span>
      </div>
    </div>
  @endif

  @if(session('error'))
    <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 flex items-center gap-2 text-red-400">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      {{ session('error') }}
    </div>
  @endif

</div>

{{-- QR scanner JS (no install needed — CDN) --}}
<script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
function qrScanner() {
    return {
        active: false,
        stream: null,
        animFrame: null,

        async toggle() {
            this.active ? this.stop() : await this.start();
        },

        async start() {
            this.active = true;
            await this.$nextTick();
            const video = document.getElementById('qr-video');
            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = this.stream;
            video.play();
            video.addEventListener('loadeddata', () => this.scan(video));
        },

        stop() {
            this.active = false;
            this.stream?.getTracks().forEach(t => t.stop());
            cancelAnimationFrame(this.animFrame);
        },

        scan(video) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            const tick = () => {
                if (!this.active) return;
                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    canvas.width  = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    if (code) {
                        document.getElementById('qr-result').value = code.data;
                        document.getElementById('qr-form').submit();
                        this.stop();
                        return;
                    }
                }
                this.animFrame = requestAnimationFrame(tick);
            };
            tick();
        }
    }
}
</script>
@endsection