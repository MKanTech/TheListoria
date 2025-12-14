@extends('layouts.app') 

@section('title', 'Anasayfa | The Listoria')

@section('content')
    <div class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-6">Hoş Geldiniz!</h2>
        
        @auth
            <p class="text-lg mb-4">Merhaba, {{ Auth::user()->name }}! Burası sizin kişisel akış sayfanız olacak.</p>
        @else
            <p class="text-lg mb-4">Burada kullanıcıların herkese açık listelerine eklediği içerikler gözükecek.</p>
            <p class="text-gray-600">Başlamak için lütfen <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Giriş Yap</a> veya <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Kayıt Ol</a>.</p>
        @endauth

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            {{-- Bu kısma gelecekte herkese açık gönderiler/içerik kutucukları gelecek --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Örnek Gönderi Akışı</h3>
                <p>Anasayfada listelerden gelen içerikler burada listelenecek.</p>
            </div>
        </div>
    </div>
@endsection