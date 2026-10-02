@extends('layouts.app')

@section('title', 'Pengurusan Pengguna')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-white uppercase tracking-wider mb-2">
                <i class="fa-solid fa-users-gear mr-1.5 text-amber-400"></i> Pentadbiran Keselamatan
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Pengurusan Pengguna & Peranan
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Senarai dan kawalan akaun kakitangan, pegawai UPF, pemandu, dan pentadbir sistem.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.create') }}" class="px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black transition flex items-center shadow-lg shadow-slate-900/15">
                <i class="fa-solid fa-user-plus mr-2"></i> Tambah Pengguna Baharu
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-6">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Carian Nama / Emel / Jabatan
                </label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, emel, atau jabatan..."
                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-xs font-medium bg-slate-50 focus:bg-white transition">
            </div>

            <div class="sm:col-span-4">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">
                    <i class="fa-solid fa-user-shield mr-1"></i> Peranan (Role)
                </label>
                <select name="role" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-xs font-bold bg-slate-50 focus:bg-white transition">
                    <option value="">-- Semua Peranan --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Pentadbir)</option>
                    <option value="upf" {{ request('role') === 'upf' ? 'selected' : '' }}>Pegawai UPF</option>
                    <option value="pemohon" {{ request('role') === 'pemohon' ? 'selected' : '' }}>Pemohon (Staff)</option>
                    <option value="pemandu" {{ request('role') === 'pemandu' ? 'selected' : '' }}>Pemandu</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-filter mr-1.5"></i> Tapis
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('users.index') }}" class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-black border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-5">Nama & Emel</th>
                        <th class="py-3.5 px-4">Peranan</th>
                        <th class="py-3.5 px-4">Jawatan & Bahagian</th>
                        <th class="py-3.5 px-4">No. Telefon</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-600 text-white flex items-center justify-center font-black text-xs shadow-sm">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-900 text-sm">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-black
                                    @if($u->role === 'admin') bg-purple-100 text-purple-800 border border-purple-200
                                    @elseif($u->role === 'upf') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif($u->role === 'pemandu') bg-amber-100 text-amber-800 border border-amber-200
                                    @else bg-slate-100 text-slate-800 border border-slate-200
                                    @endif">
                                    {{ $u->role_label }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $u->position ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $u->department ?? 'Akademi Sains Malaysia' }}</div>
                            </td>

                            <td class="py-3.5 px-4 font-mono text-slate-600">
                                {{ $u->phone ?? '-' }}
                            </td>

                            <td class="py-3.5 px-4">
                                @if($u->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.edit', $u->id) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Kemaskini">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk memadamkan pengguna {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Padam">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-2">
                                    <i class="fa-solid fa-users-slash"></i>
                                </div>
                                <div class="font-bold text-slate-700 text-sm">Tiada pengguna dijumpai</div>
                                <p class="text-xs text-slate-400 mt-1">Tiada akaun yang sepadan dengan carian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
