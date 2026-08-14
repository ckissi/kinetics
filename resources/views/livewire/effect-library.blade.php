<div>
  @foreach ($categories as $category)
    <section class="section" @if ($loop->first) id="library" @endif>
      <div class="wrap">
        <div class="section-head">
          <h2>{{ $category->name }}</h2>
          <span class="count mono">{{ $category->effects->count() }} / {{ $total }}</span>
        </div>
        <div class="gallery">
          @foreach ($category->effects as $effect)
            @include('partials.effect-card', ['effect' => $effect, 'libraryTotal' => $total])
          @endforeach
        </div>
      </div>
    </section>
  @endforeach
</div>
