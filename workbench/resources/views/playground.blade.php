<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel imgproxy — playground</title>
    <style>
        /* ------------------------------------------------------------------
           Tokens. Light = daylight light table; dark = space navy. Status
           colors live in their own lane, apart from the accent.
        ------------------------------------------------------------------ */
        :root {
            --bg: #f3f7fb;
            --surface: #ffffff;
            --ink: #14293d;
            --ink-2: #4c6278;
            --line: #d3dfeb;
            --accent: #1f6fd6;
            --accent-ink: #ffffff;
            --focus: #0b4f9e;
            --ok: #1a7f37;
            --warn: #96680a;
            --bad: #c92c34;
            --stage: #e8eff7;
            --term-bg: #0e1726;
            --term-ink: #d9e5f3;
            --term-line: #22334d;
            --shadow: 0 1px 2px rgba(16, 40, 66, .06), 0 8px 24px -18px rgba(16, 40, 66, .35);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0a111d;
                --surface: #101a2b;
                --ink: #e8eef8;
                --ink-2: #8fa2bb;
                --line: #22324a;
                --accent: #6fb1ff;
                --accent-ink: #0a111d;
                --focus: #9cc4ff;
                --ok: #46c17a;
                --warn: #e0ad41;
                --bad: #f47067;
                --stage: #0c1524;
                --term-bg: #060b14;
                --term-ink: #cfe0f2;
                --term-line: #1d2c42;
                --shadow: 0 1px 2px rgba(0, 0, 0, .35), 0 10px 28px -18px rgba(0, 0, 0, .6);
            }
        }

        :root[data-theme="light"] {
            --bg: #f3f7fb;
            --surface: #ffffff;
            --ink: #14293d;
            --ink-2: #4c6278;
            --line: #d3dfeb;
            --accent: #1f6fd6;
            --accent-ink: #ffffff;
            --focus: #0b4f9e;
            --ok: #1a7f37;
            --warn: #96680a;
            --bad: #c92c34;
            --stage: #e8eff7;
            --term-bg: #0e1726;
            --term-ink: #d9e5f3;
            --term-line: #22334d;
            --shadow: 0 1px 2px rgba(16, 40, 66, .06), 0 8px 24px -18px rgba(16, 40, 66, .35);
        }

        :root[data-theme="dark"] {
            --bg: #0a111d;
            --surface: #101a2b;
            --ink: #e8eef8;
            --ink-2: #8fa2bb;
            --line: #22324a;
            --accent: #6fb1ff;
            --accent-ink: #0a111d;
            --focus: #9cc4ff;
            --ok: #46c17a;
            --warn: #e0ad41;
            --bad: #f47067;
            --stage: #0c1524;
            --term-bg: #060b14;
            --term-ink: #cfe0f2;
            --term-line: #1d2c42;
            --shadow: 0 1px 2px rgba(0, 0, 0, .35), 0 10px 28px -18px rgba(0, 0, 0, .6);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font: 15px/1.55 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        :focus-visible { outline: 2px solid var(--focus); outline-offset: 2px; }

        code, pre, .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
        }

        .wrap { max-width: 1180px; margin: 0 auto; padding: 0 20px 72px; }

        /* Masthead ------------------------------------------------------- */

        .masthead {
            position: sticky;
            top: 0;
            z-index: 20;
            background: color-mix(in srgb, var(--bg) 86%, transparent);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .masthead-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 14px 20px 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px 20px;
            align-items: center;
            justify-content: space-between;
        }

        .masthead h1 { margin: 0; font-size: 21px; line-height: 1.2; letter-spacing: -0.02em; font-weight: 750; }
        .masthead .sub { margin: 2px 0 0; font-size: 12.5px; color: var(--ink-2); }
        .masthead .sub code { font-size: 12px; }

        .controls { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

        .seg { display: inline-flex; border: 1px solid var(--line); border-radius: 9px; overflow: hidden; background: var(--surface); }
        .seg button {
            font: 600 12px/1 system-ui, sans-serif;
            padding: 7px 11px;
            border: 0;
            border-right: 1px solid var(--line);
            background: transparent;
            color: var(--ink-2);
            cursor: pointer;
        }
        .seg button:last-child { border-right: 0; }
        .seg button:hover { color: var(--accent); }
        .seg button[aria-pressed="true"] { background: var(--accent); color: var(--accent-ink); }

        .btn {
            font: 600 12.5px/1 system-ui, sans-serif;
            padding: 8px 13px;
            border: 1px solid var(--line);
            border-radius: 9px;
            background: var(--surface);
            color: var(--ink);
            cursor: pointer;
            box-shadow: var(--shadow);
        }
        .btn:hover { border-color: var(--accent); color: var(--accent); }
        .btn:disabled { opacity: .55; cursor: default; }

        .chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 10px 0 12px; }

        .chip {
            font: 11.5px/1.4 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            padding: 4px 10px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--surface);
            color: var(--ink-2);
        }
        .chip.ok { color: var(--ok); border-color: color-mix(in srgb, var(--ok) 40%, var(--line)); background: color-mix(in srgb, var(--ok) 8%, var(--surface)); }
        .chip.warn { color: var(--warn); border-color: color-mix(in srgb, var(--warn) 40%, var(--line)); background: color-mix(in srgb, var(--warn) 8%, var(--surface)); }
        .chip.bad { color: var(--bad); border-color: color-mix(in srgb, var(--bad) 40%, var(--line)); background: color-mix(in srgb, var(--bad) 8%, var(--surface)); }

        /* Status chip: clickable, reports imgproxy's answer for one URL ---- */

        .status {
            appearance: none;
            cursor: pointer;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            border-radius: 999px;
            border: 1px dashed var(--line);
            background: transparent;
            color: var(--ink-2);
            padding: 3px 10px;
            font-size: 11.5px;
            line-height: 1.4;
        }
        .status:hover { border-color: var(--accent); color: var(--accent); }
        .status[data-state="busy"] { opacity: .6; cursor: progress; }
        .status[data-state="ok"] { color: var(--ok); border-color: color-mix(in srgb, var(--ok) 45%, var(--line)); background: color-mix(in srgb, var(--ok) 9%, var(--surface)); }
        .status[data-state="bad"] { color: var(--bad); border-color: color-mix(in srgb, var(--bad) 45%, var(--line)); background: color-mix(in srgb, var(--bad) 9%, var(--surface)); }
        .status[data-state="warn"] { color: var(--warn); border-color: color-mix(in srgb, var(--warn) 45%, var(--line)); background: color-mix(in srgb, var(--warn) 9%, var(--surface)); }

        /* Sections -------------------------------------------------------- */

        section { margin: 44px 0 0; }

        .eyebrow {
            margin: 0 0 6px;
            font: 600 11px/1.3 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--accent);
        }

        section > h2 { margin: 0 0 12px; font-size: 19px; line-height: 1.25; letter-spacing: -0.015em; font-weight: 700; }

        section > p, .lead { margin: 0 0 14px; max-width: 64ch; color: var(--ink-2); }
        section > p strong, .lead strong { color: var(--ink); }

        /* Notice (unconfigured) ------------------------------------------- */

        .notice {
            margin-top: 24px;
            border: 1px solid color-mix(in srgb, var(--warn) 45%, var(--line));
            background: color-mix(in srgb, var(--warn) 7%, var(--surface));
            border-radius: 12px;
            padding: 16px 18px;
        }
        .notice strong { color: var(--warn); }

        /* Recipes ---------------------------------------------------------- */

        .recipe { position: relative; border: 1px solid var(--line); border-radius: 9px; background: var(--surface); box-shadow: var(--shadow); }
        .recipe code {
            display: block;
            padding: 10px 46px 10px 12px;
            font-size: 12px;
            line-height: 1.55;
            overflow-wrap: anywhere;
        }

        .copy {
            position: absolute;
            top: 7px;
            right: 7px;
            font: 600 11px/1 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            padding: 5px 8px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--surface);
            color: var(--ink-2);
            cursor: pointer;
        }
        .copy:hover { color: var(--accent); border-color: var(--accent); }
        .copy:disabled { opacity: .7; }

        /* Prints ----------------------------------------------------------- */

        .print { display: block; max-width: 100%; height: auto; border-radius: 6px; box-shadow: var(--shadow); }

        .stage {
            aspect-ratio: 4 / 3;
            background: var(--stage);
            border: 1px solid var(--line);
            border-radius: 10px 10px 0 0;
            overflow: hidden;
        }
        .stage img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .stage--natural { aspect-ratio: auto; }
        .stage--natural img { width: 320px; max-width: 100%; height: auto; object-fit: contain; }

        body[data-mode="recipes"] .stage { display: none; }
        body[data-mode="recipes"] .demo-card { grid-template-columns: 1fr; }

        /* Demo cards -------------------------------------------------------- */

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 18px; }

        .demo-card {
            display: grid;
            grid-template-rows: auto auto auto 1fr;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--surface);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .demo-card .card-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px 0;
        }
        .demo-card h3 { margin: 0; font-size: 14px; font-weight: 650; line-height: 1.3; }
        .demo-card .card-body { padding: 10px 14px 14px; display: grid; gap: 10px; }
        .demo-card p { margin: 0; font-size: 12.5px; color: var(--ink-2); }
        .demo-card .note { color: var(--warn); font-size: 12px; }

        /* Presets config + lists ------------------------------------------- */

        .preset-config { margin: 4px 0 18px; }
        .preset-config .recipe code { padding: 12px 14px; }

        .two { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 18px; margin-top: 18px; }
        .panel { border: 1px solid var(--line); border-radius: 10px; background: var(--surface); padding: 16px; box-shadow: var(--shadow); }
        .panel h3 { margin: 0 0 4px; font-size: 14px; font-weight: 650; }
        .panel > p { margin: 0 0 12px; font-size: 12.5px; color: var(--ink-2); }
        .panel .print { margin-top: 12px; }

        details { margin-top: 12px; }
        summary { cursor: pointer; font-size: 12.5px; font-weight: 600; color: var(--accent); }
        details pre {
            margin: 10px 0 0;
            background: var(--term-bg);
            color: var(--term-ink);
            border: 1px solid var(--term-line);
            border-radius: 9px;
            padding: 12px 14px;
            font-size: 12px;
            line-height: 1.55;
            overflow-x: auto;
        }

        /* Storage ------------------------------------------------------------ */

        .kv { margin: 0; display: grid; gap: 16px; max-width: 860px; }
        .kv dt { font-weight: 650; font-size: 13.5px; margin-bottom: 4px; }
        .kv dd { margin: 0; display: grid; gap: 8px; }
        .kv dd .recipe { margin: 0; }

        /* Terminal ------------------------------------------------------------ */

        .term {
            margin: 0;
            background: var(--term-bg);
            color: var(--term-ink);
            border: 1px solid var(--term-line);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 12.5px;
            line-height: 1.6;
            overflow-x: auto;
            position: relative;
        }
        .term .copy { top: 8px; right: 8px; background: var(--term-bg); border-color: var(--term-line); color: var(--term-ink); }
        .term .copy:hover { color: var(--accent); border-color: var(--accent); }
        .term .dim { color: color-mix(in srgb, var(--term-ink) 62%, transparent); }

        /* Footer --------------------------------------------------------------- */

        footer {
            margin-top: 56px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
            font-size: 12px;
            color: var(--ink-2);
        }

        @media (max-width: 640px) {
            .masthead-inner { flex-direction: column; align-items: stretch; }
            .controls { justify-content: space-between; }
            section { margin-top: 36px; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body data-mode="prints">
<div class="masthead">
    <div class="masthead-inner">
        <div>
            <h1>Laravel imgproxy</h1>
            <p class="sub">Playground — live review surface for <code>imsus/laravel-imgproxy</code> 2.0.0.</p>
        </div>
        <div class="controls" role="group" aria-label="Playground controls">
            @if ($configured)
                <div class="seg" role="group" aria-label="View mode">
                    <button type="button" data-mode-btn="prints" aria-pressed="true">Prints</button>
                    <button type="button" data-mode-btn="recipes" aria-pressed="false">Recipes</button>
                </div>
                <button type="button" class="btn" id="check-all">Check all against imgproxy</button>
            @endif
            <div class="seg" role="group" aria-label="Theme">
                <button type="button" data-theme-btn="auto" aria-pressed="true">Auto</button>
                <button type="button" data-theme-btn="light" aria-pressed="false">Light</button>
                <button type="button" data-theme-btn="dark" aria-pressed="false">Dark</button>
            </div>
        </div>
    </div>
    <div class="chips" style="max-width:1180px;margin:0 auto">
        <span class="chip">instance: {{ $instance['name'] }}</span>
        <span class="chip">{{ $instance['url'] }}</span>
        @if ($instance['signed'])
            <span class="chip ok">signed · signature_size: {{ $instance['signature_size'] ?? 'full' }}</span>
        @else
            <span class="chip warn">unsigned · unsafe slot</span>
        @endif
        <span class="chip">encoding: {{ $instance['encoding'] }}</span>
        @if ($configured)
            <button type="button" class="status" data-check-url="{{ rtrim($instance['url'], '/') }}/health" data-state="idle" aria-live="polite">probe /health</button>
        @endif
    </div>
</div>

<div class="wrap">

    @if (! $configured)
        <div class="notice">
            <strong>No imgproxy instance configured.</strong>
            Copy <code>workbench/.env.example</code> to <code>workbench/.env</code> and point
            <code>IMGPROXY_URL</code>, <code>IMGPROXY_KEY</code>, and <code>IMGPROXY_SALT</code> at a local imgproxy.
            The defaults match the Docker imgproxy used by the integration tests:
            <pre class="term">docker run -d -p 8081:8080 \
  -e IMGPROXY_KEY=0000000000000000000000000000000000000000000000000000000000000000 \
  -e IMGPROXY_SALT=0000000000000000000000000000000000000000000000000000000000000000 \
  imgproxy/imgproxy</pre>
            Then restart the server with <code>composer serve</code>. The package works unsigned too — the
            <code>unsafe</code> slot is used when no key and salt are set.
        </div>
    @endif

    @if ($configured)
        <section aria-labelledby="source-title">
            <p class="eyebrow">Source</p>
            <h2 id="source-title">The film</h2>
            <p class="lead">All demos develop the same negative: the "Blue Marble" photograph (NASA / Apollo 17,
                public domain). The workbench serves it at <code>/sample/blue-marble.jpg</code> and imgproxy fetches
                it from there — no external image host, so nothing can be rate-limited.</p>
            <div class="recipe" style="margin-bottom:14px">
                <button type="button" class="copy" data-copy="{{ $source }}">Copy</button>
                <code>{{ $source }}</code>
            </div>
            <img class="print" src="{{ $source }}" alt="The demo source image, the Blue Marble photograph" style="max-width:100%;height:auto">
        </section>

        <section aria-labelledby="builder-title">
            <p class="eyebrow">{{ count($demos) }} recipes</p>
            <h2 id="builder-title">URL builder</h2>
            <p class="lead">Each print is one chain through the fluent builder. Click a status seal to ask imgproxy
                for that exact URL, or check them all at once.</p>
            <div class="grid">
                @foreach ($demos as $demo)
                    <article class="demo-card">
                        <div class="stage {{ str_contains($demo['label'], 'placeholder') ? 'stage--natural' : '' }}">
                            <img src="{{ $demo['url'] }}" loading="lazy" alt="{{ $demo['label'] }} — processed demo image">
                        </div>
                        <div class="card-head">
                            <h3>{{ $demo['label'] }}</h3>
                            <button type="button" class="status" data-check-url="{{ $demo['url'] }}" data-state="idle" aria-live="polite">not checked</button>
                        </div>
                        <div class="card-body">
                            <p>{{ $demo['description'] }}</p>
                            <div class="recipe">
                                <button type="button" class="copy" data-copy="{{ $demo['url'] }}">Copy</button>
                                <code>{{ $demo['url'] }}</code>
                            </div>
                            @if ($demo['note'])
                                <p class="note">{{ $demo['note'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section aria-labelledby="presets-title">
            <p class="eyebrow">Two mechanisms</p>
            <h2 id="presets-title">Presets</h2>
            <p class="lead">Do not confuse them: client-side presets are option sets this package composes into the
                URL — no server configuration. Server-side presets live on the imgproxy server
                (<code>IMGPROXY_PRESETS</code> / <code>IMGPROXY_PRESETS_PATH</code>) and are referenced with the
                <code>pr:</code> option; the server must know the name or it refuses the URL.</p>

            <div class="preset-config">
                <p class="lead" style="margin-bottom:8px">Defined in <code>workbench/config/laravel-imgproxy.php</code>:</p>
                <div class="recipe">
                    <code>'presets' =&gt; [
    'thumb' =&gt; ['resize' =&gt; 'fill', 'width' =&gt; 300, 'height' =&gt; 300],
    'hero'  =&gt; ['resize' =&gt; 'fill', 'width' =&gt; 1200, 'height' =&gt; 600, 'quality' =&gt; 85],
],</code>
                </div>
            </div>

            <div class="grid">
                @foreach ($presets['client'] as $preset)
                    <article class="demo-card">
                        <div class="stage">
                            <img src="{{ $preset['url'] }}" loading="lazy" alt="{{ $preset['label'] }} — processed demo image">
                        </div>
                        <div class="card-head">
                            <h3>{{ $preset['label'] }}</h3>
                            <button type="button" class="status" data-check-url="{{ $preset['url'] }}" data-state="idle" aria-live="polite">not checked</button>
                        </div>
                        <div class="card-body">
                            <p class="mono" style="font-size:11.5px;color:var(--ink-2)">{{ $preset['code'] }}</p>
                            <div class="recipe">
                                <button type="button" class="copy" data-copy="{{ $preset['url'] }}">Copy</button>
                                <code>{{ $preset['url'] }}</code>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="grid" style="margin-top:18px">
                <article class="demo-card">
                    <div class="stage">
                        @if ($presets['server']['status'] === 200)
                            <img src="{{ $presets['server']['url'] }}" loading="lazy" alt="Server-side preset demo image">
                        @else
                            <img src="{{ $source }}" loading="lazy" alt="Server-side preset demo image (server rejected the preset)">
                        @endif
                    </div>
                    <div class="card-head">
                        <h3>{{ $presets['server']['label'] }}</h3>
                        @if ($presets['server']['status'] === 200)
                            <span class="chip ok">HTTP 200 · registered</span>
                        @elseif ($presets['server']['status'] === null)
                            <span class="chip warn">imgproxy unreachable</span>
                        @else
                            <span class="chip bad">HTTP {{ $presets['server']['status'] }} · not registered</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p>References a preset that must exist on the imgproxy server. The local demo server has none
                            registered, so this is expected to fail until it does — the failure is the lesson.</p>
                        <p class="mono" style="font-size:11.5px;color:var(--ink-2)">{{ $presets['server']['code'] }}</p>
                        <div class="recipe">
                            <button type="button" class="copy" data-copy="{{ $presets['server']['url'] }}">Copy</button>
                            <code>{{ $presets['server']['url'] }}</code>
                        </div>
                        @if ($presets['server']['status'] !== null && $presets['server']['status'] !== 200)
                            <p class="note">Register it, e.g. <code>IMGPROXY_PRESETS=sharp=sharpen:0.7</code>, then
                                restart the container.</p>
                        @endif
                    </div>
                </article>
            </div>
        </section>

        <section aria-labelledby="components-title">
            <p class="eyebrow">Blade output</p>
            <h2 id="components-title">Components</h2>
            <p class="lead">Rendered by the package's components against the configured instance — inspect the
                generated markup, then the source that produced it.</p>
            <div class="two">
                <div class="panel">
                    <h3>&lt;x-imgproxy-img&gt;</h3>
                    <p>Width-based srcset with an LQIP placeholder in the src, lazy loading, and class passthrough.</p>
                    <x-imgproxy-img
                        src="{{ $source }}"
                        :widths="[320, 640, 1280]"
                        sizes="(min-width: 1024px) 50vw, 100vw"
                        preset="thumb"
                        placeholder
                        alt="Playground demo image"
                        class="print"
                    />
                    <details>
                        <summary>Blade source</summary>
                        @verbatim
<pre>&lt;x-imgproxy-img
    src="{{ $source }}"
    :widths="[320, 640, 1280]"
    sizes="(min-width: 1024px) 50vw, 100vw"
    preset="thumb"
    placeholder
    alt="Playground demo image"
    class="print"
/&gt;</pre>
                        @endverbatim
                    </details>
                </div>
                <div class="panel">
                    <h3>&lt;x-imgproxy-picture&gt;</h3>
                    <p>AVIF and WebP sources with a JPG fallback, each with its own srcset.</p>
                    <x-imgproxy-picture
                        src="{{ $source }}"
                        :widths="[640, 1280]"
                        :formats="['avif', 'webp', 'jpg']"
                        sizes="100vw"
                        alt="Playground demo image"
                        class="print"
                    />
                    <details>
                        <summary>Blade source</summary>
                        @verbatim
<pre>&lt;x-imgproxy-picture
    src="{{ $source }}"
    :widths="[640, 1280]"
    :formats="['avif', 'webp', 'jpg']"
    sizes="100vw"
    alt="Playground demo image"
    class="print"
/&gt;</pre>
                        @endverbatim
                    </details>
                </div>
            </div>
        </section>

        <section aria-labelledby="storage-title">
            <p class="eyebrow">Sources</p>
            <h2 id="storage-title">Storage</h2>
            <p class="lead">The <code>Storage::disk(...)-&gt;imgproxy($path)</code> macro: public disks yield the disk
                <code>url()</code>, private disks a pre-signed <code>temporaryUrl()</code>. The demo disks are wired
                locally with a fake signing callback — no cloud credentials — so only the URL shapes are shown.</p>
            <dl class="kv">
                <dt>Public disk</dt>
                <dd>
                    <div class="recipe"><code>Storage::disk('public')-&gt;imgproxy('sample.jpg')-&gt;url();</code></div>
                    <div class="recipe">
                        <button type="button" class="copy" data-copy="{{ $storage['public'] }}">Copy</button>
                        <code>{{ $storage['public'] }}</code>
                    </div>
                </dd>
                <dt>Private disk</dt>
                <dd>
                    <div class="recipe"><code>Storage::disk('s3')-&gt;imgproxy('sample.jpg')-&gt;url();</code></div>
                    <div class="recipe">
                        <button type="button" class="copy" data-copy="{{ $storage['private'] }}">Copy</button>
                        <code>{{ $storage['private'] }}</code>
                    </div>
                </dd>
            </dl>
        </section>
    @endif

    <section aria-labelledby="commands-title">
        <p class="eyebrow">CLI</p>
        <h2 id="commands-title">Artisan commands</h2>
        <p class="term" style="margin-top:4px">
            <button type="button" class="copy" data-copy="php artisan imgproxy:key
# IMGPROXY_KEY=...
# IMGPROXY_SALT=...

php artisan imgproxy:health
# The imgproxy instance [default] is healthy: HTTP 200.

php artisan imgproxy:health --instance=staging
# The imgproxy instance [staging] is healthy: HTTP 200.">Copy</button>
            <span>php artisan imgproxy:key</span>
            <br><span class="dim"># IMGPROXY_KEY=...</span>
            <br><span class="dim"># IMGPROXY_SALT=...</span>
            <br><br><span>php artisan imgproxy:health</span>
            <br><span class="dim"># The imgproxy instance [default] is healthy: HTTP 200.</span>
            <br><br><span>php artisan imgproxy:health --instance=staging</span>
            <br><span class="dim"># The imgproxy instance [staging] is healthy: HTTP 200.</span>
        </p>
    </section>

    <section aria-labelledby="validation-title">
        <p class="eyebrow">QA</p>
        <h2 id="validation-title">Validation</h2>
        <p class="term" style="margin-top:4px">
            <button type="button" class="copy" data-copy="composer test          # phpstan + pint + type coverage + pest
composer lint:check    # pint --test
composer analyse       # phpstan">Copy</button>
            <span>composer test</span> <span class="dim"># phpstan + pint + type coverage + pest</span>
            <br><span>composer lint:check</span> <span class="dim"># pint --test</span>
            <br><span>composer analyse</span> <span class="dim"># phpstan</span>
        </p>
        <p class="lead">Live integration tests against a real imgproxy run when <code>IMGPROXY_URL</code>,
            <code>IMGPROXY_KEY</code>, and <code>IMGPROXY_SALT</code> are exported in the shell running
            <code>composer test</code>; they skip otherwise.</p>
    </section>

    <footer>
        Source image: "The Blue Marble" (NASA / Apollo 17, public domain), served locally by the workbench app.
        Generated with <code>composer build</code> + <code>vendor/bin/testbench serve --host=0.0.0.0</code>.
    </footer>
</div>

<script>
    (function () {
        'use strict';

        var $ = function (s, c) { return (c || document).querySelector(s); };
        var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

        /* Theme: auto / light / dark. Attribute beats the media query. */
        var themeButtons = $$('[data-theme-btn]');
        function applyTheme(theme) {
            document.documentElement.dataset.theme = theme === 'auto' ? '' : theme;
            themeButtons.forEach(function (b) {
                b.setAttribute('aria-pressed', String(b.getAttribute('data-theme-btn') === theme));
            });
            try { localStorage.setItem('pg-theme', theme); } catch (e) { /* private mode */ }
        }
        applyTheme((function () {
            try { return localStorage.getItem('pg-theme') || 'auto'; } catch (e) { return 'auto'; }
        })());
        themeButtons.forEach(function (b) {
            b.addEventListener('click', function () { applyTheme(b.getAttribute('data-theme-btn')); });
        });

        /* Prints / recipes view mode. */
        var modeButtons = $$('[data-mode-btn]');
        function applyMode(mode) {
            document.body.dataset.mode = mode;
            modeButtons.forEach(function (b) {
                b.setAttribute('aria-pressed', String(b.getAttribute('data-mode-btn') === mode));
            });
            try { localStorage.setItem('pg-mode', mode); } catch (e) { /* private mode */ }
        }
        applyMode((function () {
            try { return localStorage.getItem('pg-mode') || 'prints'; } catch (e) { return 'prints'; }
        })());
        modeButtons.forEach(function (b) {
            b.addEventListener('click', function () { applyMode(b.getAttribute('data-mode-btn')); });
        });

        /* Copy recipe. */
        $$('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var text = btn.getAttribute('data-copy');
                var done = function () {
                    var label = btn.textContent;
                    btn.textContent = 'Copied';
                    btn.disabled = true;
                    setTimeout(function () { btn.textContent = label; btn.disabled = false; }, 1200);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text); done(); });
                } else {
                    fallbackCopy(text);
                    done();
                }
            });
        });
        function fallbackCopy(text) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) { /* ignore */ }
            document.body.removeChild(ta);
        }

        /* Status checks: per chip and "check all". Proxied through the
           workbench app, because imgproxy sends no CORS headers. */
        var chips = $$('[data-check-url]');
        var endpoint = '/playground/status?url=';

        function checkOne(chip) {
            var url = chip.getAttribute('data-check-url');
            chip.dataset.state = 'busy';
            chip.textContent = 'checking\u2026';
            chip.setAttribute('aria-busy', 'true');
            return fetch(endpoint + encodeURIComponent(url))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    var status = data && typeof data.status === 'number' ? data.status : null;
                    chip.removeAttribute('aria-busy');
                    if (status === null) {
                        chip.dataset.state = 'warn';
                        chip.textContent = 'unreachable';
                    } else if (status >= 200 && status < 300) {
                        chip.dataset.state = 'ok';
                        chip.textContent = 'HTTP ' + status;
                        healCardImage(chip);
                    } else {
                        chip.dataset.state = 'bad';
                        chip.textContent = 'HTTP ' + status;
                    }
                })
                .catch(function () {
                    chip.removeAttribute('aria-busy');
                    chip.dataset.state = 'warn';
                    chip.textContent = 'unreachable';
                });
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () { checkOne(chip); });
        });

        /* Self-healing images. The workbench serves the sample and proxies
           status checks, so a busy single-threaded dev server can make an
           image fail once while imgproxy recovers a moment later. Retry
           failed prints a few times, and when a status seal comes back 2xx,
           re-fire a broken print in the same card. */
        function retryImage(img) {
            var src = img.dataset.src || img.getAttribute('src');
            img.src = '';
            img.src = src;
        }

        $$('.stage img, .panel img, .print').forEach(function (img) {
            img.dataset.src = img.getAttribute('src');
            var attempts = 0;
            img.addEventListener('error', function handler() {
                attempts++;
                if (attempts > 3) { img.removeEventListener('error', handler); return; }
                setTimeout(function () { retryImage(img); }, attempts * 1200);
            });
        });

        function healCardImage(chip) {
            var card = chip.closest('.demo-card');
            if (!card) { return; }
            var img = card.querySelector('.stage img');
            if (img && !(img.complete && img.naturalWidth > 0)) { retryImage(img); }
        }

        var checkAll = $('#check-all');
        if (checkAll) {
            checkAll.addEventListener('click', function () {
                var pending = chips.filter(function (c) { return c.dataset.state !== 'ok'; });
                if (pending.length === 0) { return; }
                checkAll.disabled = true;
                checkAll.textContent = 'Checking\u2026';
                var index = 0;
                // The workbench dev server is multi-worker (PHP_CLI_SERVER_WORKERS);
                // keep the concurrent proxied checks below the worker count, or
                // every worker blocks on imgproxy while imgproxy waits on the
                // workbench for the sample image — a deadlock until timeouts.
                var concurrency = 2;
                function worker() {
                    if (index >= pending.length) { return Promise.resolve(); }
                    var chip = pending[index++];
                    return checkOne(chip).then(worker);
                }
                var workers = [];
                for (var i = 0; i < Math.min(concurrency, pending.length); i++) { workers.push(worker()); }
                Promise.all(workers).then(function () {
                    checkAll.disabled = false;
                    checkAll.textContent = 'Check all against imgproxy';
                });
            });
        }
    })();
</script>
</body>
</html>
