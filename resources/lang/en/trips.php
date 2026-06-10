<?php

return [
    'statuses' => [
        '1' => 'New Trip',
        '2' => 'Ongoing Trip',
        '3' => 'Cancelled',
        '4' => 'Completed - Waiting Count',
        '5' => 'Counting in Progress',
        '6' => 'Completed Count Waiting for Sale',
        '7' => 'Ready for Sale',
        '8' => 'Completed',
    ],

    'status_keys' => [
        '1' => 'new',
        '2' => 'in_progress',
        '3' => 'cancelled',
        '4' => 'captain_done',
        '5' => 'progress_count',
        '6' => 'counter_done',
        '7' => 'ready_to_sell',
        '8' => 'completed',
    ],

    'status_labels' => [
        'new' => 'New Trip',
        'in_progress' => 'Ongoing',
        'cancelled' => 'Cancelled',
        'captain_done' => 'Completed by Captain',
        'progress_count' => 'Counting in Progress',
        'counter_done' => 'Count Completed',
        'ready_to_sell' => 'Ready for Sale',
        'completed' => 'Successfully Completed',
    ],

    'roles' => [
        'captain' => [
            'new' => 'New Trip Waiting for You',
            'in_progress' => 'Ongoing Trip',
            'cancelled' => 'Cancelled',
            'captain_done' => 'Completed - Waiting Count',
            'progress_count' => 'Counting in Progress',
            'counter_done' => 'Completed Count Waiting for Sale',
            'ready_to_sell' => 'Ready for Sale',
            'completed' => 'Completed',
        ],
        'counter' => [
            'new' => 'New Trip (Not Assigned to You)',
            'in_progress' => 'Ongoing Trip (Not Assigned to You)',
            'cancelled' => 'Cancelled',
            'captain_done' => 'Completed by Captain - Waiting Count',
            'progress_count' => 'Counting in Progress',
            'counter_done' => 'Count Completed',
            'ready_to_sell' => 'Count Completed - Ready for Sale',
            'completed' => 'Successfully Sold',
        ],
        'owner' => [
            'new' => 'New Trip',
            'in_progress' => 'Ongoing Trip',
            'cancelled' => 'Cancelled',
            'captain_done' => 'Completed by Captain',
            'progress_count' => 'Ready for Counting',
            'counter_done' => 'Count Completed Waiting for Sale',
            'ready_to_sell' => 'Selling in Progress',
            'completed' => 'All Sold',
        ],
        'broker' => [
            'new' => 'New Trip',
            'in_progress' => 'Ongoing Trip',
            'cancelled' => 'Cancelled',
            'captain_done' => 'Completed',
            'progress_count' => 'Ready for Counting',
            'counter_done' => 'Count Completed Waiting for Sale',
            'ready_to_sell' => 'Ready for Sale',
            'completed' => 'Successfully Sold',
        ],
    ],

    'actions' => [
        'start_trip' => 'Start Trip',
        'finish_trip' => 'Finish Trip',
        'start_counting' => 'Start Counting',
        'finish_counting' => 'Finish Counting',
        'mark_ready' => 'Mark Ready to Sell',
        'mark_sold' => 'Mark as Sold',
        'cancel_trip' => 'Cancel Trip',
    ],

    'errors' => [
        'trip_terminal' => 'Cannot change status of a completed or cancelled trip.',
        'invalid_transition' => 'This transition is not allowed from the current status.',
        'cancel_reason_required' => 'A cancellation reason is required.',
        'catch_required' => 'Catch data must be added before starting counting.',
    ],

    'duration' => [
        'day_singular' => 'Day',
        'day_plural' => 'Days',
    ],
    'choose_permit_type' => 'Choose Permit Type',
    'permit_types' => [
        'artisanal' => 'Artisanal Fishing (Individual)',
        'commercial' => 'Commercial Fishing (Large Boat)',
        'leisure' => 'Leisure Boat (Private)',
        'fishing' => 'Fishing Boat',
        'transport' => 'Maritime Transport (Goods/People)',
        'aquaculture' => 'Aquaculture (Fish/Shrimp Farm)',
        'exploration' => 'Marine Exploration',
        'diving' => 'Marine Diving (Non-Commercial)',
    ],

];
