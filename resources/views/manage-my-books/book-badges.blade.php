@extends('manage-my-books.index')

@section('badges-content')
    <div class="container my-5">
        @if($badges->isEmpty())
            {{-- Overlay com a mensagem e botão --}}
            <div class="favourites-container favourite-overlay">
                <h4 class="st-title">No Badges Found</h4>
                <p class="text-muted text-center">
                    You haven't completed any books yet to earn badges.
                </p>
                <a href="{{ route('home') }}" class="btn btn-warning text-white">Choose a Book</a>
            </div>
        @else
            <div class="d-flex flex-wrap justify-content-center gap-4 p-3">
                @foreach ($badges as $badge)
                    <div class="badge-scene" style="width: 150px; height: 150px; perspective: 600px;">
                        <div class="badge-card" style="width: 100%; height: 100%; position: relative; transition: transform 0.6s; transform-style: preserve-3d;">
                            {{-- Front --}}
                            <div class="badge-face badge-front" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <img src="{{ asset('storage/' . $badge->cover_url) }}"
                                     alt="{{ $badge->title }}"
                                     class="img-fluid" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                <div class="shine"></div>
                            </div>
                            
                            {{-- Back --}}
                            <div class="badge-face badge-back" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; background: var(--orange); transform: rotateY(180deg); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; color: white; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <small>Earned</small>
                                <div class="fw-bold" style="font-size: 0.9rem;">
                                    {{ $badge->pivot->created_at ? \Carbon\Carbon::parse($badge->pivot->created_at)->format('d/m/Y') : 'Completed' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
