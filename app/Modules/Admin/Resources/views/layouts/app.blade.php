<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">

 <title>{{ $title ?? 'Admin Dashboard' }} | Smart Transport Management System</title>

 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

 <!-- Scripts -->
 @vite(['resources/css/app.css', 'resources/js/app.js'])

 <!-- Alpine.js -->
 {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

 <!-- Alpine Stores -->
 <script>
 document.addEventListener('alpine:init', () => {
 Alpine.store('sidebar', {
 // Initialize based on screen size
 isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
 isMobileOpen: false,
 isHovered: false,

 toggleExpanded() {
 this.isExpanded = !this.isExpanded;
 // When toggling desktop sidebar, ensure mobile menu is closed
 this.isMobileOpen = false;
 },

 toggleMobileOpen() {
 this.isMobileOpen = !this.isMobileOpen;
 // Don't modify isExpanded when toggling mobile menu
 },

 setMobileOpen(val) {
 this.isMobileOpen = val;
 },

 setHovered(val) {
 // Only allow hover effects on desktop when sidebar is collapsed
 if (window.innerWidth >= 1280 && !this.isExpanded) {
 this.isHovered = val;
 }
 }
 });
 });
 </script>
 </head>

<body class="overflow-x-hidden"
 x-data="{ 'loaded': true}"
 x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
 const checkMobile = () => {
 if (window.innerWidth < 1280) {
 $store.sidebar.setMobileOpen(false);
 $store.sidebar.isExpanded = false;
 } else {
 $store.sidebar.isMobileOpen = false;
 $store.sidebar.isExpanded = true;
 }
 };
 window.addEventListener('resize', checkMobile);">

 {{-- preloader --}}
 <x-common.preloader/>
 {{-- preloader end --}}

 <div class="min-h-screen overflow-x-hidden xl:flex">
 @include('admin::layouts.backdrop')
 @include('admin::layouts.sidebar')

 <div class="min-w-0 flex-1 overflow-x-hidden transition-all duration-300 ease-in-out"
 :class="{
 'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
 'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
 'ml-0': $store.sidebar.isMobileOpen
 }">
 <!-- app header start -->
 @include('admin::layouts.app-header')
 <!-- app header end -->
 <div class="mx-auto min-w-0 max-w-(--breakpoint-2xl) p-4 md:p-6">
 @yield('content')
 </div>
 </div>

 </div>

@stack('scripts')

<script>
	window.addEventListener('load', function() {
		if (typeof window.Toastify !== 'function') {
			return;
		}

		const toastQueue = [];

		if (@json(session()->has('toast_success'))) {
			toastQueue.push({ text: @json(session('toast_success')), background: '#22c55e', duration: 3000 });
		}

		if (@json(session()->has('toast_error'))) {
			toastQueue.push({ text: @json(session('toast_error')), background: '#ef4444', duration: 4000 });
		}

		if (@json(session()->has('toast_warning'))) {
			toastQueue.push({ text: @json(session('toast_warning')), background: '#f59e0b', duration: 4000 });
		}

		if (@json(session()->has('toast_info'))) {
			toastQueue.push({ text: @json(session('toast_info')), background: '#3b82f6', duration: 3000 });
		}

		if (@json($errors->any())) {
			toastQueue.push({ text: @json($errors->first()), background: '#ef4444', duration: 4500 });
		}

		toastQueue.forEach((toast, index) => {
			setTimeout(() => {
				Toastify({
					text: toast.text,
					duration: toast.duration,
					gravity: 'top',
					position: 'right',
					stopOnFocus: true,
					style: { background: toast.background },
				}).showToast();
			}, index * 160);
		});
	});
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</body>

</html>
