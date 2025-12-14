<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'The Listoria')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body>

    {{-- HEADER KISMI --}}
    <header class="bg-white shadow-md">
        <div class="container flex justify-between items-center py-4">
            {{-- Sol: Logo --}}
            <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-700">The Listoria</a>

            {{-- Sağ: Giriş/Kayıt veya Kullanıcı Bilgileri --}}
            <nav>
                @auth
                    {{-- Giriş yaptıysa --}}
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/profile/'.Auth::user()->username) }}" class="flex items-center space-x-2">
                            <img src="{{ Auth::user()->profile_image_url ?? 'varsayilan_profil.png' }}" alt="Profil" class="w-8 h-8 rounded-full border">
                            <span class="font-semibold text-gray-700">{{ Auth::user()->username }}</span>
                        </a>
                        
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Çıkış Yap</button>
                        </form>
                    </div>
                @else
                    {{-- Giriş yapmadıysa --}}
                    <a href="{{ route('login') }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 mr-2">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="text-indigo-600 border border-indigo-600 px-4 py-2 rounded hover:bg-indigo-50">Kayıt Ol</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- SAYFA İÇERİĞİ --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- FOOTER KISMI --}}
    <footer class="bg-gray-800 text-white p-4 text-center mt-8">
        © {{ date('Y') }} The Listoria. Tüm hakları saklıdır.
    </footer>
    @yield('scripts')
</body>
</html>