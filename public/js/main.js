/* ============================================================
   Kinetics — main.js
   Code-panel UI + all 35 live demo interactions
   ============================================================ */
(function () {
  'use strict';

  const $  = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  /* ---------------------------------------------------------
     Code modal: "View code" opens a shared dialog.
     Each card keeps its .code-panel as a hidden template; we
     clone its tabs + body into the modal on open.
  --------------------------------------------------------- */
  const modal = (function buildModal() {
    const el = document.createElement('div');
    el.className = 'code-modal';
    el.id = 'code-modal';
    el.hidden = true;
    el.innerHTML =
      '<div class="code-modal-backdrop" data-close></div>' +
      '<div class="code-modal-dialog" role="dialog" aria-modal="true" aria-label="Source code">' +
        '<div class="code-modal-head">' +
          '<div class="code-modal-title"></div>' +
          '<button class="code-modal-close" data-close aria-label="Close">✕</button>' +
        '</div>' +
        '<div class="code-modal-content"></div>' +
      '</div>';
    document.body.appendChild(el);
    return el;
  })();

  const modalTitle = $('.code-modal-title', modal);
  const modalContent = $('.code-modal-content', modal);
  let lastTrigger = null;

  const openModal = (card) => {
    const panel = $('.code-panel', card);
    if (!panel) return;
    const name = (card.querySelector('.name') || {}).textContent || 'Source';
    const param = (card.querySelector('.card-param') || {}).textContent || '';
    modalTitle.innerHTML = name + (param ? '<span class="param">' + param + '</span>' : '');

    modalContent.innerHTML = '';
    const clone = panel.cloneNode(true);
    clone.classList.remove('open');
    modalContent.appendChild(clone);

    // normalise to the first (CSS) tab on every open
    const tabs = $$('.code-tab', clone);
    const pres = $$('pre[data-lang]', clone);
    tabs.forEach((t, i) => t.classList.toggle('active', i === 0));
    const first = tabs[0] && tabs[0].dataset.lang;
    pres.forEach((p) => p.classList.toggle('hidden', p.dataset.lang !== first));

    modal.hidden = false;
    requestAnimationFrame(() => modal.classList.add('open'));
    document.body.style.overflow = 'hidden';
  };

  const closeModal = () => {
    modal.classList.remove('open');
    document.body.style.overflow = '';
    setTimeout(() => { modal.hidden = true; modalContent.innerHTML = ''; }, 280);
    if (lastTrigger) lastTrigger.focus();
  };

  $$('.view-code').forEach((btn) => {
    btn.addEventListener('click', () => {
      lastTrigger = btn;
      openModal(btn.closest('.card'));
    });
  });

  // Tab switching + copy, delegated within the modal
  modal.addEventListener('click', async (e) => {
    if (e.target.closest('[data-close]')) { closeModal(); return; }

    const tab = e.target.closest('.code-tab');
    if (tab) {
      const lang = tab.dataset.lang;
      $$('.code-tab', modalContent).forEach((t) => t.classList.toggle('active', t === tab));
      $$('pre[data-lang]', modalContent).forEach((pre) => {
        pre.classList.toggle('hidden', pre.dataset.lang !== lang);
      });
      return;
    }

    const copyBtn = e.target.closest('.copy-btn');
    if (copyBtn) {
      const visible = $$('pre[data-lang]', modalContent).find((p) => !p.classList.contains('hidden'));
      if (!visible) return;
      try {
        await navigator.clipboard.writeText(visible.innerText);
      } catch (_) {
        const r = document.createRange();
        r.selectNodeContents(visible);
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(r);
        document.execCommand('copy');
        sel.removeAllRanges();
      }
      copyBtn.textContent = 'Copied';
      copyBtn.classList.add('copied');
      setTimeout(() => { copyBtn.textContent = 'Copy'; copyBtn.classList.remove('copied'); }, 1400);
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) closeModal();
  });

  /* ---------------------------------------------------------
     1. Spring card resize
  --------------------------------------------------------- */
  $$('.demo-spring-card').forEach((card) => {
    card.addEventListener('click', () => card.classList.toggle('expanded'));
  });

  /* ---------------------------------------------------------
     2. Magnetic button
  --------------------------------------------------------- */
  $$('.demo-magnet-zone').forEach((zone) => {
    const btn = $('.demo-magnet-btn', zone);
    if (!btn) return;
    const PULL = 0.35;
    zone.addEventListener('mousemove', (e) => {
      const r = btn.getBoundingClientRect();
      const x = e.clientX - (r.left + r.width / 2);
      const y = e.clientY - (r.top + r.height / 2);
      btn.style.transform = `translate(${x * PULL}px, ${y * PULL}px)`;
    });
    zone.addEventListener('mouseleave', () => { btn.style.transform = 'translate(0,0)'; });
  });

  /* ---------------------------------------------------------
     3. Elastic counter
  --------------------------------------------------------- */
  $$('.demo-counter').forEach((el) => {
    let val = parseInt(el.dataset.value || el.textContent, 10) || 0;
    el.addEventListener('click', () => {
      val += 1;
      el.textContent = val;
      el.classList.remove('bump');
      void el.offsetWidth; // restart transition
      el.classList.add('bump');
      setTimeout(() => el.classList.remove('bump'), 400);
    });
  });

  /* ---------------------------------------------------------
     4. Toast overshoot
  --------------------------------------------------------- */
  $$('.demo-toast-trigger').forEach((btn) => {
    const zone = btn.closest('.demo-toast-zone');
    const toast = $('.demo-toast', zone);
    let timer;
    btn.addEventListener('click', () => {
      clearTimeout(timer);
      toast.classList.add('show');
      timer = setTimeout(() => toast.classList.remove('show'), 2200);
    });
  });

  /* ---------------------------------------------------------
     5. Tab pill glide
  --------------------------------------------------------- */
  $$('.demo-tabs').forEach((tabs) => {
    const pill = $('.demo-tab-pill', tabs);
    const btns = $$('.demo-tab-btn', tabs);
    const move = (target) => {
      pill.style.left = target.offsetLeft + 'px';
      pill.style.width = target.offsetWidth + 'px';
    };
    const active = btns.find((b) => b.classList.contains('active')) || btns[0];
    if (active) requestAnimationFrame(() => move(active));
    btns.forEach((b) => b.addEventListener('click', () => {
      btns.forEach((x) => x.classList.toggle('active', x === b));
      move(b);
    }));
  });

  /* ---------------------------------------------------------
     6. Accordion spring
  --------------------------------------------------------- */
  $$('.demo-acc-head').forEach((head) => {
    head.addEventListener('click', () => {
      head.closest('.demo-acc-item').classList.toggle('open');
    });
  });

  /* ---------------------------------------------------------
     7. Drag to dismiss
  --------------------------------------------------------- */
  $$('.demo-drag-card').forEach((card) => {
    let startX = 0, x = 0, dragging = false;
    const reset = () => { x = 0; card.style.transform = ''; card.style.opacity = ''; };

    const down = (e) => {
      dragging = true; startX = e.clientX;
      card.classList.add('dragging');
      card.setPointerCapture(e.pointerId);
    };
    const move = (e) => {
      if (!dragging) return;
      x = e.clientX - startX;
      card.style.transform = `translateX(${x}px) rotate(${x * 0.04}deg)`;
      card.style.opacity = Math.max(1 - Math.abs(x) / 260, 0.25);
    };
    const up = () => {
      if (!dragging) return;
      dragging = false;
      card.classList.remove('dragging');
      if (Math.abs(x) > 100) {
        card.classList.add('gone');
        card.style.transform = `translateX(${x > 0 ? 400 : -400}px) rotate(${x > 0 ? 24 : -24}deg)`;
        card.style.opacity = '0';
        setTimeout(() => { card.classList.remove('gone'); reset(); }, 480);
      } else {
        reset();
      }
    };
    card.addEventListener('pointerdown', down);
    card.addEventListener('pointermove', move);
    card.addEventListener('pointerup', up);
    card.addEventListener('pointercancel', up);
  });

  /* ---------------------------------------------------------
     8. Ripple feedback (delegated to all ripple buttons)
  --------------------------------------------------------- */
  $$('.demo-ripple-btn').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      const r = btn.getBoundingClientRect();
      const size = Math.max(r.width, r.height);
      const span = document.createElement('span');
      span.className = 'ripple';
      span.style.width = span.style.height = size + 'px';
      span.style.left = (e.clientX - r.left - size / 2) + 'px';
      span.style.top  = (e.clientY - r.top  - size / 2) + 'px';
      btn.appendChild(span);
      setTimeout(() => span.remove(), 650);
    });
  });

  /* ---------------------------------------------------------
     9. Scramble reveal
  --------------------------------------------------------- */
  const SCRAMBLE_CHARS = '!<>-_/[]{}=+*^?#________';
  $$('.demo-scramble').forEach((el) => {
    const text = el.dataset.text || el.textContent;
    let running = false;
    const play = () => {
      if (running) return;
      running = true;
      let frame = 0;
      const total = 26;
      const id = setInterval(() => {
        frame++;
        el.textContent = text.split('').map((c, i) => {
          if (c === ' ') return ' ';
          const progress = frame - i * 1.4;
          if (progress > total * 0.55) return c;
          return SCRAMBLE_CHARS[Math.floor(Math.random() * SCRAMBLE_CHARS.length)];
        }).join('');
        if (frame > total + text.length) {
          clearInterval(id);
          el.textContent = text;
          running = false;
        }
      }, 35);
    };
    el.addEventListener('click', play);
    el.addEventListener('mouseenter', play);
  });

  /* ---------------------------------------------------------
     10. Morph icon swap
  --------------------------------------------------------- */
  $$('.demo-morph-icon').forEach((wrap) => {
    const svgs = $$('svg', wrap);
    if (svgs.length < 2) return;
    wrap.addEventListener('click', () => {
      svgs.forEach((svg) => {
        const showing = svg.classList.contains('show');
        svg.classList.toggle('show', !showing);
        svg.classList.toggle('hide', showing);
      });
    });
  });

  /* ---------------------------------------------------------
     11. Elastic progress randomize
  --------------------------------------------------------- */
  $$('.demo-progress-trigger').forEach((btn) => {
    const fill = $('.demo-progress-fill', btn.closest('.card-stage'));
    btn.addEventListener('click', () => {
      fill.style.width = (15 + Math.floor(Math.random() * 80)) + '%';
    });
  });

  /* ---------------------------------------------------------
     12. Switch spring
  --------------------------------------------------------- */
  $$('.demo-switch').forEach((sw) => {
    sw.addEventListener('click', () => sw.classList.toggle('on'));
  });

  /* ---------------------------------------------------------
     13. Error shake (retriggerable)
  --------------------------------------------------------- */
  $$('.demo-shake-trigger').forEach((btn) => {
    const zone = btn.closest('.demo-shake-zone');
    const input = $('.demo-shake-input', zone);
    btn.addEventListener('click', () => {
      zone.classList.add('invalid');
      input.classList.remove('error');
      void input.offsetWidth;
      input.classList.add('error');
    });
    input.addEventListener('animationend', () => input.classList.remove('error'));
  });

  /* ---------------------------------------------------------
     14. Confetti burst
  --------------------------------------------------------- */
  const CONFETTI_COLORS = ['#FF8A00', '#5B8DEF', '#4CD08A', '#EDE9E0'];
  $$('.demo-confetti-btn').forEach((btn) => {
    const zone = btn.closest('.demo-confetti-zone');
    btn.addEventListener('click', () => {
      const N = 16;
      for (let i = 0; i < N; i++) {
        const angle = (Math.PI * 2 * i) / N + Math.random() * 0.4;
        const dist = 55 + Math.random() * 55;
        const p = document.createElement('span');
        p.className = 'confetti-particle';
        p.style.background = CONFETTI_COLORS[i % CONFETTI_COLORS.length];
        p.style.setProperty('--tx', `calc(-50% + ${Math.cos(angle) * dist}px)`);
        p.style.setProperty('--ty', `calc(-50% + ${Math.sin(angle) * dist}px)`);
        p.style.setProperty('--rot', `${Math.random() * 360}deg`);
        zone.appendChild(p);
        setTimeout(() => p.remove(), 950);
      }
    });
  });

  /* ---------------------------------------------------------
     15. Parallax tilt
  --------------------------------------------------------- */
  $$('.demo-tilt-zone').forEach((zone) => {
    const card = $('.demo-tilt-card', zone);
    const glow = $('.glow', card);
    zone.addEventListener('mousemove', (e) => {
      const r = card.getBoundingClientRect();
      const px = (e.clientX - r.left) / r.width;
      const py = (e.clientY - r.top) / r.height;
      card.style.transform = `rotateX(${(py - 0.5) * -16}deg) rotateY(${(px - 0.5) * 16}deg)`;
      if (glow) { glow.style.left = px * 100 + '%'; glow.style.top = py * 100 + '%'; }
    });
    zone.addEventListener('mouseleave', () => { card.style.transform = ''; });
  });

  /* ---------------------------------------------------------
     16. Page peel
  --------------------------------------------------------- */
  $$('.demo-peel-trigger').forEach((btn) => {
    const zone = btn.closest('.demo-peel-zone');
    const cards = $$('.demo-peel-card', zone); // [back, front] in DOM order
    btn.addEventListener('click', () => {
      // peel the topmost (last in DOM) card that isn't peeled yet
      const top = cards.slice().reverse().find((c) => !c.classList.contains('peeled'));
      if (top) {
        top.classList.add('peeled');
      } else {
        cards.forEach((c) => c.classList.remove('peeled'));
      }
    });
  });

  /* ---------------------------------------------------------
     17. Cursor spotlight
  --------------------------------------------------------- */
  $$('.demo-spotlight-zone').forEach((zone) => {
    const glow = $('.demo-spotlight-glow', zone);
    zone.addEventListener('mousemove', (e) => {
      const r = zone.getBoundingClientRect();
      glow.style.left = (e.clientX - r.left) + 'px';
      glow.style.top  = (e.clientY - r.top) + 'px';
    });
  });

  /* ---------------------------------------------------------
     18. Stagger entrance (IntersectionObserver re-trigger)
  --------------------------------------------------------- */
  $$('.demo-stagger-list').forEach((list) => {
    const items = $$('.demo-stagger-item', list);
    items.forEach((it) => it.classList.remove('in'));
    const reveal = () => items.forEach((it, i) => setTimeout(() => it.classList.add('in'), i * 90));
    if ('IntersectionObserver' in window) {
      const obs = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) { reveal(); obs.disconnect(); }
        });
      }, { threshold: 0.4 });
      obs.observe(list);
    } else {
      reveal();
    }
  });

  /* ---------------------------------------------------------
     19. Hold to confirm
  --------------------------------------------------------- */
  $$('.demo-hold-btn').forEach((btn) => {
    const label = $('.demo-hold-label', btn);
    let timer = null;
    const start = (e) => {
      if (btn.classList.contains('done')) return;
      e.preventDefault();
      btn.classList.add('holding');
      timer = setTimeout(() => {
        btn.classList.remove('holding');
        btn.classList.add('done');
        if (label) label.textContent = '✓';
        setTimeout(() => {
          btn.classList.remove('done');
          if (label) label.textContent = 'Hold';
        }, 1400);
      }, 800);
    };
    const cancel = () => {
      clearTimeout(timer);
      if (!btn.classList.contains('done')) btn.classList.remove('holding');
    };
    btn.addEventListener('pointerdown', start);
    btn.addEventListener('pointerup', cancel);
    btn.addEventListener('pointerleave', cancel);
    btn.addEventListener('pointercancel', cancel);
  });

  /* ---------------------------------------------------------
     20. Rubber-band slider
  --------------------------------------------------------- */
  $$('.demo-rubber').forEach((root) => {
    const track = $('.demo-rubber-track', root);
    const thumb = $('.demo-rubber-thumb', root);
    const fill = $('.demo-rubber-fill', root);
    const out = $('.demo-rubber-val', root);
    let dragging = false;

    const apply = (clientX, rubber) => {
      const r = track.getBoundingClientRect();
      let raw = (clientX - r.left) / r.width;       // may go <0 or >1
      const clamped = Math.min(Math.max(raw, 0), 1);
      let pct = clamped;
      if (rubber) pct = clamped + (raw - clamped) * 0.32; // resistance past ends
      thumb.style.left = (pct * 100) + '%';
      fill.style.width = (clamped * 100) + '%';
      if (out) out.textContent = clamped.toFixed(2);
    };

    const down = (e) => {
      dragging = true;
      thumb.classList.add('dragging');
      thumb.classList.remove('snap'); fill.classList.remove('snap');
      thumb.setPointerCapture && thumb.setPointerCapture(e.pointerId);
      apply(e.clientX, true);
    };
    const move = (e) => { if (dragging) apply(e.clientX, true); };
    const up = (e) => {
      if (!dragging) return;
      dragging = false;
      thumb.classList.remove('dragging');
      thumb.classList.add('snap'); fill.classList.add('snap');
      apply(e.clientX, false); // snap back inside range
    };
    thumb.addEventListener('pointerdown', down);
    thumb.addEventListener('pointermove', move);
    thumb.addEventListener('pointerup', up);
    thumb.addEventListener('pointercancel', up);
  });

  /* ---------------------------------------------------------
     21. Like burst
  --------------------------------------------------------- */
  $$('.demo-like-btn').forEach((btn) => {
    const zone = btn.closest('.demo-like-zone');
    const count = $('.demo-like-count', btn);
    let base = parseInt(count.textContent.replace(/\D/g, ''), 10) || 0;
    btn.addEventListener('click', () => {
      const liked = btn.classList.toggle('liked');
      count.textContent = base + (liked ? 1 : 0);
      btn.classList.remove('pop');
      void btn.offsetWidth;
      btn.classList.add('pop');
      setTimeout(() => btn.classList.remove('pop'), 320);
      if (liked) {
        for (let i = 0; i < 8; i++) {
          const a = (Math.PI * 2 * i) / 8;
          const d = 22 + Math.random() * 14;
          const p = document.createElement('span');
          p.className = 'like-particle';
          p.style.setProperty('--tx', Math.cos(a) * d + 'px');
          p.style.setProperty('--ty', Math.sin(a) * d + 'px');
          zone.appendChild(p);
          setTimeout(() => p.remove(), 620);
        }
      }
    });
  });

  /* ---------------------------------------------------------
     22. Cursor trail
  --------------------------------------------------------- */
  $$('.demo-trail-zone').forEach((zone) => {
    const dots = $$('.demo-trail-dot', zone);
    if (!dots.length) return;
    const pts = dots.map(() => ({ x: -20, y: -20 }));
    let target = { x: -20, y: -20 };
    let active = false;

    zone.addEventListener('pointerenter', () => { active = true; });
    zone.addEventListener('pointermove', (e) => {
      const r = zone.getBoundingClientRect();
      target = { x: e.clientX - r.left, y: e.clientY - r.top };
    });
    zone.addEventListener('pointerleave', () => { active = false; });

    const tick = () => {
      let lead = target;
      pts.forEach((p, i) => {
        p.x += (lead.x - p.x) * 0.35;
        p.y += (lead.y - p.y) * 0.35;
        dots[i].style.transform = `translate(${p.x}px, ${p.y}px)`;
        dots[i].style.opacity = active ? '' : '0';
        lead = p;
      });
      requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  });

  /* ---------------------------------------------------------
     23. Checkbox draw
  --------------------------------------------------------- */
  $$('.demo-check').forEach((box) => {
    box.addEventListener('click', (e) => {
      e.preventDefault();
      box.classList.toggle('checked');
    });
  });

  /* ---------------------------------------------------------
     24. Typewriter
  --------------------------------------------------------- */
  $$('.demo-typewriter').forEach((el) => {
    const out = $('.demo-type-text', el);
    const phrases = (el.dataset.phrases || 'spring(320, 24);damping matters;ship the motion').split(';');
    let pi = 0, ci = 0, deleting = false;
    const loop = () => {
      const word = phrases[pi];
      out.textContent = word.slice(0, ci);
      if (!deleting && ci < word.length) {
        ci++; setTimeout(loop, 55);
      } else if (!deleting && ci === word.length) {
        deleting = true; setTimeout(loop, 1100);
      } else if (deleting && ci > 0) {
        ci--; setTimeout(loop, 30);
      } else {
        deleting = false; pi = (pi + 1) % phrases.length; setTimeout(loop, 320);
      }
    };
    loop();
  });

  /* ---------------------------------------------------------
     25. Odometer count-up (on scroll into view)
  --------------------------------------------------------- */
  $$('.demo-odometer').forEach((el) => {
    const target = parseInt(el.dataset.target, 10) || 0;
    const fmt = (n) => Math.round(n).toLocaleString('en-US');
    let done = false;
    const run = () => {
      if (done) return; done = true;
      const dur = 1400, t0 = performance.now();
      const step = (t) => {
        const p = Math.min((t - t0) / dur, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = fmt(target * eased);
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = fmt(target);
      };
      requestAnimationFrame(step);
    };
    if ('IntersectionObserver' in window) {
      const obs = new IntersectionObserver((entries) => {
        entries.forEach((en) => { if (en.isIntersecting) { run(); obs.disconnect(); } });
      }, { threshold: 0.5 });
      obs.observe(el);
    } else { run(); }
  });

  /* ---------------------------------------------------------
     26. Status pill (idle -> loading -> success)
  --------------------------------------------------------- */
  const CHECK_SVG = '<svg viewBox="0 0 24 24"><path d="M5 12.5 L10 17.5 L19 7"/></svg>';
  $$('.demo-status-pill').forEach((pill) => {
    const icon = $('.demo-status-icon', pill);
    const text = $('.demo-status-text', pill);
    const label = text ? text.textContent : 'Deploy';
    let busy = false;
    pill.addEventListener('click', () => {
      if (busy) return;
      const state = pill.dataset.state;
      if (state === 'success') {
        pill.dataset.state = 'idle'; icon.innerHTML = ''; if (text) text.textContent = label;
        return;
      }
      busy = true;
      pill.dataset.state = 'loading'; icon.innerHTML = ''; if (text) text.textContent = 'Deploying…';
      setTimeout(() => {
        pill.dataset.state = 'success'; icon.innerHTML = CHECK_SVG; if (text) text.textContent = 'Deployed';
        busy = false;
      }, 1500);
    });
  });

  /* ---------------------------------------------------------
     27. Flip card
  --------------------------------------------------------- */
  $$('.demo-flip-card').forEach((card) => {
    card.addEventListener('click', () => card.classList.toggle('flipped'));
  });

  /* ---------------------------------------------------------
     28. Star rating
  --------------------------------------------------------- */
  $$('.demo-rating').forEach((rating) => {
    const stars = $$('.demo-star', rating);
    const paint = (n) => stars.forEach((s, i) => s.classList.toggle('on', i < n));
    const current = () => parseInt(rating.dataset.value, 10) || 0;
    stars.forEach((star) => {
      const n = parseInt(star.dataset.i, 10);
      star.addEventListener('mouseenter', () => paint(n));
      star.addEventListener('click', () => {
        rating.dataset.value = n;
        paint(n);
        star.classList.remove('pop');
        void star.offsetWidth;
        star.classList.add('pop');
        setTimeout(() => star.classList.remove('pop'), 260);
      });
    });
    rating.addEventListener('mouseleave', () => paint(current()));
  });

  /* ---------------------------------------------------------
     29. Success check (draw on toggle)
  --------------------------------------------------------- */
  $$('.demo-draw-check').forEach((btn) => {
    btn.addEventListener('click', () => btn.classList.toggle('done'));
  });

  /* ---------------------------------------------------------
     30. Segment loader (staggered fill)
  --------------------------------------------------------- */
  $$('.demo-segments-trigger').forEach((btn) => {
    const zone = btn.closest('.demo-segments-zone');
    const segments = $$('.demo-segment', zone);
    btn.addEventListener('click', () => {
      segments.forEach((s) => s.classList.remove('filled'));
      // reflow so a rapid re-run animates from empty again
      void zone.offsetWidth;
      segments.forEach((s, i) => setTimeout(() => s.classList.add('filled'), i * 120));
    });
  });

  /* ---------------------------------------------------------
     31. Copy button
  --------------------------------------------------------- */
  $$('.demo-copy-btn').forEach((btn) => {
    const label = $('.demo-copy-label', btn);
    let timer = null;
    btn.addEventListener('click', async () => {
      const text = btn.dataset.copy || '';
      try {
        await navigator.clipboard.writeText(text);
      } catch (_) { /* clipboard may be unavailable; still show feedback */ }
      btn.classList.add('copied');
      if (label) label.textContent = 'Copied';
      clearTimeout(timer);
      timer = setTimeout(() => {
        btn.classList.remove('copied');
        if (label) label.textContent = 'Copy';
      }, 1400);
    });
  });

  /* ---------------------------------------------------------
     32. Quantity stepper
  --------------------------------------------------------- */
  $$('.demo-stepper').forEach((stepper) => {
    const val = $('.demo-step-val', stepper);
    let n = parseInt(val.textContent, 10) || 0;
    $$('.demo-step-btn', stepper).forEach((btn) => {
      btn.addEventListener('click', () => {
        const dir = parseInt(btn.dataset.dir, 10) || 0;
        n = Math.max(0, n + dir);
        val.textContent = n;
        val.classList.remove('bump');
        void val.offsetWidth;
        val.classList.add('bump');
        setTimeout(() => val.classList.remove('bump'), 350);
      });
    });
  });

  /* ---------------------------------------------------------
     33. Choice chips
  --------------------------------------------------------- */
  $$('.demo-chip').forEach((chip) => {
    chip.addEventListener('click', () => {
      chip.classList.toggle('on');
      chip.classList.remove('pop');
      void chip.offsetWidth;
      chip.classList.add('pop');
      setTimeout(() => chip.classList.remove('pop'), 300);
    });
  });

  /* ---------------------------------------------------------
     34. Progress ring (randomize)
  --------------------------------------------------------- */
  $$('.demo-ring-trigger').forEach((btn) => {
    const zone = btn.closest('.demo-ring-zone');
    const prog = $('.demo-ring .prog', zone);
    const out = $('.demo-ring-val', zone);
    const C = 2 * Math.PI * 34; // r = 34
    const set = (pct) => {
      prog.style.strokeDashoffset = C * (1 - pct / 100);
      if (out) out.textContent = pct + '%';
    };
    set(0);
    btn.addEventListener('click', () => set(10 + Math.floor(Math.random() * 90)));
  });

  /* ---------------------------------------------------------
     35. Notification slide-in
  --------------------------------------------------------- */
  $$('.demo-notify-trigger').forEach((btn) => {
    const zone = btn.closest('.demo-notify-zone');
    const notify = $('.demo-notify', zone);
    let timer;
    btn.addEventListener('click', () => {
      clearTimeout(timer);
      notify.classList.add('show');
      timer = setTimeout(() => notify.classList.remove('show'), 2200);
    });
  });

  /* 19a. Pointer tooltip — label eases toward the cursor each frame */
  $$('.demo-pointer-zone').forEach((zone) => {
    const tip = $('.demo-pointer-tip', zone);
    if (!tip) return;
    const pos = { x: 0, y: 0, tx: 0, ty: 0 };
    let raf = null, active = false;

    const loop = () => {
      pos.x += (pos.tx - pos.x) * 0.18;
      pos.y += (pos.ty - pos.y) * 0.18;
      tip.style.transform =
        'translate(' + pos.x + 'px, ' + pos.y + 'px) translate(-50%, -140%)';
      tip.textContent = 'x: ' + Math.round(pos.tx) + ', y: ' + Math.round(pos.ty);
      if (active || Math.abs(pos.tx - pos.x) > 0.3 || Math.abs(pos.ty - pos.y) > 0.3) {
        raf = requestAnimationFrame(loop);
      } else {
        raf = null;
      }
    };
    zone.addEventListener('mousemove', (e) => {
      const r = zone.getBoundingClientRect();
      pos.tx = e.clientX - r.left;
      pos.ty = e.clientY - r.top;
      active = true;
      if (!raf) raf = requestAnimationFrame(loop);
    });
    zone.addEventListener('mouseleave', () => { active = false; });
  });

  /* 19b. PIN input — digit pops and focus auto-advances */
  $$('.demo-pin').forEach((group) => {
    const boxes = $$('.demo-pin-box', group);
    boxes.forEach((box, i) => {
      box.addEventListener('input', () => {
        box.value = box.value.replace(/\D/g, '').slice(0, 1);
        if (box.value) {
          box.classList.add('filled', 'pop');
          setTimeout(() => box.classList.remove('pop'), 350);
          if (boxes[i + 1]) boxes[i + 1].focus();
        } else {
          box.classList.remove('filled');
        }
      });
      box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !box.value && boxes[i - 1]) boxes[i - 1].focus();
      });
    });
  });

  /* 19d. Password meter — score fills and tints the segment bars */
  $$('.demo-pwd').forEach((field) => {
    const input = $('.demo-pwd-input', field);
    const meter = $('.demo-pwd-meter', field);
    const label = $('.demo-pwd-label', field);
    const words = ['Strength', 'Weak', 'Fair', 'Good', 'Strong'];
    input.addEventListener('input', () => {
      const v = input.value;
      let s = 0;
      if (v.length > 4) s++;
      if (v.length > 8) s++;
      if (/[0-9]/.test(v)) s++;
      if (/[^a-zA-Z0-9]/.test(v)) s++;
      s = Math.min(s, 4);
      meter.setAttribute('data-score', s);
      label.textContent = words[s];
    });
  });

  /* 20a. Undo snackbar — timed bar drains, dismissible early */
  $$('.demo-undo-trigger').forEach((btn) => {
    const zone = btn.closest('.demo-undo-zone');
    const bar = $('.demo-undo-bar', zone);
    const progress = $('.demo-undo-progress', zone);
    const undo = $('.demo-undo-action', zone);
    let timer;
    const hide = () => { clearTimeout(timer); bar.classList.remove('show'); };
    btn.addEventListener('click', () => {
      clearTimeout(timer);
      bar.classList.remove('show');
      // force reflow so the drain animation restarts on rapid clicks
      void progress.offsetWidth;
      bar.classList.add('show');
      timer = setTimeout(hide, 3000);
    });
    if (undo) undo.addEventListener('click', hide);
  });

  /* 21a. Submit states — label -> dots -> checkmark, self-resetting */
  $$('.demo-loaddone-btn').forEach((btn) => {
    let busy = false;
    btn.addEventListener('click', () => {
      if (busy) return;
      busy = true;
      btn.classList.add('loading');
      setTimeout(() => {
        btn.classList.remove('loading');
        btn.classList.add('done');
        setTimeout(() => {
          btn.classList.remove('done');
          busy = false;
        }, 1500);
      }, 1400);
    });
  });

  /* 20b. Step progress — Next advances the fill and active nodes, wraps */
  $$('.demo-steps-next').forEach((btn) => {
    const zone = btn.closest('.demo-steps-zone');
    const steps = $('.demo-steps', zone);
    const nodes = $$('.demo-step', steps);
    const count = nodes.length;
    const apply = (n) => {
      steps.setAttribute('data-step', n);
      nodes.forEach((node, i) => node.classList.toggle('active', i < n));
    };
    apply(1);
    btn.addEventListener('click', () => {
      const cur = parseInt(steps.getAttribute('data-step'), 10) || 1;
      apply((cur % count) + 1);
    });
  });

})();
