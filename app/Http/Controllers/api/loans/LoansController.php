<?php

namespace App\Http\Controllers\api\loans;

use App\Models\Loan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class LoansController extends Controller
{
    // Create a new loan (issue a book for a member)
    public function create(Request $request)
    {
        try {
            $validateLoan = Validator::make($request->all(), [
                'issued_date' => 'required|date',
                'due_date' => 'required|date|after:issued_date',
                'return_date' => 'nullable|date|after:issued_date',
                'is_returned' => 'required|boolean',
                'fine_amount' => 'required|numeric',
                'member_id' => 'required|integer|exists:members,id',
                'book_id' => 'required|integer|exists:books,id',
            ]);

            if ($validateLoan->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateLoan->errors()
                ], 401);
            }

            $loan = Loan::create([
                'issued_date' => $request->issued_date,
                'due_date' => $request->due_date,
                'return_date' => $request->return_date,
                'is_returned' => $request->is_returned,
                'fine_amount' => $request->fine_amount,
                'member_id' => $request->member_id,
                'book_id' => $request->book_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Loan created successfully',
                'data' => $loan
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all loans
    public function index()
    {
        try {
            $loans = Loan::with(['member', 'book'])->get();

            // Map through each loan and modify structure
            $loans->transform(function ($loan) {
                // Convert the loans query result an array
                $loanArray = $loan->toArray();

                // Modify the member_id and book_id keys
                $loanArray['member_id'] = [
                    'id' => $loan->member->id,
                    'full_name' => $loan->member->full_name,
                    'email' => $loan->member->email,
                ];

                $loanArray['book_id'] = [
                    'id' => $loan->book->id,
                    'title' => $loan->book->title,
                    'author' => $loan->book->author,
                    'loan_fee' => $loan->book->loan_fee
                ];

                // Remove the 'member' and 'book' objects from the loan
                unset($loanArray['member'], $loanArray['book']);

                return $loanArray;
            });

            return response()->json([
                'status' => true,
                'message' => 'All loans',
                'data' => $loans
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Return a loan (a member returns a book)
    public function return(Request $request, $id) {
        try {
            $loan = Loan::find($id);

            if (!$loan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan not found'
                ], 404);
            }

            // Validate the return_date
            $validateLoan = validator::make($request->all(), [
                'return_date' => 'required|date|after_or_equal:issued_date'
            ]);

            if ($validateLoan->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateLoan->errors()
                ], 400);
            }

            $loan->return_date = Carbon::parse($request->return_date);
            $loan->is_returned = 1;
            // Calculate fine amount
            $this->calculateFine($loan);
            $loan->save();

            return response()->json([
                'status' => true,
                'message' => 'Loan returned successfully',
                'data' => $loan
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Calculate fine amount
    public function calculateFine(Loan $loan) {
        // Define the loan fee
        $loanFee = $loan->book->loan_fee;
        // Calculate the number of overdue days
        $dueDate = Carbon::parse($loan->due_date);
        $returnDate = Carbon::parse($loan->return_date);
        $overdueDays = $dueDate->diffInDays($returnDate, false); // false to include negative values

        // Fine is only applicable if there are overdue days
        if ($overdueDays > 0) {
            $loan->fine_amount = $overdueDays * $loanFee;
        } else {
            $loan->fine_amount = 0;
        }
    }
}
