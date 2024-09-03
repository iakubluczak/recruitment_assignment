<div class="relative">
    <div class="relative"> <!-- Dodajemy relative tutaj, aby loader pozycjonował się względem tabeli -->
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/3 py-2 px-4">Name</th>
                    <th class="w-1/3 py-2 px-4">Email</th>
                    <th class="w-1/3 py-2 px-4">Birthdate</th>
                    <th class="w-1/3 py-2 px-4">Last purchase date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="text-center border-b">
                        <td class="py-2 px-4">{{ $user->name }}</td>
                        <td class="py-2 px-4">{{ $user->email }}</td>
                        <td class="py-2 px-4">{{ $user->birthdate }}</td>
                        <td class="py-2 px-4">{{ \Carbon\Carbon::parse($user->last_purchase_date)->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                            Results not found
                        </td>
                    </tr>
                @endempty
            </tbody>
        </table>

        <!-- Nakładka, która pojawi się podczas ładowania -->
        <div wire:loading.delay class="absolute top-0 left-0 w-full h-full flex items-center justify-center bg-gray-100 bg-opacity-75 z-10">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-600 h-20 w-20"></div>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <style>
        .relative {
            position: relative;
            overflow: hidden;
        }

        .loader {
            border-color: #f3f3f3;
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
            position: absolute; /* Add this line */
            top: 50%; /* Add this line */
            left: 50%; /* Add this line */
            transform: translate(-50%, -50%); /* Add this line */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</div>