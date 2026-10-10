@php
    $fmt = fn($v, $c) => $c === 'USD' ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' IQD';
    $debt = $o->customer ? $o->customer->currencySummary() : null;
    $stClass = ['pending' => 'bg-amber-500/20 text-amber-300', 'accepted' => 'bg-emerald-500/20 text-emerald-300', 'rejected' => 'bg-rose-500/20 text-rose-300'][$o->status] ?? '';
    $stLabel = ['pending' => 'چاوەڕێ', 'accepted' => 'قبوڵکراو', 'rejected' => 'ڕەتکراو'][$o->status] ?? $o->status;
@endphp
<div class="order-card relative bg-slate-800 border border-slate-700 rounded-2xl p-4 space-y-3" data-id="{{ $o->id }}">
    <span class="new-tag hidden absolute -top-2 left-4 bg-gradient-to-r from-amber-400 to-rose-500 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-lg"><i class="fa-solid fa-bell"></i> نوێ</span>

    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="font-mono font-black text-sm text-white">{{ $o->order_no }}</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black {{ $stClass }}">{{ $stLabel }}</span>
        </div>
        <span class="text-[11px] text-slate-400 font-mono" dir="ltr">{{ $o->created_at->format('n/j/y, g:i A') }}</span>
    </div>

    {{-- کڕیار --}}
    <div class="bg-slate-900/60 rounded-xl p-3 space-y-1.5">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-extrabold text-white">{{ $o->name }}</span>
            @if($o->customer)
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-cyan-500/20 text-cyan-300"><i class="fa-solid fa-user-check"></i> کڕیاری تۆمارکراو</span>
            @else
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-slate-600/50 text-slate-300">کڕیاری ئاسایی</span>
            @endif
            @if($o->claims_regular && !$o->customer)
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-rose-500/20 text-rose-300">وتی هەمیشەییم، بەڵام ژمارەکە نەدۆزرایەوە</span>
            @endif
        </div>
        <div class="text-xs text-cyan-300 font-mono" dir="ltr" style="text-align:right;"><i class="fa-solid fa-phone"></i> {{ $o->phone }}</div>
        @if($o->address)<div class="text-xs text-slate-400"><i class="fa-solid fa-location-dot"></i> {{ $o->address }}</div>@endif
        @if($o->latitude && $o->longitude)
            <a href="https://www.google.com/maps?q={{ $o->latitude }},{{ $o->longitude }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600 hover:text-white px-2.5 py-1 rounded-lg text-[11px] font-extrabold transition">
                <i class="fa-solid fa-map-location-dot"></i> کردنەوەی شوێن لەسەر نەخشە
            </a>
        @endif
        @if($debt)
            <div class="text-[11px] text-slate-400 flex flex-wrap gap-x-4 gap-y-0.5 pt-1 border-t border-slate-700">
                <span>قەرزی ئێستا:</span>
                @foreach(['USD', 'IQD'] as $cur)
                    <span class="font-mono font-bold {{ $debt[$cur]['debt'] > 0 ? 'text-amber-300' : 'text-emerald-400' }}" dir="ltr">{{ $fmt($debt[$cur]['debt'], $cur) }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- کاڵاکان --}}
    <div class="space-y-1">
        @foreach($o->items as $it)
            <div class="flex items-center justify-between text-xs bg-slate-900/40 rounded-lg px-3 py-2">
                <span class="font-bold text-slate-100">{{ $it->product->name ?? '— (کاڵا سڕاوەتەوە)' }}</span>
                <span class="font-mono font-black text-cyan-300">{{ rtrim(rtrim(number_format((float) $it->quantity, 3), '0'), '.') }} <span class="font-sans font-bold text-slate-400">{{ $it->unit->name ?? '' }}</span></span>
            </div>
        @endforeach
    </div>

    @if($o->note)
        <div class="text-xs bg-amber-500/10 border border-amber-500/30 text-amber-200 rounded-lg px-3 py-2"><i class="fa-solid fa-note-sticky"></i> {{ $o->note }}</div>
    @endif

    {{-- کردارەکان --}}
    <div class="flex flex-wrap items-center gap-2 pt-1">
        @if($o->status === 'pending')
            <a href="{{ route('pos.order', $o->id) }}" class="flex-1 text-center bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs py-2.5 rounded-xl transition">
                <i class="fa-solid fa-check"></i> قبوڵکردن و کردنی بە وەسڵ
            </a>
            <form action="{{ route('orders.reject', $o->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە ڕەتکردنەوەی ئەم داواکارییە؟')">
                @csrf
                <button type="submit" class="bg-rose-600/20 text-rose-300 border border-rose-500/40 hover:bg-rose-600 hover:text-white px-3 py-2.5 rounded-xl text-xs font-bold transition"><i class="fa-solid fa-xmark"></i> ڕەتکردنەوە</button>
            </form>
        @elseif($o->status === 'rejected')
            <a href="{{ route('pos.order', $o->id) }}" class="flex-1 text-center bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs py-2.5 rounded-xl transition">
                <i class="fa-solid fa-rotate-left"></i> قبوڵکردنەوە و کردنی بە وەسڵ
            </a>
        @elseif($o->status === 'accepted' && $o->sale_id)
            <a href="{{ route('sales.print', $o->sale_id) }}?type=small" target="_blank" class="bg-blue-600/20 text-blue-300 border border-blue-500/40 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-xl text-xs font-bold transition"><i class="fa-solid fa-receipt"></i> وەسڵەکە</a>
        @endif
        <form action="{{ route('orders.destroy', $o->id) }}" method="POST" onsubmit="return confirm('سڕینەوەی ئەم داواکارییە؟')" class="mr-auto">
            @csrf @method('DELETE')
            <button type="submit" title="سڕینەوە" class="text-slate-500 hover:text-rose-400 px-2 py-2 text-xs"><i class="fa-solid fa-trash"></i></button>
        </form>
    </div>
</div>