@php
    $preferenceMap = $notificationPreferences->keyBy(fn($item) => $item->event.'_'.$item->channel);
    $events = [
        'trip_created' => 'Trip created',
        'trip_completed' => 'Trip completed',
        'invoice_generated' => 'New invoice generated',
        'payment_received' => 'Payment received',
        'due_raised' => 'Due payment raised',
        'due_collected' => 'Due payment collected',
        'driver_status_changed' => 'Driver status changed',
        'low_spare_inventory' => 'Low spare inventory',
        'client_added' => 'New client added',
        'daily_summary' => 'Daily summary report',
    ];
    $channels = ['in_app' => 'In-App', 'email' => 'Email', 'sms' => 'SMS'];
@endphp

<section id="tab-panel-notifications" class="tab-panel hidden">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="sticky top-0 border-b border-gray-200 bg-white text-left text-xs uppercase text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-3 py-3">Event</th>
                        <th class="px-3 py-3 text-center">In-App</th>
                        <th class="px-3 py-3 text-center">Email</th>
                        <th class="px-3 py-3 text-center">SMS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $eventKey => $eventLabel)
                        <tr class="border-b border-gray-100 dark:border-gray-700/60">
                            <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ $eventLabel }}</td>
                            @foreach($channels as $channelKey => $channelLabel)
                                @php
                                    $pref = $preferenceMap->get($eventKey.'_'.$channelKey);
                                    $checked = $pref ? (bool) $pref->enabled : true;
                                @endphp
                                <td class="px-3 py-3 text-center">
                                    <label class="inline-flex cursor-pointer items-center gap-2">
                                        <span class="relative inline-flex h-6 w-11 items-center rounded-full {{ $checked ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-700' }}" data-toggle-track>
                                            <input
                                                type="checkbox"
                                                class="peer sr-only"
                                                data-notification-toggle
                                                data-event="{{ $eventKey }}"
                                                data-channel="{{ $channelKey }}"
                                                {{ $checked ? 'checked' : '' }}
                                            >
                                            <span class="absolute left-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                        </span>
                                        <span id="spinner-{{ $eventKey }}-{{ $channelKey }}" class="hidden h-3.5 w-3.5 animate-spin rounded-full border border-gray-400 border-t-transparent"></span>
                                    </label>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
