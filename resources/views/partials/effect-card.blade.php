@php
    // CSS, React, Prompt — in the order the tabs have always appeared.
    // React is absent for the 63 CSS-only effects, so its tab is skipped
    // entirely rather than rendered empty.
    $prompt = $effect->prompts->firstWhere('type', 'general');

    // Prompts are Pro content. Visitors without access get the opening of the
    // prompt, faded out, rather than a bare notice — seeing what the writing
    // is actually like is the argument for buying it. Only that excerpt is
    // rendered; the rest never reaches the page.
    $promptLocked = $prompt && ! Gate::allows('view-pro-content');

    $panes = collect([
        ['lang' => 'css', 'label' => 'CSS', 'content' => $effect->snippets->firstWhere('type', 'css')?->content],
        ['lang' => 'react', 'label' => 'React', 'content' => $effect->snippets->firstWhere('type', 'react')?->content],
        [
            'lang' => 'prompt',
            'label' => 'Prompt',
            'content' => $promptLocked ? $prompt->teaser() : $prompt?->content,
            'locked' => $promptLocked,
        ],
    ])->filter(fn($pane) => filled($pane['content']))->values();
@endphp

<div class="card">
  <div class="card-stage">
    @if ($effect->param)
      <div class="card-param mono">{{ $effect->param }}</div>
    @endif
    {!! $effect->demo_html !!}
  </div>
  <div class="card-foot">
    <div class="row">
      <div>
        <div class="name">{{ $effect->name }}</div>
        <div class="desc">{{ $effect->description }}</div>
      </div>
      <button class="view-code plausible-event-name=View+Code plausible-event-effect={{ str_replace(' ', '+', $effect->name) }}">View code</button>
    </div>
  </div>
  <div class="code-panel">
    <div class="code-tabs">
      @foreach ($panes as $pane)
        <button class="code-tab{{ $loop->first ? ' active' : '' }}" data-lang="{{ $pane['lang'] }}">{{ $pane['label'] }}</button>
      @endforeach
    </div>
    <div class="code-body">
      <button class="copy-btn">Copy</button>
      @foreach ($panes as $pane)
        @if ($pane['locked'] ?? false)
          {{-- The gate sits inside the <pre> because main.js toggles panes by
               that element; a sibling would stay visible on every tab. It is
               written on one line because <pre> preserves whitespace. --}}
          <pre data-lang="{{ $pane['lang'] }}" @class(['pro-locked', 'hidden' => ! $loop->first])><code>{{ $pane['content'] }}</code><span class="pro-gate"><span class="pro-gate-copy">@include('partials.icon-lock')<span class="pro-gate-text"><span class="pro-gate-title">{{ __('The rest of this prompt is Pro') }}</span><span class="pro-gate-note">{{ __('All :count prompts, tuned for Cursor and Claude.', ['count' => $libraryTotal ?? \App\Models\Effect::count()]) }}</span></span></span><a class="btn-primary pro-gate-cta" href="{{ config('kinetics.pro_url') }}">{{ __('Unlock prompts') }}</a></span></pre>
        @else
          <pre data-lang="{{ $pane['lang'] }}" @class(['hidden' => ! $loop->first])><code>{{ $pane['content'] }}</code></pre>
        @endif
      @endforeach
    </div>
  </div>
</div>
