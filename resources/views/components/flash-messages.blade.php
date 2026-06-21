@if(count($messages))
  <div {{ $attributes->merge(['class' => 'flex flex-col gap-3']) }}>
    @foreach($messages as $type => $message)
      @php
        $styles = match($type) {
          'success' => 'border-green-500/30 bg-green-500/10 text-green-400',
          'warning' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-400',
          'error'   => 'border-red-500/30 bg-red-500/10 text-red-400',
          'info'    => 'border-blue-500/30 bg-blue-500/10 text-blue-400',
          default   => 'border-gray-500/30 bg-gray-500/10 text-gray-400',
        };
      @endphp

      <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        class="flex items-start justify-between gap-3 px-4 py-3 rounded-lg border text-sm {{ $styles }}"
      >
        <div class="flex items-start gap-2">
          <span>{{ $message }}</span>
        </div>

        {{-- Dismiss button --}}
        <button
          type="button"
          @click="show = false"
          class="opacity-50 hover:opacity-100 transition-opacity shrink-0 mt-0.5"
        >✕</button>
      </div>
    @endforeach
  </div>
@endif