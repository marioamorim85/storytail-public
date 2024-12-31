@section('content')
    <div class="fixed-header">
        <div class="st-home-banner"></div>
        <div class="search-bar text-center my-4">
            <h2>Find a book</h2>
            <form action="{{ route('books.search') }}" method="GET" class="d-flex justify-content-center">
                <input type="text" name="query" class="form-control" placeholder="Book's Search">
                <input type="hidden" name="category" value="all">
                <button type="submit" class="btn btn-orange">
                    <i class="bi bi-search text-white bold-icon"></i>
                </button>
            </form>
        </div>
    </div>
    @yield('menu')
@endsection
