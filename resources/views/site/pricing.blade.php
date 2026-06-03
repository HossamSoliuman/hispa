@extends('site.layouts.app')

@section('title', __('site.nav.pricing') . ' - ' . __('site.meta.title'))
@section('description', __('site.pricing.subtitle'))

@section('content')
    @include('site.sections.pricing')
@endsection
