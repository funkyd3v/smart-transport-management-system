@include('manager::pages.clients.partials._table')

<div class="mt-4">{{ $clients->withQueryString()->links() }}</div>
