{{-- Player de Vídeo do YouTube --}}
<div class="book-video-container">
    @if($book->video && $book->video->video_url)
        <iframe
            class="book-video"
            src="https://www.youtube.com/embed/{{ $book->getYoutubeId($book->video->video_url) }}"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    @else
        <div class="text-center p-4">
            <p>No video available for this book.</p>
        </div>
    @endif
</div>
