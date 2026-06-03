@extends('site.layouts.app')

@section('title', __('site.meta.title'))
@section('description', __('site.meta.description'))

@section('content')
    @include('site.sections.hero')
    @include('site.sections.about')
    @include('site.sections.features')
    @include('site.sections.forwho')
    @include('site.sections.pricing')
    @include('site.sections.contact')
@endsection
