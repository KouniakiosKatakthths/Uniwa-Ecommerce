@extends("layouts.cinema")

@php
  //QR code generator system
  $renderer = new \BaconQrCode\Renderer\ImageRenderer(
    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180),
    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
  );
  $writer = new \BaconQrCode\Writer($renderer);
  $qrSvg = $writer->writeString($ticket->qr_code);

  //Barcode generator system
  $generator = new Picqer\Barcode\BarcodeGeneratorSVG();
  $barcode = $generator->getBarcode($ticket->barcode, $generator::TYPE_CODE_128);
@endphp


@section("content")
<div class="max-w-lg mx-auto px-4 py-10">
  <x-flash-messages class="mb-6"></x-flash-messages>

  <div class="rounded-2xl overflow-hidden border border-white/10 bg-gray-800/60">
    {{-- Top section --}}
    <div class="flex gap-5 p-6 border-b border-white/10">
      <img
        src="{{ $ticket->showtime->movie->getMoviePoster() }}"
        alt="{{ $ticket->showtime->movie->title }}"
        class="w-24 rounded-lg object-cover aspect-2/3 shrink-0">
      <div class="flex flex-col gap-1 justify-center">
        <h1 class="text-xl font-bold text-gray-100">{{ $ticket->showtime->movie->title }}</h1>
        <p class="text-gray-400 text-sm">{{ $ticket->showtime->starts_at->format('l, d M Y') }}</p>
        <p class="text-gray-400 text-sm">{{ $ticket->showtime->starts_at->format('H:i') }}</p>
        <p class="text-gray-400 text-sm">Room: <span class="text-gray-200">{{ $ticket->showtime->room }}</span></p>
      </div>
    </div>

    {{-- Ticket details --}}
    <div class="grid grid-cols-3 divide-x divide-white/10 border-b border-white/10">
      <div class="flex flex-col items-center py-5 gap-1">
        <span class="text-xs text-gray-500 uppercase tracking-widest">Seat</span>
        <span class="text-2xl font-bold text-gray-100">{{ $ticket->seat }}</span>
      </div>
      <div class="flex flex-col items-center py-5 gap-1">
        <span class="text-xs text-gray-500 uppercase tracking-widest">Price</span>
        <span class="text-2xl font-bold text-accent">€{{ number_format($ticket->price, 2) }}</span>
      </div>
      <div class="flex flex-col items-center py-5 gap-1">
        <span class="text-xs text-gray-500 uppercase tracking-widest">Status</span>
        <span class="text-sm font-semibold px-3 py-1 rounded-full
          {{ $ticket->status === \App\Enums\TicketStatus::Confirmed ? 'bg-green-500/20 text-green-400' : '' }}
          {{ $ticket->status === \App\Enums\TicketStatus::Pending   ? 'bg-yellow-500/20 text-yellow-400' : '' }}
          {{ $ticket->status === \App\Enums\TicketStatus::Cancelled ? 'bg-red-500/20 text-red-400' : '' }}">
          {{ $ticket->status->name }}
        </span>
      </div>
    </div>

    {{-- QR Code --}}
    <div class="flex flex-col items-center gap-3 py-8">
      <span class="text-xs text-gray-500 uppercase tracking-widest">Scan at entrance</span>
      <div class="bg-white p-3 rounded-lg">
          {!! $qrSvg !!}
      </div>
      <span class="text-xs text-gray-600 font-mono mt-1">{{ $ticket->qr_code }}</span>
    </div>

    {{-- Dashed divider --}}
    <div class="border-t border-dashed border-white/10 mx-6"></div>

    {{-- Barcode --}}
    <div class="flex flex-col items-center gap-2 py-6">
      <span class="text-xs text-gray-500 uppercase tracking-widest">Barcode</span>
      <div class="bg-white px-4 py-2 rounded-lg">
          {!! $barcode !!}
      </div>
      <span class="font-mono text-sm tracking-widest text-gray-500">{{ $ticket->barcode }}</span>
    </div>

  </div>
</div>
@endsection