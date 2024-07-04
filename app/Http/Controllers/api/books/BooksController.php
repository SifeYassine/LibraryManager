<?php

namespace App\Http\Controllers\api\books;

use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class BooksController extends Controller
{
    //Create a new book
    public function create(Request $request)
    {
        try {
            $validateBook = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|unique:books',
                'published_year' => 'required|integer',
                'available_copies' => 'required|integer',
                'loan_fee' => 'required|numeric',
                'genre_id' => 'required|integer|exists:genres,id',
            ]);

            if ($validateBook->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateBook->errors()
                ], 401);
            }

            $book = Book::create([
                'title' => $request->title,
                'author' => $request->author,
                'isbn' => $request->isbn,
                'published_year' => $request->published_year,
                'available_copies' => $request->available_copies,
                'loan_fee' => $request->loan_fee,
                'genre_id' => $request->genre_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Book created successfully',
                'data' => $book
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all books
    public function index()
    {
        try {
            $query = Book::with('genre');

            // Search a book by its title, author, or genre
            if (request('search')) {
                $query->where('title', 'like', '%' . request('search') . '%')
                    ->orWhere('author', 'like', '%' . request('search') . '%')
                    ->orWhereHas('genre', function ($query) {
                        $query->where('name', 'like', '%' . request('search') . '%');
                    });
            }

            // Paginate the results
            $books = $query->paginate(5);

            // Map through each book and modify structure
            $books->transform(function ($book) {
                // Convert the books query result an array
                $bookArray = $book->toArray();

                // Modify the genre_id key
                $bookArray['genre_id'] = [
                    'id' => $book->genre->id,
                    'name' => $book->genre->name,
                ];
                // Remove the 'genre' key
                unset($bookArray['genre']);

                return $bookArray;
            });

            // Convert pagination result to array
            $keys = $books->toArray();

            // Remove unwanted keys
            unset(
                $keys['first_page_url'],
                $keys['from'],
                $keys['last_page_url'],
                $keys['links'],
                $keys['next_page_url'],
                $keys['path'],
                $keys['prev_page_url'],
                $keys['to']
            );

            return response()->json([
                'status' => true,
                'message' => 'All books',
                'data' => $keys
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //Get a single book
    public function show($id)
    {
        try {
            $book = Book::find($id);
            
            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found',
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Book details',
                'data' => $book
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //Update a book
    public function update(Request $request, $id)
    {
        try {
            $book = Book::find($id);

            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found',
                ], 404);
            }
            $validateBook = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|',
                'published_year' => 'required|integer',
                'available_copies' => 'required|integer',
                'loan_fee' => 'required|numeric',
                'genre_id' => 'required|integer|exists:genres,id',
            ]);

            if ($validateBook->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateBook->errors()
                ], 401);
            }

            $book->update([
                'title' => $request->title,
                'author' => $request->author,
                'isbn' => $request->isbn,
                'published_year' => $request->published_year,
                'available_copies' => $request->available_copies,
                'loan_fee' => $request->loan_fee,
                'genre_id' => $request->genre_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Book updated successfully',
                'data' => $book
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //Delete a book
    public function delete($id)
    {
        try {
            $book = Book::find($id);

            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found',
                ], 404);
            }

            $book->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Book deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
