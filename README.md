# Kinetics

A gallery of **35 spring-physics micro-interactions** for web interfaces. Each effect ships with a live demo, a physics-style parameter readout, and copy-paste **CSS** + **React** code.

## Develop

```bash
npm install
npm run dev      # http://localhost:4321
npm run build    # static output -> ./dist
npm run preview
```

## Structure

```
src/
  pages/index.astro      # page shell: <head>, fonts, CSS links, scripts
  content/body.html      # all markup (header, 35 cards, physics, footer),
                         # imported raw so the embedded React snippets
                         # ({}, backticks, ${}) aren't parsed as Astro expressions
public/
  css/   base · hero · gallery · effects-a/b/c · closing
  js/    main.js          # code panel/tabs/copy + all 35 demo interactions
         physics-demo.js  # header oscilloscope + interactive spring simulator
```

## Editing effects

Markup lives in `src/content/body.html`. Each card is a `.card` block with a
`.card-stage` (live demo), `.card-foot` (title + View code), and a `.code-panel`
holding the CSS/React `<pre>` samples. Demo behaviour is wired by class name in
`public/js/main.js`; styling for each demo is in the matching `public/css/effects-*.css`.

Fonts (Archivo, Inter, JetBrains Mono) load from Google Fonts — self-host for
zero external requests.
