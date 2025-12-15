@extends('components.layout')
@extends('components.home-banner')

{{-- Menu --}}
<div class="home-main-content">
    <div class="home-menu text-center my-4">
        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-category="all">All Books</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="picks">Picks for You</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="popular">Most Popular</a>
            </li>
        </ul>
    </div>
</div>


<div class="container my-4">

    {{-- Combo box com os seletores de ordenação --}}
    <div class="filter text-left">
        <span style="display: inline;">Sort:</span>
        <select class="form-select w-auto d-inline-block" onchange="sortBooks(this.value)">
            <option value="asc" selected>A to Z</option>
            <option value="desc">Z to A</option>
        </select>
    </div>


    {{-- Conteúdo dinâmico --}}

    {{-- Exibe 'All books' e o componente de filtros--}}
    <div id="all-section" class="content-section hidden">
        {{-- Exibe o componente de filtros --}}
        @include('components.filter-component')

        <div class="row books-grid loading" id="books-list">
            {{-- Exibe os card dos livros --}}
            @foreach($books as $book)
                @include('components.book-component', ['book' => $book])
            @endforeach
        </div>
    </div>


    {{-- Exibe 'Picks for you' --}}
    <div id="picks-section" class="content-section hidden">
        <div class="row books-grid" id="picks-books-list">
            @foreach($recommendedBooks as $book)
                @include('components.book-component', ['book' => $book])
            @endforeach
        </div>
    </div>

    {{-- Exibe 'Most Popular' --}}
    <div id="popular-section" class="content-section hidden">
        <div class="row books-grid" id="popular-books-list">
            @foreach($popularBooks as $book)
                @include('components.book-component', ['book' => $book])
            @endforeach
        </div>
    </div>
</div>



