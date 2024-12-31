<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function show($id, Request $request)
    {
        try {
            $author = Author::with(['books' => function($query) use ($request) {
                $query->where('is_active', true)
                    ->where('id', '!=', $request->book_id);
            }])->findOrFail($id);

            return view('book-details.book-author-about', compact('author'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Author not found.');
        }
    }
}
