@extends('manage-my-books.index')

@section('badges-content')
    <div class="container my-5">
        @if($badges->isEmpty())
            {{-- Overlay com a mensagem e botão --}}
            <div class="favourites-container favourite-overlay">
                <h4 class="st-title">No Badges Found</h4>
                <p class="text-muted text-center">
                    You haven't completed any activities yet to earn badges.
                </p>
                <a href="{{ route('home') }}" class="btn btn-warning text-white">Choose a Book</a>
            </div>
        @else
            <div class="badges-container d-flex flex-wrap p-3">
                @foreach($badges as $activity)
                    <div class="badge-container">
                        <div class="badge-item">
                            @if($activity->activityImages->count() > 0)
                                <img src="{{ Storage::url($activity->activityImages->first()->image_url) }}"
                                     alt="{{ $activity->title }}"
                                     onerror="this.src='{{ asset('images/no-image.png') }}'"
                                     class="badge-img"
                            @else
                                <img src="{{ asset('images/no-image.png') }}"
                                     alt="{{ $activity->title }}"
                                     class="badge-img"
                            @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

