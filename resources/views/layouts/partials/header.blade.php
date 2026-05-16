<header class="app-header">
    <div class="app-header__inner">
        <div class="app-header__brand">
            @auth
                <span class="app-header__user">{{ Auth::user()->name }}</span>
            @else
                <a href="{{ route('login') }}" class="app-header__user hover:text-gray-200">
                    {{ config('app.name', 'Laravel') }}
                </a>
            @endauth
        </div>

        <nav class="app-header__nav">
            @auth
                <a
                    href="{{ route('tasks.index') }}"
                    @class(['app-nav-link', 'app-nav-link--active' => request()->routeIs('tasks.*')])
                >
                    タスク一覧
                </a>
                <a
                    href="{{ route('profile.edit') }}"
                    @class(['app-nav-link', 'app-nav-link--active' => request()->routeIs('profile.*')])
                >
                    プロフィール
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="app-header__logout">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="app-nav-link">ログイン</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="app-nav-link">新規登録</a>
                @endif
            @endauth
        </nav>
    </div>

    @if (! empty($header) && auth()->check())
        <div class="app-header__subtitle">
            <div class="app-header__subtitle-inner">
                <h1 class="app-header__title">{{ $header }}</h1>
                @if (request()->routeIs('tasks.index'))
                    <a href="{{ route('tasks.create') }}" class="app-btn--primary shrink-0">新規作成</a>
                @endif
            </div>
        </div>
    @endif
</header>
