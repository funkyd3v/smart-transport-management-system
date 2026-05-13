@extends('admin::layouts.app')

@section('content')
 <div class="grid grid-cols-12 gap-4 md:gap-6">
 <div class="col-span-12 space-y-6 xl:col-span-7">
 <x-admin::ecommerce.ecommerce-metrics />
 <x-admin::ecommerce.monthly-sale />
 </div>
 <div class="col-span-12 xl:col-span-5">
 <x-admin::ecommerce.monthly-target />
 </div>

 <div class="col-span-12">
 <x-admin::ecommerce.statistics-chart />
 </div>

 <div class="col-span-12 xl:col-span-5">
 <x-admin::ecommerce.customer-demographic />
 </div>

 <div class="col-span-12 xl:col-span-7">
 <x-admin::ecommerce.recent-orders />
 </div>
 </div>
@endsection
