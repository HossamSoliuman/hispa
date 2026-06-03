<?php

namespace App\Http\Controllers;

use App\Models\IfeshItem;
use Illuminate\Http\Request;

class IfeshMarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = IfeshItem::with(['fish', 'auction', 'owner', 'bids'])
            ->where('status', 'available')
            ->whereHas('auction', function ($q) {
                $q->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            });

        // Apply filters
        if ($request->filled('fish_id')) {
            $query->where('fish_id', $request->fish_id);
        }

        if ($request->filled('min_price')) {
            $query->where('starting_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('starting_price', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('fish', function ($q) use ($search) {
                $q->where('local_name_primary', 'like', "%{$search}%")
                    ->orWhere('red_sea_name', 'like', "%{$search}%")
                    ->orWhere('arabian_gulf_name', 'like', "%{$search}%")
                    ->orWhere('english_name', 'like', "%{$search}%")
                    ->orWhere('scientific_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('starting_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('starting_price', 'desc');
                break;
            case 'ending_soon':
                $query->join('ifesh_auctions', 'ifesh_items.auction_id', '=', 'ifesh_auctions.id')
                    ->orderBy('ifesh_auctions.end_date', 'asc')
                    ->select('ifesh_items.*');
                break;
            default:
                $query->orderBy('ifesh_items.created_at', 'desc');
        }

        $items = $query->paginate(12);

        // Get all fish types for filter - order by local_name_primary
        $fishTypes = \App\Models\Fish::orderBy('local_name_primary')->get();

        // Additional Stats
        $totalBids = \App\Models\IfeshBid::count();
        $highestBid = \App\Models\IfeshItem::whereHas('auction', function ($q) {
            $q->where('status', 'active');
        })->max('current_bid') ?? 0;

        return view('ifesh-marketplace.index', compact('items', 'fishTypes', 'totalBids', 'highestBid'));
    }

    public function show($id)
    {
        $item = IfeshItem::with(['fish', 'auction', 'owner', 'bids.dalal'])
            ->findOrFail($id);

        return view('ifesh-marketplace.show', compact('item'));
    }
}
