<div class="flex gap-2 mb-4">
    @php
        $tabs = [
            'all' => 'All',
            'takamul_no' => 'Takamul No',
            'tasheer_no' => 'Tasheer No',
            'ttc_no' => 'TTC No',
            'embassy_no' => 'Embassy No',
            'bmet_no' => 'BMET No',
        ];
        $current = request()->get('tab', 'all');
    @endphp

    @foreach ($tabs as $key => $label)
        <a href="{{ route('filament.resources.visas.index', ['tab' => $key]) }}"
           class="px-4 py-2 rounded-md font-medium text-sm
           {{ $current === $key ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-800' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
