@extends('site.layouts.app')

@section('title', __('site.meta.title'))
@section('description', __('site.meta.description'))

@section('content')
    @include('site.sections.hero')
    @include('site.sections.features')
    @include('site.sections.pricing')
@endsection
