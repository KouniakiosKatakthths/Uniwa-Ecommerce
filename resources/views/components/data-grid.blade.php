@props(['pagination' => null])

<div class="flex flex-col gap-6">
  <div class="rounded-xl border border-white/10 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-white/5 text-gray-500 uppercase tracking-widest text-xs">
        <tr>
          {{ $head }}
        </tr>
      </thead>
      <tbody class="divide-y divide-white/5">
        {{ $slot }}
      </tbody>
    </table>
  </div>

  @if($pagination)
    <div>{!! $pagination !!}</div>
  @endif
</div>