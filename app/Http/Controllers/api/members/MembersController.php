<?php

namespace App\Http\Controllers\api\members;

use App\Models\Member;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class MembersController extends Controller
{
    // Create a new member
    public function create(Request $request) {
        try {
            $validateMember = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:members',
                'membership_date' => 'required|date',
                'membership_status' => 'required|string|max:255',
                'user_id' => 'required|integer|exists:users,id'
            ]);

            if ($validateMember->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateMember->errors()
                ], 401);
            }

            $member = Member::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'membership_date' => $request->membership_date,
                'membership_status' => $request->membership_status,
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Member created successfully',
                'member' => $member,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }
    
    // Get all members
    public function index() {
        try {
            $query = Member::with('user');

            // Paginate the results
            $members = $query->paginate(5);

            // Map through each member and modify structure
            $members->transform(function ($member) {
               // Convert the members query result to an array
               $memberArray = $member->toArray();

               // Modify the user_id key
               $memberArray['user_id'] = [
                   'id' => $member->user->id,
                   'name' => $member->user->name,
                   'email' => $member->user->email,
               ];

               // Remove the user key
               unset($memberArray['user']);

               return $memberArray;
            });

            // Convert pagination result to array
            $keys = $members->toArray();

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
                'message' => 'All members',
                'members' => $keys,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get a single member
    public function show($id) {
        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member not found',
                ], 404);
            } 

            return response()->json([
                'status' => true,
                'message' => 'Member',
                'member' => $member,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a member
    public function update(Request $request, $id)
    {
        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member not found',
                ], 404);
            }

            $validateMember = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|',
                'membership_date' => 'required|date',
                'membership_status' => 'required|string|max:255',
                'user_id' => 'required|integer|exists:users,id'
            ]);

            if ($validateMember->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateMember->errors()
                ], 401);
            }

            $member->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'membership_date' => $request->membership_date,
                'membership_status' => $request->membership_status,
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Member updated successfully',
                'member' => $member,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a member
    public function delete($id) {
        try {
            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member not found',
                ], 404);
            }

            $member->delete();
            return response()->json([
                'status' => true,
                'message' => 'Member deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
