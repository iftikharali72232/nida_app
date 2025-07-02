<?php
// ReviewController
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Store a new review.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_id' => 'required|integer',
            'order_id'   => 'required|integer',
            'rating'      => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create($validatedData);

        return response()->json([
            'message' => 'Review created successfully!',
            'data'    => $review,
        ], 201);
    }
}
