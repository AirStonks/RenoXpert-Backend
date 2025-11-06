<?php

namespace App\Http\Controllers;

use App\Models\Operations\ProjectStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProjectStatusHistoryController extends BaseController
{
    /**
     * Display a listing of project status history with date range and optional snapshot type filter
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
            'size' => 'integer|min:1|max:1000',
            'snapshot_date_from' => 'nullable|date',
            'snapshot_date_to' => 'nullable|date|after_or_equal:snapshot_date_from',
            // 'snapshot_type' => 'nullable|string|in:weekly,monthly,daily',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        try {
            // Get validated parameters
            $page = $request->input('page', 1);
            $size = $request->input('size', 1000);
            // $snapshotType = $request->input('snapshot_type');
            $snapshotType = 'weekly';

            // If snapshot dates are not provided, use current month
            $snapshotDateFrom = $request->input('snapshot_date_from');
            $snapshotDateTo = $request->input('snapshot_date_to');

            if (!$snapshotDateFrom && !$snapshotDateTo) {
                $currentMonth = Carbon::now();
                $snapshotDateFrom = $currentMonth->startOfMonth()->format('Y-m-d');
                $snapshotDateTo = $currentMonth->endOfMonth()->format('Y-m-d');
            }

            // Build the query
            $query = ProjectStatusHistory::query()
                ->with(['renoProgress', 'property'])
                ->whereBetween('snapshot_date', [$snapshotDateFrom, $snapshotDateTo]);

            // Apply snapshot type filter if provided
            if ($snapshotType) {
                $query->where('snapshot_type', $snapshotType);
            }

            // Order by snapshot date descending
            $query->orderBy('snapshot_date', 'desc')
                ->orderBy('created_at', 'desc');

            // Get total count for pagination
            $totalCount = $query->count();

            // Apply pagination
            $projectStatusHistories = $query->paginate($size, ['*'], 'page', $page);

            // Transform the data
            $transformedData = $projectStatusHistories->map(function ($history) {
                return [
                    'id' => $history->id,
                    'reno_progress_id' => $history->reno_progress_id,
                    'property_id' => $history->property_id,
                    'unit' => $history->unit,
                    'status' => $history->status,
                    'payment_percentage' => $history->payment_percentage,
                    'snapshot_year' => $history->snapshot_year,
                    'snapshot_week' => $history->snapshot_week,
                    'snapshot_date' => $history->snapshot_date,
                    'snapshot_type' => $history->snapshot_type,
                    'created_at' => $history->created_at,
                    'updated_at' => $history->updated_at,
                    'reno_progress' => $history->renoProgress ? [
                        'id' => $history->renoProgress->id,
                        'status' => $history->renoProgress->status,
                        'contractual_start_date' => $history->renoProgress->contractual_start_date,
                        'contractual_end_date' => $history->renoProgress->contractual_end_date,
                    ] : null,
                    'property' => $history->property ? [
                        'id' => $history->property->id,
                        'name' => $history->property->name,
                        'address' => $history->property->address,
                        'street' => $history->property->street,
                        'postcode' => $history->property->postcode,
                        'city' => $history->property->city,
                        'state' => $history->property->state,
                    ] : null,
                ];
            });

            // Prepare response data
            $responseData = [
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $projectStatusHistories->currentPage(),
                    'per_page' => $projectStatusHistories->perPage(),
                    'total' => $totalCount,
                    'last_page' => $projectStatusHistories->lastPage(),
                    'from' => $projectStatusHistories->firstItem(),
                    'to' => $projectStatusHistories->lastItem(),
                ],
                'filters' => [
                    'snapshot_date_from' => $snapshotDateFrom,
                    'snapshot_date_to' => $snapshotDateTo,
                    'snapshot_type' => $snapshotType,
                ]
            ];

            return $this->sendResponse($responseData, 'Project status history retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }
}
