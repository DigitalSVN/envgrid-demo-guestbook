<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EnvGrid Guestbook</title>
    <style>
        :root {
            --header-bg: #b45309;
            --header-fg: #ffffff;
            --page-bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --error: #b91c1c;
            --success-bg: #dcfce7;
            --success-fg: #166534;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--page-bg);
            color: var(--text);
            line-height: 1.5;
        }
        header {
            background: var(--header-bg);
            color: var(--header-fg);
            padding: 2.5rem 1rem;
            text-align: center;
        }
        header h1 { margin: 0; font-size: 2rem; }
        header p { margin: 0.5rem 0 0; }
        main { max-width: 40rem; margin: 0 auto; padding: 2rem 1rem; }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .card h2 { margin: 0 0 0.5rem; font-size: 1.25rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.25rem; }
        input, textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font: inherit;
            margin-bottom: 1rem;
        }
        textarea { min-height: 6rem; resize: vertical; }
        button {
            background: var(--header-bg);
            color: var(--header-fg);
            border: 0;
            border-radius: 0.5rem;
            padding: 0.65rem 1.25rem;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }
        .error { color: var(--error); font-size: 0.875rem; margin: -0.75rem 0 1rem; }
        .status {
            background: var(--success-bg);
            color: var(--success-fg);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
        .entries { list-style: none; margin: 0; padding: 0; }
        .entry { border-bottom: 1px solid var(--border); padding: 1rem 0; }
        .entry:last-child { border-bottom: 0; }
        .entry-meta { color: var(--muted); font-size: 0.875rem; }
        .entry-message { margin: 0.25rem 0 0; white-space: pre-line; overflow-wrap: anywhere; }
        .empty { color: var(--muted); }
        footer { text-align: center; color: var(--muted); font-size: 0.875rem; padding: 0 1rem 2rem; }
        footer a { color: inherit; }
    </style>
</head>
<body>
    <header>
        <h1>Sign the guestbook</h1>
        <p>A live EnvGrid demo: every pull request gets its own preview, with its own database.</p>
    </header>

    <main>
        @if (session('status'))
            <div class="status" role="status">{{ session('status') }}</div>
        @endif

        <section class="card">
            <form method="POST" action="{{ route('guestbook.store') }}">
                @csrf

                <label for="name">Name</label>
                <input id="name" name="name" type="text" maxlength="100" value="{{ old('name') }}" required>
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror

                <label for="message">Message</label>
                <textarea id="message" name="message" maxlength="1000" required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="error">{{ $message }}</p>
                @enderror

                <button type="submit">Sign the guestbook</button>
            </form>
        </section>

        <section class="card">
            <h2>Recent entries</h2>

            @if ($entries->isEmpty())
                <p class="empty">No entries yet. Be the first to sign!</p>
            @else
                <ul class="entries">
                    @foreach ($entries as $entry)
                        <li class="entry">
                            <div class="entry-meta">
                                <strong>{{ $entry->name }}</strong>
                                &middot;
                                <time datetime="{{ $entry->created_at->toIso8601String() }}">{{ $entry->created_at->diffForHumans() }}</time>
                            </div>
                            <p class="entry-message">{{ $entry->message }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </main>

    <footer>
        Previewed with <a href="https://www.envgrid.com">EnvGrid</a>
    </footer>
</body>
</html>
