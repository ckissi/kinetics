@extends('layouts.app')

@section('title', 'Kinetics Pro — every prompt, ready to paste')

@section('content')
  <section class="section" id="pro">
    <div class="wrap">
      <div class="section-head">
        <h2>Kinetics Pro</h2>
        <span class="count mono">{{ $effectCount }} prompts</span>
      </div>

      <div class="pro-pitch">
        <p class="pro-pitch-lead">
          Every effect in the library ships with an AI prompt that rebuilds it —
          written against the same physics as the CSS, so what you get back
          behaves like the demo rather than approximating it.
        </p>

        <ul class="pro-pitch-list">
          <li>{{ $effectCount }} prompts, one for every effect in the library.</li>
          <li>Written for pasting straight into Cursor, Claude or any coding agent.</li>
          <li>New prompts land with every effect added to the gallery.</li>
        </ul>

        <p class="pro-pitch-note mono">
          Checkout is not open yet. The CSS and React for all
          {{ $effectCount }} effects stay free, always.
        </p>

        <a href="/#library" class="btn-secondary">Back to the library</a>
      </div>
    </div>
  </section>
@endsection
