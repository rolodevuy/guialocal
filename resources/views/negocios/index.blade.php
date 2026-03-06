@extends('layouts.app')

@section('title', 'Negocios — Guía Local')
@section('description', 'Explorá todos los negocios y servicios del barrio.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @livewire('negocios-index')
</div>
@endsection
