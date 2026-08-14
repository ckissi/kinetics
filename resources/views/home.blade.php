@extends('layouts.app')

@section('content')
  @include('partials.hero')

  <livewire:effect-library />

  @include('partials.closing')
@endsection
