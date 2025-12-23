<x-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Management') }}
            </h2>
            <a href="{{ route('users.create') }}" class="px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Add New User
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-sm:py-1">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-10">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3">User ID</th>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Role</th>
                                    <th class="px-6 py-3">Department</th>
                                    <th class="px-6 py-3">Level Grade</th>
                                    <th class="px-6 py-3">Manager ID</th>
                                    <th class="px-6 py-3">Location</th>
                                    <th class="px-6 py-3">Join Date</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr
                                        class="border-b dark:border-gray-700 {{ !$user->is_active ? 'bg-white-50 text-gray-400 italic' : '' }}">
                                        <td class="px-6 py-4 font-medium">{{ $user->user_id }}</td>
                                        <td class="px-6 py-4">{{ $user->name }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full {{ $user->role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $user->department ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $user->level_grade ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $user->manager_id ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $user->lokasi_kerja ?? '-' }}</td>
                                        <td class="px-6 py-4">
                                            {{ $user->tanggal_masuk ? $user->tanggal_masuk->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($user->is_active)
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                <!-- Edit -->
                                                <a href="{{ route('users.edit', $user) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                    Edit
                                                </a>

                                                <!-- Activate/Deactivate -->
                                                @if ($user->is_active)
                                                    <form action="{{ route('users.deactivate', $user) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-orange-600 hover:text-orange-900 text-sm"
                                                            onclick="return confirm('Deactivate this user?')">
                                                            Deactivate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('users.activate', $user) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-green-600 hover:text-green-900 text-sm"
                                                            onclick="return confirm('Activate this user?')">
                                                            Activate
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Delete (only if no timesheets and not self) -->
                                                @if ($user->timesheets()->count() === 0 && auth()->id() !== $user->user_id)
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900 text-sm"
                                                            onclick="return confirm('PERMANENTLY DELETE this user? This cannot be undone!')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No users found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
