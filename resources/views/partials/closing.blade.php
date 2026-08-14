  <section class="section" id="physics">
    <div class="wrap">
      <div class="physics-grid">
        <div>
          <div class="eyebrow">// how it works</div>
          <h2
            class="h-display"
            style="font-size: clamp(28px, 4vw, 40px); margin-top: 8px"
          >
            Two numbers,<br />not a duration.
          </h2>
          <p class="dim" style="max-width: 480px; margin-top: 16px">
            Most CSS motion is built on
            <span class="mono">duration + easing</span> — a fixed time window
            forced through a curve. It looks fine until something interrupts it:
            a second click, a fast scroll, a resize. The animation either snaps
            or stutters, because a duration has no idea where the interruption
            left it.
          </p>
          <p class="dim" style="max-width: 480px; margin-top: 12px">
            A spring has two numbers instead:
            <span class="mono" style="color: var(--bone)">stiffness</span> (how
            hard it pulls toward the target) and
            <span class="mono" style="color: var(--bone)">damping</span> (how
            much it resists oscillating). Interrupt it mid-motion and it just
            keeps integrating from its current velocity — no snap, no restart.
          </p>
        </div>
        <div class="physics-demo-card">
          <div class="physics-row">
            <label for="stiff-slider">stiffness</label>
            <input
              type="range"
              id="stiff-slider"
              aria-labelledby="stiff-val"
              min="80"
              max="500"
              value="320"
            />
            <span class="mono" id="stiff-val">320</span>
          </div>
          <div class="physics-row">
            <label for="damp-slider">damping</label>
            <input type="range" id="damp-slider" aria-labelledby="damp-val" min="5" max="50" value="24" />
            <span class="mono" id="damp-val">24</span>
          </div>
          <div class="physics-stage">
            <div class="physics-ball" id="physics-ball"></div>
          </div>
          <button
            class="btn-secondary mono"
            id="physics-trigger"
            style="width: 100%; margin-top: 16px"
          >
            Release
          </button>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="about">
    <div class="wrap">
      <div class="about-band">
        <div>
          <div class="eyebrow">// about this library</div>
          <h2
            class="h-display"
            style="font-size: clamp(24px, 3.4vw, 32px); margin-top: 8px"
          >
            Built for the copy-paste workflow.
          </h2>
          <p class="dim" style="max-width: 600px; margin-top: 12px">
            Every demo above ships in both plain CSS and React. No build step,
            no animation library dependency — take what you need, tune the two
            numbers in the readout, and move on. If you want to go deeper on the
            theory behind spring physics in interfaces, motion design courses
            like animations.dev are worth your time.
          </p>
        </div>
      </div>
    </div>
  </section>
