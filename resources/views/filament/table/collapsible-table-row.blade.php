
<div class="bg-white dark:bg-gray-950 rounded-xl shadow-sm">
    <table class="min-w-full border-separate border-spacing-y-2">
        <thead>
            <tr class="text-xs text-gray-500 uppercase tracking-wider">
                <th class="px-3 py-2 font-medium text-left">Avatar</th>
                <th class="px-3 py-2 font-medium text-left">Name</th>
                <th class="px-3 py-2 font-medium text-left">Precinct No.</th>
                <th class="px-3 py-2 font-medium text-left">Cluster No.</th>
                <th class="px-3 py-2 font-medium text-left">Services</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($getRecord()->listmembers as $member)
                <tr class="bg-gray-50 dark:bg-red-800 dark:text-red-800 hover:bg-gray-100 transition rounded-lg">
                    <td class="px-3 py-2">
                        @if($member->avatar)
                            <img src="{{ asset('storage/' . $member->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 font-semibold text-sm">
                                {{ strtoupper(substr($member->first_name,0,1) . substr($member->surname,0,1)) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-2  font-medium">{{ $member->full_name }}</td>
                    <td class="px-3 py-2 ">{{ $member->precinct_no }}</td>
                    <td class="px-3 py-2 ">{{ $member->cluster_no }}</td>
                    <td class="text-amber-500 text-center">
                        <span class="inline-block bg-amber-500/10 border border-stone-950 rounded w-6 h-6 text-xs pt-1">{{ count($member->memberServices) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

