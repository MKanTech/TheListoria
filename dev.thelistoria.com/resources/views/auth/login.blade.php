@extends('layouts.app') 

@section('title', 'Giriş Yap | The Listoria')

@section('content')
    <div class="container mx-auto max-w-md py-12">
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Giriş Yap</h2>

        <form method="POST" action="{{ url('/login') }}" class="bg-white p-8 rounded-lg shadow-md">
            @csrf
            
            {{-- Hata Mesajları --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kullanıcı Adı veya E-posta --}}
            <div class="mb-4">
                <label for="login_credential" class="block text-gray-700 text-sm font-bold mb-2">Kullanıcı Adı veya E-posta</label>
                <input type="text" id="login_credential" name="login_credential" value="{{ old('login_credential') }}" required autofocus class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            {{-- Şifre --}}
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Şifre</label>
                <input type="password" id="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Giriş Yap
                </button>
            </div>
            <p class="text-center text-gray-600 text-sm mt-4">
                Hesabın yok mu? <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Kayıt Ol</a>
            </p>
        </form>
    </div>
@endsection