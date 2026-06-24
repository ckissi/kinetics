/// <reference path="../.astro/types.d.ts" />

// Plausible Analytics type declarations
interface Window {
  plausible:
    | {
        (event: string, options?: Record<string, any>): void;
        q: any[];
        init: (options?: Record<string, any>) => void;
        o: Record<string, any>;
      }
    | undefined;
}

declare const plausible: {
  (event: string, options?: Record<string, any>): void;
  q: any[];
  init: (options?: Record<string, any>) => void;
  o: Record<string, any>;
};
