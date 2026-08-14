@php
    // CSS, React, Prompt — in the order the tabs have always appeared.
    // React is absent for the 54 CSS-only effects, so its tab is skipped
    // entirely rather than rendered empty.
    $prompt = $effect->prompts->firstWhere('type', 'general');

    // Prompts are Pro content. The tab still shows for everyone, because
    // hiding it entirely would hide what the subscription is for, but the
    // text itself is only rendered for those who may read it.
    $promptLocked = $prompt && ! Gate::allows('view-pro-content');

    $panes = collect([
        ['lang' => 'css', 'label' => 'CSS', 'content' => $effect->snippets->firstWhere('type', 'css')?->content],
        ['lang' => 'react', 'label' => 'React', 'content' => $effect->snippets->firstWhere('type', 'react')?->content],
        ['lang' => 'prompt', 'label' => 'Prompt', 'content' => $prompt?->content, 'locked' => $promptLocked],
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
          <pre data-lang="{{ $pane['lang'] }}" @class(['pro-locked', 'hidden' => ! $loop->first])><code>{{ __('This prompt is part of Kinetics Pro.') }}</code></pre>
        @else
          <pre data-lang="{{ $pane['lang'] }}" @class(['hidden' => ! $loop->first])><code>{{ $pane['content'] }}</code></pre>
        @endif
      @endforeach
    </div>
  </div>
</div>
