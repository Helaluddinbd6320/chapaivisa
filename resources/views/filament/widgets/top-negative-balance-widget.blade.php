<div class="p-4 bg-white rounded-lg shadow">
    <h2 class="text-lg font-bold text-gray-700 mb-4">Top 10 Negative Balance Users</h2>

    @php
        // Users কে নেগেটিভ ব্যালেন্স অনুসারে সাজান (সবচেয়ে বেশি নেগেটিভ প্রথমে)
        $sortedUsers = $users
            ->where('current_balance', '<', 0)
            ->sortBy('current_balance') // সবচেয়ে ছোট মান (সবচেয়ে বেশি নেগেটিভ) প্রথমে
            ->take(10);
    @endphp

    @if ($sortedUsers->isEmpty())
        <p class="text-gray-500">No negative balance found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 divide-y divide-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 uppercase">
                            Name
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 uppercase">
                            Balance
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($sortedUsers as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-800">
                                {{ $user->name }}
                            </td>
                            <td class="px-4 py-2 font-semibold text-red-600">
                                {{ number_format($user->current_balance) }} ৳
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
