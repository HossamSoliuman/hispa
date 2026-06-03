<?php

namespace App\Http\Controllers;

use App\Services\SupportServiceManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    protected $supportService;

    public function __construct(SupportServiceManager $supportService)
    {
        $this->supportService = $supportService;
    }

    /**
     * Get ticket priorities
     * Cache for 15 minutes to reduce API calls
     */
    public function getPriorities()
    {
        try {
            $priorities = Cache::remember('support_priorities', 900, function () {
                return $this->supportService->getPriorities();
            });

            return response()->json(['data' => $priorities]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch priorities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get ticket categories
     * Cache for 15 minutes to reduce API calls
     */
    public function getCategories()
    {
        try {
            $categories = Cache::remember('support_categories', 900, function () {
                return $this->supportService->getCategories();
            });

            return response()->json(['data' => $categories]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user tickets by phone and/or email
     * Cache for 2 minutes to reduce API calls while allowing fresh data
     */
    public function getUserTickets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:255',
            'mobile_no' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');
        $mobileNo = $request->input('mobile_no');

        if (! $email && ! $mobileNo) {
            return response()->json([
                'status' => 0,
                'message' => 'Email or mobile number is required',
                'data' => [],
            ], 400);
        }

        try {
            $cacheKey = 'user_tickets_'.md5($email.$mobileNo);

            $tickets = Cache::remember($cacheKey, 120, function () use ($email, $mobileNo) {
                // Build query parameters
                $queryParams = [];
                if ($email) {
                    $queryParams['email'] = $email;
                }
                if ($mobileNo) {
                    $queryParams['mobile_no'] = $mobileNo;
                }

                // Call the API via SupportServiceManager
                $response = $this->supportService->sendRequest('get', '/api/public/tickets/phone', [
                    'query' => $queryParams,
                ]);

                return $response->json();
            });

            // Return the tickets in the expected format
            return response()->json($tickets);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to fetch tickets',
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new support ticket
     */
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Prepare ticket data for API
            $ticketData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'subject' => $request->input('subject'),
                'message' => $request->input('message'),
            ];

            // Add optional fields if provided
            if ($request->filled('phone')) {
                $ticketData['phone'] = $request->input('phone');
            }

            if ($request->filled('priority_id')) {
                $ticketData['priority_id'] = $request->input('priority_id');
            }

            if ($request->filled('category_id')) {
                $ticketData['category_id'] = $request->input('category_id');
            }

            // If user is authenticated, add user_id
            if (auth()->check()) {
                $ticketData['user_id'] = auth()->id();
            }

            $result = $this->supportService->createTicket($ticketData);

            return response()->json([
                'is_success' => true,
                'message' => __('admin.swal.saved_success'),
                'data' => $result,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'API validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create ticket',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
