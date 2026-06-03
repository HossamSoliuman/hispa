@extends('site.layouts.app')

@section('title', __('site.nav.contact') . ' - ' . __('site.meta.title'))
@section('description', __('site.contact.description'))

@section('content')
    @include('site.sections.contact')
@endsection
