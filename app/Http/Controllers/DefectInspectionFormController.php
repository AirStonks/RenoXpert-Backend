<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Support\Str;
use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\DefectInspectionForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\DefectInspectionFormResource;
use App\Http\Resources\DefectInspectionFormResourceHead;

class DefectInspectionFormController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $size = $request->input('size', 10);
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'id'); // Default to 'id' instead of ''
        $sortOrder = $request->input('sortOrder', 'desc'); // Default to 'desc'
        $filters = $request->input('filter', []); // Get all filter parameters

        // Build the query to retrieve product categories
        $query = DefectInspectionForm::query();

        // Filter by status if available
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter out with 'status' as 'archived' by default
        $query->where('status', '!=', 'archived');

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // Normalize the search term by removing '-' and spaces
            $normalizedSearch = str_replace(['-', ' '], '', $search);

            $query->whereHas('renoProgress.sale.order.property', function ($q) use ($normalizedSearch) {
                $q->where('name', 'like', '%' . $normalizedSearch . '%');
            })
                ->orWhereHas('renoProgress.sale.order', function ($q) use ($normalizedSearch) {
                    $q->whereRaw("REPLACE(REPLACE(CONCAT(block, floor, unit_no), '-', ''), ' ', '') like ?", ['%' . $normalizedSearch . '%']);
                });
        }

        if (empty($sortField)) {
            $sortField = 'id'; // Fallback to 'id' if sortField is empty
        }

        $query->orderBy($sortField, $sortOrder);

        $diForms = $query->paginate($size);

        // Custome response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $diForms->currentPage(),  // Current page number
            "pageCount" => $diForms->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $diForms->total(),  // Total number of items
            "data" => $request->input('head') === 'true' ? DefectInspectionFormResourceHead::collection($diForms) : DefectInspectionFormResource::collection($diForms) // Transformed product data
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function submitForm($formId)
    {
        try {
            $user = Auth::user();

            // Update complete date of the Progress
            $diForm = DefectInspectionForm::find($formId);

            $diForm->contractor_name = $user->name;
            $diForm->contractor_email = $user->email;
            $diForm->submitted_at = Carbon::now();
            $diForm->status = 'submitted';
            $diForm->save();

            if ($diForm->renoProgress->rpm_version === 3) {
                $rpmTask = $diForm->renoProgress->rpmJobs[1]->tasks[0];
                $rpmTask->status = 'completed';
                $rpmTask->save();
            }

            return $this->sendResponse('success', 'Form submitted successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }

    public function addDIF(Request $request)
    {
        $input = $request->input();

        try {
            // Generate hashed link for report
            $randomString = Str::random(12); // 32-character random string
            $base64String = base64_encode($randomString);
            $base64UrlString = str_replace(['+', '/', '='], ['-', '_', ''], $base64String);

            $formStatus = $input['isDISubmitted'] ? 'submitted' : 'not_available';

            $diBy = $input['inspectionType'] === 'By Owner' ? 'owner' : 'belive';

            $submitDate = $input['isDISubmitted'] ? Carbon::parse($input['submitted_at'])->format('Y-m-d H:i:s') : null;

            $form = DefectInspectionForm::create([
                'property_name' => $input['property_id'],
                'owner_email' => $input['owner_email'],
                // 'other_property_name' => null,
                'di_by' => $diBy,
                'block' => $input['block'],
                'level' => $input['floor'],
                'unit' => $input['unit'],
                'status' => $formStatus,
                'bedroom_count' => $input['bedrooms'],
                'bathroom_count' => $input['bathrooms'],
                'metadata' => null,
                'report_hash' => $base64UrlString,
                'submitted_at' => $submitDate,
            ]);

            return $this->sendResponse(new DefectInspectionFormResource($form), 'Form created successfully.');
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $form = DefectInspectionForm::find($id);

        if (is_null($form)) {
            return $this->sendError('Form not found.');
        }

        return $this->sendResponse(new DefectInspectionFormResource($form), 'Form retrieved successfully.');
    }

    public function publicShow($hashedString)
    {
        $form = DefectInspectionForm::where('report_hash', $hashedString)
            ->where('link_status', 'active')
            ->first();

        if (is_null($form)) {
            return $this->sendError('DI Report not found.');
        }

        return $this->sendResponse(new DefectInspectionFormResource($form), 'Form retrieved successfully.');
    }

    public function fetch($id)
    {
        $user = Auth::user();

        $diForm = DefectInspectionForm::where('created_by', $user->id)
            ->where('id', $id)
            ->first();

        return $this->sendResponse(new DefectInspectionFormResource($diForm), 'QC Form retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DefectInspectionForm $defectInspectionForm)
    {
        //
    }

    public function liveUpdateForm(Request $request, $formId)
    {
        $diForm = DefectInspectionForm::find($formId);

        if (is_null($diForm)) {
            return $this->sendError('Form not found.');
        }

        // s3 files directory
        $directory = 'form/defect/inspection/' . $formId;

        // Get $diForm->metadata
        $updatedMetadata = json_decode($diForm->metadata);

        // If $request->input() is key 'bedrooms' or 'bathrooms'
        if ($request->input('area') === 'bedrooms' || $request->input('area') === 'bathrooms') {
            foreach ($updatedMetadata as $area => $subAreas) {
                if ($request->input('area') === $area) {
                    foreach ($subAreas as $roomKey => $rooms) {
                        if ($request->input('sub_area') === $roomKey) {
                            foreach ($rooms as $q => $question) {
                                if ($request->input('question') === $q) {
                                    if ($request->input('value')) {
                                        $question->value = $request->input('value');
                                    } else if ($request->input('remark')) {
                                        $question->remark = $request->input('remark');
                                    } else if ($request->hasFile('attachment')) {
                                        $file = $request->file('attachment');
                                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                                        $path = Storage::disk('s3')->putFileAs(
                                            $directory,
                                            $file,
                                            $filename,
                                            'public'
                                        );

                                        $question->attachments = $question->attachments ?? []; // Initialize it if not already an array
                                        $question->attachments[] = [
                                            'file_url' => Storage::disk('s3')->path($path),
                                            'size' => $file->getSize(),
                                            'name' => $filename,
                                            'original_name' => $file->getClientOriginalName(),
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            // Else update $metadata
            foreach ($updatedMetadata as $area => $questions) {
                if ($request->input('area') === $area) {
                    foreach ($questions as $q => $question) {
                        if ($request->input('question') === $q) {
                            if ($request->input('value')) {
                                $question->value = $request->input('value');
                            } else if ($request->input('remark')) {
                                $question->remark = $request->input('remark');
                            } else if ($request->hasFile('attachment')) {
                                $file = $request->file('attachment');
                                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                                $path = Storage::disk('s3')->putFileAs(
                                    $directory,
                                    $file,
                                    $filename,
                                    'public'
                                );

                                $question->attachments = $question->attachments ?? []; // Initialize it if not already an array
                                $question->attachments[] = [
                                    'file_url' => Storage::disk('s3')->path($path),
                                    'size' => $file->getSize(),
                                    'name' => $filename,
                                    'original_name' => $file->getClientOriginalName(),
                                ];
                            }
                        }
                    }
                }
            }
        }

        $diForm->metadata = json_encode($updatedMetadata);
        $diForm->save();

        // if the diForm status is submitted, change it to not_submitted
        if ($diForm->status === 'submitted') {
            $diForm->status = 'not_submitted';
            $diForm->save();

            // Send Lark Message
        }

        return $this->sendResponse(new DefectInspectionFormResource($diForm), 'QC Form retrieved successfully.');
    }

    public function markAsCompleted($id)
    {
        $diForm = DefectInspectionForm::find($id);
        $renoProgress = $diForm->renoProgress;

        if (is_null($diForm)) {
            return $this->sendError('Form not found.');
        }

        $diForm->status = 'completed';
        $diForm->save();

        if ($diForm->renoProgress->rpm_version === 1 || $diForm->renoProgress->rpm_version === 2) {
            $diTask = $renoProgress->progressPhases[0]->jobs[1]->tasks[0];

            $diTask->status = 'completed';
            $diTask->save();
        } else {
            // 
        }

        return $this->sendResponse(new DefectInspectionFormResource($diForm), 'DI Form mark as paid successfully.');
    }

    public function toggleDIReportLink($id)
    {
        $diForm = DefectInspectionForm::find($id);

        if (is_null($diForm)) {
            return $this->sendError('Form not found.');
        }

        if ($diForm->link_status === 'unactive') {
            $diForm->link_status = 'active';
        } else {
            $diForm->link_status = 'unactive';
        }

        $diForm->save();

        return $this->sendResponse(new DefectInspectionFormResource($diForm), 'DI Form link status toggled successfully.');
    }

    public function generateDIForm($id)
    {
        $diForm = DefectInspectionForm::find($id);

        if (is_null($diForm)) {
            return $this->sendError('Form not found.');
        }



        $metadata = [
            'yard' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
            ],
            'foyer' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
            ],
            'living' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
                'q7' => ['value' => '', 'remark' => null],
                'q8' => ['value' => '', 'remark' => null],
                'q9' => ['value' => '', 'remark' => null],
            ],
            'balcony' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
            ],
            'hallway' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
            ],
            'kitchen' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
                'q7' => ['value' => '', 'remark' => null],
                'q8' => ['value' => '', 'remark' => null],
            ],
            'bedrooms' => [],
            'bathrooms' => [],
        ];

        // Generate bedroom entries dynamically
        $bedroomTemplate = [
            'q1' => ['value' => '', 'remark' => null],
            'q2' => ['value' => '', 'remark' => null],
            'q3' => ['value' => '', 'remark' => null],
            'q4' => ['value' => '', 'remark' => null],
            'q5' => ['value' => '', 'remark' => null],
            'q6' => ['value' => '', 'remark' => null],
            'q7' => ['value' => '', 'remark' => null],
            'q8' => ['value' => '', 'remark' => null],
            'q9' => ['value' => '', 'remark' => null],
        ];

        for ($i = 1; $i <= $diForm->bedroom_count; $i++) {
            $metadata['bedrooms']["bedroom{$i}"] = $bedroomTemplate;
        }

        // Generate bathroom entries dynamically
        $bathroomTemplate = [
            'q1' => ['value' => '', 'remark' => null],
            'q2' => ['value' => '', 'remark' => null],
            'q3' => ['value' => '', 'remark' => null],
            'q4' => ['value' => '', 'remark' => null],
            'q5' => ['value' => '', 'remark' => null],
            'q6' => ['value' => '', 'remark' => null],
            'q7' => ['value' => '', 'remark' => null],
            'q8' => ['value' => '', 'remark' => null],
            'q9' => ['value' => '', 'remark' => null],
        ];

        for ($i = 1; $i <= $diForm->bathroom_count; $i++) {
            $metadata['bathrooms']["bathroom{$i}"] = $bathroomTemplate;
        }

        $diForm->status = 'not_submitted';
        $diForm->metadata = json_encode($metadata);
        $diForm->save();

        return $this->sendResponse(new DefectInspectionFormResource($diForm), 'DI Form generated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DefectInspectionForm $defectInspectionForm)
    {
        //
    }

    // $request->input() = {
    //     "area": "bathrooms",
    //     "sub-area": "bedroom2",
    //     "question": "q5",
    //     "fileIndex": 0
    // }
    public function removeAttachment(Request $request, $renoProgressId)
    {
        $diForm = DefectInspectionForm::where('reno_progress_id', $renoProgressId)->first();

        if (is_null($diForm)) {
            return $this->sendError('Form not found.');
        }

        // Get $diForm->metadata
        $updatedMetadata = json_decode($diForm->metadata);

        if ($request->input('area') === 'bedrooms' || $request->input('area') === 'bathrooms') {
            foreach ($updatedMetadata as $area => $subAreas) {
                if ($request->input('area') === $area) {
                    foreach ($subAreas as $roomKey => $rooms) {
                        if ($request->input('sub_area') === $roomKey) {
                            foreach ($rooms as $q => $question) {
                                if ($request->input('question') === $q) {
                                    if ($request->input('file_index') !== null) {
                                        $fileUrl = $question->attachments[$request->input('file_index')]->file_url;
                                        Storage::disk('s3')->deleteDirectory($fileUrl);
                                        unset($question->attachments[$request->input('file_index')]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($updatedMetadata as $area => $questions) {
                if ($request->input('area') === $area) {
                    foreach ($questions as $q => $question) {
                        if ($request->input('question') === $q) {
                            if ($request->input('file_index') !== null) {
                                $fileUrl = $question->attachments[$request->input('file_index')]->file_url;
                                Storage::disk('s3')->deleteDirectory($fileUrl);
                                unset($question->attachments[$request->input('file_index')]);
                            }
                        }
                    }
                }
            }
        }

        $diForm->metadata = json_encode($updatedMetadata);
        $diForm->save();

        return $this->sendResponse(new DefectInspectionFormResource($diForm), 'QC Form retrieved successfully.');
    }
}


// Invoice status
// paid, overdue, pending