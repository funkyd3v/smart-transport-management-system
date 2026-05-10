@include('manager::pages.drivers.partials._table')

<div class="mt-4">{{ $drivers->withQueryString()->links() }}</div>
