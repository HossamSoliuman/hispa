<?php

return [
    'getData' => 'Data retrieved successfully',
    'unauthorized_trip' => 'You are not authorized to add data to this trip',
    'invalid_trip_status_add' => 'Cannot add fish data at this stage of the trip',
    'duplicate_fish' => 'This fish has already been entered for this trip',
    'fish_added' => 'Fish stock added successfully',
    'fish_not_found' => 'Fish stock not found',
    'trip_not_found' => 'Trip not found',
    'invalid_trip_status_update' => 'Cannot update fish data at this stage of the trip',
    'unauthorized_update' => 'You are not authorized to update this fish stock',
    'invalid_counter_status' => 'Cannot update fish data after counting is completed',
    'no_permission_role' => 'Your role is not authorized to update this fish stock',
    'fish_updated' => 'Fish stock updated successfully',
    'unauthorized_delete' => 'You are not authorized to delete this fish stock',
    'fish_deleted' => 'Fish stock deleted successfully',

    // General
    'unknown' => 'Unknown',
    'status_completed' => 'Completed',
    'no_notes' => 'No notes',

    // Auth/Access
    'unauthorized_view' => 'You are not authorized to view this data',
    'unauthorized_create' => 'You are not authorized to create a sale',
    'unauthorized_update_sale' => 'You are not authorized to update this sale',
    'unauthorized_delete_data' => 'You are not authorized to delete data',
    'not_authorized_delete_invoice' => 'You do not have permission to delete this invoice',

    // Lists/Fetching
    'no_sales_for_stock' => 'No sales found for this stock',
    'invoices_fetched' => 'Invoices fetched successfully',
    'details_fetched' => 'Invoice details fetched successfully',

    // Invoices
    'invoice_not_found_or_forbidden' => 'Invoice not found or you do not have permission to edit it',
    'invoice_not_found_or_not_yours' => 'Invoice not found or it does not belong to you',
    'invoice_already_closed' => 'This invoice has already been closed and cannot be modified',
    'invoice_no_items' => 'Invoice cannot be closed because it has no items',
    'invoice_closed_successfully' => 'Invoice closed successfully',
    'invoice_id_required' => 'Invoice ID is required',
    'cannot_delete_completed_invoice' => 'You cannot delete from this invoice. The invoice is completed',
    'invoice_has_items' => 'The invoice cannot be deleted because it contains items',
    'invoice_deleted' => 'Invoice deleted successfully',

    // Settings/Refs
    'no_commission_setting' => 'No commission setting found, please contact the admin',
    'customer_not_found' => 'Customer not found',
    'payment_method_not_found' => 'Payment method not found',

    // Stock/Items
    'stock_not_found_for_fish' => 'No available stock for fish: :fish in the selected record.',
    'fish_already_added' => 'This fish has already been added to the same invoice: :fish',
    'requested_exceeds_stock' => 'Requested quantity/weight exceeds available stock for fish: :fish in this record.',
    'stock_detail_missing' => 'Linked stock record was not found.',
    'new_requested_exceeds_stock' => 'New requested quantity/weight exceeds available stock.',
    'dalal_stock_not_found' => 'Dalal stock not found.',
    'invoice_details_not_linked_to_dalal_stock' => 'Invoice details are not linked to a Dalal stock.',

    // Item operations
    'item_added' => 'Item successfully added to invoice',
    'item_updated' => 'Item updated successfully',
    'item_deleted_updated_stock' => 'Item deleted successfully and stock updated',

    'stocks_fetched' => 'Stocks fetched successfully',
    'stock_not_found' => 'Stock not found or does not belong to this user',
    'stock_details_fetched' => 'Stock details fetched successfully',

    // Success messages
    'list_success' => 'Dalal stocks fetched successfully',
    'status_updated' => 'Stock status updated successfully.',
    'detail_fetched' => 'Dalal stock details fetched successfully',
    'item_added' => 'Item added successfully and quantity deducted from owner stock.',
    'item_updated' => 'Fish item data updated successfully.',
    'item_deleted' => 'Fish item deleted successfully and quantity restored to original stock.',

    // Failure messages
    'stock_not_found' => 'Dalal stock not found.',
    'cannot_set_status' => 'Cannot change status to 1 without adding fish quantities.',
    'insufficient_stock' => 'Insufficient quantity in owner stock for this item.',
    'item_exists' => 'This item already exists for this Dalal in this trip. Current weight: :weight kg',
    'not_owner' => 'Your role does not have permission to modify this item.',
    'detail_not_found' => 'Item details not found.',
    'cannot_edit_item' => 'You cannot edit an item that does not belong to you.',
    'fishstock_not_found' => 'No stock available for this item in owner stock.',
    'insufficient_stock_update' => 'Insufficient owner stock to update weight.',
    'cannot_delete_item' => 'You cannot delete an item that does not belong to you.',

    // General errors
    'error_saving' => 'An error occurred while saving: :error',
    'error_updating' => 'An error occurred while updating: :error',
    'error_deleting' => 'An error occurred while deleting: :error',
    'captain_has_trips' => 'Cannot delete the captain because they are linked to trips. Please delete or reassign their trips first.',

    // Success messages
    'list_sale_success' => 'Sales fetched successfully',
    'sale_completed' => 'Sale completed successfully',
    'detail_fetched' => 'Sale details fetched successfully',
    'item_added' => 'Item added successfully to the sale',
    'item_updated' => 'Item data updated successfully',
    'sale_deleted' => 'Sale deleted successfully',
    'detail_deleted' => 'Item deleted successfully',

    // Failure messages
    'unauthorized_list' => 'You are not authorized to view sales',
    'sale_not_found' => 'Sale not found',
    'sale_already_completed' => 'This sale is already completed and cannot be modified',
    'empty_sale' => 'Cannot complete sale because it has no items',
    'unauthorized_view' => 'You are not authorized to view this sale',
    'unauthorized_add' => 'You are not authorized to create a sale',
    'trip_not_found' => 'Trip not found',
    'trip_completed' => 'Cannot add. Trip is already completed',
    'customer_not_found' => 'Customer not found',
    'payment_method_not_found' => 'Payment method not found',
    'item_exists' => 'This fish has already been added in the same sale: :fish',
    'insufficient_stock' => 'Requested weight (:weight kg) + previously sold (:already kg) exceeds available stock (:stock kg) for fish: :fish',
    'unauthorized_edit' => 'You are not authorized to edit this sale',
    'item_not_found' => 'Item not found',
    'unauthorized_delete' => 'You do not have permission to delete this sale',
    'sale_has_items' => 'Cannot delete sale because it contains items',
    'cannot_delete_detail' => 'Cannot delete from this sale. Sale is completed',
    'trip_completed_delete' => 'Cannot delete. Trip is already completed',

    // Employee
    'employee_added' => 'Employee added successfully',
    'employee_updated' => 'Employee updated successfully',
    'employee_deleted' => 'Employee deleted successfully',
    'employee_not_found' => 'Employee not found',
];
