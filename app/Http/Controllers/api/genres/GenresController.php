<?php

namespace App\Http\Controllers\api\genres;

use App\Models\Genre;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class GenresController extends Controller
{
    // Create a new genre
    public function create(Request $request)
    {
        try {
            $validateGenre = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:genres',
            ]);

            if ($validateGenre->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateGenre->errors()
                ], 401);
            }

            $genre = Genre::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Genre created successfully',
                'genre' => $genre,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all genres
    public function index()
    {
        try {
            $genres = Genre::query()->paginate(3);
            
            // Convert pagination result to array
            $keys = $genres->toArray();

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
                'message' => 'All genres',
                'genres' => $keys
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a genre
    public function update(Request $request, $id)
    {
        try {
            $genre = Genre::find($id);

            if (!$genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre not found',
                ], 404);
            }

            $validateGenre = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:genres,name,',
            ]);

            if ($validateGenre->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateGenre->errors()
                ], 401);
            }

            $genre->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Genre updated successfully',
                'genre' => $genre,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a genre
    public function delete($id)
    {
        try {
            $genre = Genre::find($id);

            if (!$genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre not found',
                ], 404);
            }

            $genre->delete();
            return response()->json([
                'status' => true,
                'message' => 'Genre deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
