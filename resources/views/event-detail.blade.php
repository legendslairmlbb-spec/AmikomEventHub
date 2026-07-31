@extends('layouts.app')

@section('title', 'Detail Event - ' . $event->title)

@section('content')
    <main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Left: Poster -->
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                <img src="{{ ($event->poster_path && Storage::disk('public')->exists($event->poster_path)) ? asset('storage/' . $event->poster_path) : 'https://placehold.co/400x600' }}" alt="{{ $event->title }} Poster"
                    class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white object-cover aspect-[3/4]">
                <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                    <h4 class="font-bold mb-4">Penyelenggara</h4>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                            AH</div>
                        <div>
                            <p class="font-bold text-slate-800">AmikomEventHub</p>
                            <p class="text-xs text-slate-500">Verified Organizer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Details -->
        <div class="lg:col-span-2 space-y-12">
            <div class="space-y-4">
                <span
                    class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">{{ $event->category->name }}</span>
                <h1 class="text-4xl md:text-5xl font-black leading-tight">{{ $event->title }}</h1>
                <div class="flex flex-wrap gap-6 text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($event->date)->format('l, d M Y H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            <div class="prose prose-slate max-w-none">
                <h3 class="text-2xl font-bold mb-4">Deskripsi Event</h3>
                <p class="text-lg text-slate-600 leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
            </div>

            <div
                class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                        @if($event->price == 0)
                            <h2 class="text-5xl font-black">Gratis <span class="text-lg font-medium text-indigo-200">/ orang</span></h2>
                        @else
                            <h2 class="text-5xl font-black">Rp {{ number_format($event->price, 0, ',', '.') }} <span class="text-lg font-medium text-indigo-200">/ orang</span></h2>
                        @endif
                        <p class="mt-4 text-indigo-100 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sisa stok: <span class="font-bold underline">{{ $event->stock }} Tiket lagi!</span>
                        </p>
                    </div>
                    <div>
                        @if($event->stock > 0)
                            <a href="{{ route('checkout.create', $event->id) }}"
                                class="inline-block px-10 py-5 bg-white text-indigo-600 rounded-2xl font-black text-xl hover:scale-105 transition-transform shadow-xl">
                                Pesan Sekarang
                            </a>
                        @else
                            <button disabled class="inline-block px-10 py-5 bg-slate-300 text-slate-500 rounded-2xl font-black text-xl shadow-xl cursor-not-allowed">
                                Tiket Habis
                            </button>
                        @endif
                    </div>
                </div>
                <!-- Decoration -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white opacity-10 rounded-full"></div>
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-400 opacity-20 rounded-full"></div>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-bold">Kebijakan Tiket</h3>
                <ul class="space-y-3 text-slate-500">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil.
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Tiket dapat discan di pintu masuk (Check-in).
                    </li>
                    <li class="flex items-start gap-2 text-rose-500">
                        <svg class="w-5 h-5 text-rose-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tiket yang sudah dibeli tidak dapat direfund.
                    </li>
                </ul>
            </div>
            
            <!-- Ulasan dan Penilaian (UAS) -->
            <div class="space-y-6 pt-10 border-t">
                <h3 class="text-2xl font-bold">Ulasan Pengunjung</h3>
                
                @if(session('review_success'))
                    <div class="p-4 bg-green-100 text-green-700 rounded-xl font-bold">
                        {{ session('review_success') }}
                    </div>
                @endif
                
                @auth
                    <!-- If user is logged in (admin), don't show review form -->
                    <p class="text-slate-500 italic">Admin tidak dapat meninggalkan ulasan.</p>
                @else
                    <!-- For simplicity of UAS, let's just make it a public form with name input, or use SSO later -->
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <h4 class="font-bold mb-4">Tinggalkan Ulasan</h4>
                        <form action="{{ route('reviews.store', $event->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium mb-1">Nama</label>
                                <input type="text" name="reviewer_name" required class="w-full p-3 rounded-xl border border-slate-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Rating (1-5)</label>
                                <select name="rating" class="w-full p-3 rounded-xl border border-slate-200">
                                    <option value="5">5 - Sangat Bagus</option>
                                    <option value="4">4 - Bagus</option>
                                    <option value="3">3 - Cukup</option>
                                    <option value="2">2 - Kurang</option>
                                    <option value="1">1 - Sangat Kurang</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Komentar</label>
                                <textarea name="comment" required rows="3" class="w-full p-3 rounded-xl border border-slate-200"></textarea>
                            </div>
                            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700">Kirim Ulasan</button>
                        </form>
                    </div>
                @endauth
                
                <!-- List Reviews -->
                <div class="space-y-4 mt-6">
                    @forelse($event->reviews()->latest()->get() as $review)
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <h5 class="font-bold">{{ $review->reviewer_name }}</h5>
                                <div class="flex text-yellow-400">
                                    @for($i = 0; $i < $review->rating; $i++)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-slate-600 text-sm">{{ $review->comment }}</p>
                            <p class="text-xs text-slate-400 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-slate-500 italic">Belum ada ulasan untuk acara ini.</p>
                    @endforelse
                </div>
            </div>
            
        </div>
    </main>
@endsection
