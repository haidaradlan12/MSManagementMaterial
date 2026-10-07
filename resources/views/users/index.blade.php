<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3">Nama</th>
                                    <th class="px-5 py-3">Email</th>
                                    <th class="px-5 py-3">Tgl Daftar</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Peran</th>
                                    <th class="px-5 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-5 py-3">{{ $user->email }}</td>
                                        <td class="px-5 py-3">{{ $user->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-5 py-3">
                                            @if($user->is_approved)
                                                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Aktif</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Menunggu</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            @if($user->is_admin)
                                                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Admin</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">User</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex flex-wrap items-center justify-center gap-2">
                                                @if(auth()->id() !== $user->id)
                                                    @if(!$user->is_approved)
                                                        <form action="{{ route('users.approve', $user) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                                                Setujui
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('users.reject', $user) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition-colors" onclick="return confirm('Cabut akses user ini?')">
                                                                Cabut Akses
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if(!$user->is_admin)
                                                        <form action="{{ route('users.make-admin', $user) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded-lg transition-colors" onclick="return confirm('Jadikan akun ini Admin?')">
                                                                Jadikan Admin
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('users.remove-admin', $user) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded-lg transition-colors" onclick="return confirm('Cabut status Admin dari akun ini?')">
                                                                Cabut Admin
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-colors" onclick="return confirm('Hapus akun ini permanen?')">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-gray-400">Tidak ada pengguna.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
