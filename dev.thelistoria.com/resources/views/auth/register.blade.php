@extends('layouts.app') 

@section('title', 'Kayıt Ol | The Listoria')

@section('content')
    <div class="container mx-auto max-w-md py-12">
        <h2 class="text-3xl font-bold text-center mb-6">Hesap Oluştur</h2>

        <form method="POST" action="{{ url('/register') }}" class="bg-white p-8 rounded-lg shadow-md">
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

            {{-- Ad Soyad --}}
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Ad Soyad</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            {{-- Kullanıcı Adı --}}
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Kullanıcı Adı</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            {{-- E-posta --}}
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-posta Adresi</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            {{-- Şifre --}}
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Şifre</label>
                <input type="password" id="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            {{-- Şifre Tekrarı --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Şifre Tekrarı</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Kayıt Ol
                </button>
            </div>
            <p class="text-center text-gray-600 text-sm mt-4">
                Zaten hesabın var mı? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Giriş Yap</a>
            </p>
        </form>
    </div>
@endsection