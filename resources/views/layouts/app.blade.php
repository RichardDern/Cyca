<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <base href="{{ env('APP_URL') }}" />
    <meta charset="utf-8" />

    <meta name="application-name" content="Cyca" />
    <meta name="author" content="Richard Dern" />
    <meta name="description" content="Bookmarks and feeds manager" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="icons-file-url" content="{{ asset('images/icons.svg') }}" />

    @auth
    <meta name="user-id" content="{{ auth()->user()->id }}" />
    <meta name="theme" content="{{ auth()->user()->theme }}" />
    @else
    <meta name="theme" content="auto" />
    @endauth

    <link rel="icon" href="{{ asset('images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" id="main-stylesheet" />

    <title>{{ config('app.name', 'Cyca') }}</title>

    <script>
        const lang = @json($langStrings);

        function setTheme() {
            if (
                localStorage.theme === "dark" ||
                (!("theme" in localStorage) &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches)
            ) {
                document.querySelector("html").classList.add("dark");
            } else {
                document.querySelector("html").classList.remove("dark");
            }
        }

        const theme = document
            .querySelector('meta[name="theme"]')
            .getAttribute("content");

        if (!theme || theme === "auto") {
            localStorage.removeItem("theme");
        } else {
            localStorage.theme = theme;
        }

        setTheme();

        window.matchMedia("(prefers-color-scheme: dark)").addListener(setTheme);
    </script>
    @auth
    <script>
        const highlights = @json($highlights);
    </script>
    @endauth

    @stack('scripts')
</head>

<body class="bg-white text-gray-500 dark:bg-gray-900 dark:text-gray-400">
    <main id="app" class="flex flex-row absolute top-0 left-0 right-0 bottom-0" role="main">
        @yield('main_content')
    </main>
    @auth
    <form id="logout_form" class="hidden" action="{{ route('logout') }}" method="POST">
        @csrf
    </form>
    @endauth
</body>

</html>