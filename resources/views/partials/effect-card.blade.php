@php
    // CSS, React, Prompt — in the order the tabs have always appeared.
    // React is absent for the 54 CSS-only effects, so its tab is skipped
    // entirely rather than rendered empty.
    $panes = collect([
        ['lang' => 'css', 'label' => 'CSS', 'content' => $effect->snippets->firstWhere('type', 'css')?->content],
        ['lang' => 'react', 'label' => 'React', 'content' => $effect->snippets->firstWhere('type', 'react')?->content],
        ['lang' => 'prompt', 'label' => 'Prompt', 'content' => $effect->prompts->firstWhere('type', 'general')?->content],
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
        <pre data-lang="{{ $pane['lang'] }}" @class(['hidden' => ! $loop->first])><code>{{ $pane['content'] }}</code></pre>
      @endforeach
    </div>
  </div>
</div>
