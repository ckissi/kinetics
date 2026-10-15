  <section class="hero">
    <div class="wrap">
      <div class="hero-scope">
        <svg viewBox="0 0 1200 84" preserveAspectRatio="none">
          <line x1="0" y1="42" x2="1200" y2="42" class="grid-line"></line>
          <polyline
            id="wave-path-2"
            class="wave secondary"
            points=""
          ></polyline>
          <polyline id="wave-path" class="wave" points=""></polyline>
        </svg>
        <div class="hero-readout">
          <span>damping <b>24</b></span>
          <span>stiffness <b>320</b></span>
          <span>mass <b>1.0</b></span>
        </div>
      </div>

      <div class="hero-grid">
        <div>
          <div class="eyebrow">// 162 spring-driven interactions</div>
          <h1 class="h-display">
            Motion that<br />has <span class="accent">weight.</span>
          </h1>
          <p class="hero-sub">
            A library of interface animations built on spring physics instead of
            fixed-duration easing. Tune stiffness and damping, then copy the
            CSS, the React, or a ready-made
            <span class="accent">AI prompt</span> — and drop the motion straight
            into your app.
          </p>
          <div class="hero-cta">
            <a href="#library" class="btn-primary">Browse the library</a>
            <a href="#physics" class="btn-secondary mono">How it works</a>
          </div>
        </div>
        <div class="hero-meta-col">
          <div class="search-bar hero-search" id="search">
            <div class="search-field">
              <svg
                class="search-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                aria-hidden="true"
              >
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="21" y1="21" x2="16.4" y2="16.4"></line>
              </svg>
              <input
                type="search"
                id="effect-search"
                placeholder="Search 162 effects&hellip;"
                aria-label="Search effects"
                autocomplete="off"
                spellcheck="false"
              />
              <span class="search-count mono" id="search-count" hidden></span>
              <button class="search-clear mono" id="search-clear" hidden aria-label="Clear search">✕</button>
              <kbd class="search-kbd mono" aria-hidden="true">/</kbd>
            </div>
          </div>
          <div class="hero-meta">
            Every effect ships three ways —<br />
            <b>CSS</b>, <b>React</b>, and a copy-paste
            <b>AI&nbsp;prompt</b> —<br />
            alongside a live <b>stiffness / damping</b> readout,<br />
            so you're copying a tuned spring, not a guess.
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="search-empty" id="search-empty" hidden>
    <div class="wrap">
      <p>No effects match &ldquo;<span id="search-empty-q"></span>&rdquo;.</p>
      <div class="search-suggestions">
        <span class="mono">Try:</span>
        <button class="search-suggestion mono" data-q="input">input</button>
        <button class="search-suggestion mono" data-q="loading">loading</button>
        <button class="search-suggestion mono" data-q="hover">hover</button>
        <button class="search-suggestion mono" data-q="currency">currency</button>
        <button class="search-suggestion mono" data-q="3d">3d</button>
        <button class="search-suggestion mono" data-q="text">text</button>
      </div>
    </div>
  </div>
