<?php

namespace App\Http\Controllers\api\reservations;

use App\Models\Reservation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ReservationsController extends Controller
{
    // Reserve a book for a member
    public function create(Request $request)
    {
        try {
            $validateReservation = Validator::make($request->all(), [
                'reservation_date' => 'required|date',
                'notification_sent' => 'required|boolean',
                'member_id' => 'required|exists:members,id',
                'book_id' => 'required|exists:books,id',
            ]);

            if ($validateReservation->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateLoan->errors()
                ], 401);
            }

            $reservation = Reservation::create([
                'reservation_date' => $request->reservation_date,
                'notification_sent' => $request->notification_sent,
                'member_id' => $request->member_id,
                'book_id' => $request->book_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Reservation created successfully',
                'data' => $reservation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get all reservations
    public function index()
    {
        try {
            $reservations = Reservation::with('member', 'book')->get();

            // Map through each reservation and modify structure
            $reservations->transform(function ($reservation) {
                // Convert the reservations query result an array
                $reservationArray = $reservation->toArray();

                // Modify the member_id and book_id keys
                $reservationArray['member_id'] = [
                    'id' => $reservation->member->id,
                    'full_name' => $reservation->member->full_name,
                    'email' => $reservation->member->email,
                ];

                $reservationArray['book_id'] = [
                    'id' => $reservation->book->id,
                    'title' => $reservation->book->title,
                    'author' => $reservation->book->author,
                    'loan_fee' => $reservation->book->loan_fee
                ];

                // Remove the 'member' and 'book' objects from the reservation
                unset($reservationArray['member'], $reservationArray['book']);

                return $reservationArray;
            });

            return response()->json([
                'status' => true,
                'message' => 'Reservations retrieved successfully',
                'data' => $reservations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Notify member about reservation
    public function notify(Request $request, $id)
    {
        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            $reservation->notification_sent = 1;
            $reservation->save();

            return response()->json([
                'status' => true,
                'message' => 'Notification sent successfully',
                'data' => $reservation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
