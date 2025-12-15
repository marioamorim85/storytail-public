@extends('manage-my-books.index')

@section('badges-content')
    <div class="container my-5">
        @if($badges->isEmpty())
            {{-- Empty State com ilustração --}}
            <div class="empty-state">
                <i class="bi bi-puzzle empty-state-icon"></i>
                <h4 class="empty-state-title">No Activity Badges Found</h4>
                <p class="empty-state-description">
                    You haven't completed any activities yet. Complete activities in books to earn badges!
                </p>
                <a href="{{ route('home') }}" class="btn btn-orange text-white">
                    <i class="bi bi-book me-2"></i>Find Activities
                </a>
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

