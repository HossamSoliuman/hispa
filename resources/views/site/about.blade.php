@extends('site.layouts.app')

@section('title', __('site.nav.about') . ' - ' . __('site.meta.title'))
@section('description', __('site.about.block1_p1'))

@section('content')
    @include('site.sections.about')
    @include('site.sections.features')
    @include('site.sections.forwho')
@endsection
