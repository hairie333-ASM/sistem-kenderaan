@extends('layouts.app')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky-500 text-white uppercase tracking-wider mb-2">
                <i class="fa-regular fa-bell mr-1.5"></i> Pemberitahuan Sistem
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Pusat Notifikasi
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Makluman status permohonan, penugasan pemandu, dan peringatan operasi kenderaan anda.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black transition flex items-center shadow-md">
                        <i class="fa-solid fa-check-double mr-1.5 text-emerald-400"></i> Tandakan Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Notification List -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm divide-y divide-slate-100 overflow-hidden">
        @forelse($notifications as $notif)
            <div class="p-5 sm:p-6 transition flex items-start gap-4 {{ $notif->is_read ? 'bg-white opacity-80' : 'bg-sky-50/40 font-medium' }} hover:bg-slate-50">
                <!-- Icon badge -->
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm flex-shrink-0 mt-0.5
                    @if($notif->type === 'success') bg-emerald-100 text-emerald-700
                    @elseif($notif->type === 'warning') bg-amber-100 text-amber-700
                    @elseif($notif->type === 'danger') bg-rose-100 text-rose-700
                    @else bg-sky-100 text-sky-700
                    @endif">
                    @if($notif->type === 'success') <i class="fa-solid fa-check"></i>
                    @elseif($notif->type === 'warning') <i class="fa-solid fa-triangle-exclamation"></i>
                    @elseif($notif->type === 'danger') <i class="fa-solid fa-circle-xmark"></i>
                    @else <i class="fa-solid fa-info"></i>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-black text-slate-900 {{ !$notif->is_read ? 'text-sky-950' : '' }}">
                            {{ $notif->title }}
                            @if(!$notif->is_read)
                                <span class="inline-block w-2 h-2 rounded-full bg-sky-600 ml-1"></span>
                            @endif
                        </h3>
                        <span class="text-[11px] text-slate-400 font-mono whitespace-nowrap">
                            {{ $notif->created_at ? $notif->created_at->diffForHumans() : '-' }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        {{ $notif->message }}
                    </p>

                    @if($notif->link)
                        <div class="mt-3">
                            <a href="{{ route('notifications.read', $notif->id) }}" class="inline-flex items-center text-xs font-bold text-sky-600 hover:text-sky-800">
                                <span>Lihat Rujukan</span>
                                <i class="fa-solid fa-arrow-right ml-1.5 text-[10px]"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick mark as read button if unread -->
                @if(!$notif->is_read && !$notif->link)
                    <a href="{{ route('notifications.read', $notif->id) }}" title="Tandakan dibaca" class="text-slate-400 hover:text-slate-700 text-xs p-1">
                        <i class="fa-regular fa-circle-check"></i>
                    </a>
                @endif
            </div>
        @empty
            <div class="p-12 text-center text-slate-400">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-regular fa-bell-slash"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700">Tiada notifikasi</h3>
                <p class="text-xs text-slate-400 mt-1">Anda tidak mempunyai sebarang pemberitahuan baharu pada masa ini.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
