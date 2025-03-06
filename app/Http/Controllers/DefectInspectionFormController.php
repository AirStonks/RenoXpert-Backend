<?php

namespace App\Http\Controllers;

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
        // Retrieve the size parameter from the request with a default value of 10
        $size = $request->input('size', 10);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

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
            });
        }

        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        // $sortField = $request->input('sortField', 'name');

        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

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

    public function submitForm($renoProgressId)
    {
        try {
            $user = Auth::user();

            // $input = $request->all();

            // $updatedArea = $input['area'];

            // $directory = 'form/defect/inspection/' . now()->format('Y-m-d_H-i-s');

            // foreach ($updatedArea as $areaIndex => $questions) {
            //     if ($areaIndex === 'bedrooms' || $areaIndex === 'bathrooms') {

            //         $dynamicField = $questions;

            //         foreach ($dynamicField as $dynamicKey => $questions) {
            //             foreach ($questions as $questionIndex => $question) {
            //                 if (isset($question['attachments']) && is_array($question['attachments'])) {
            //                     foreach ($question['attachments'] as $key => $attachment) {
            //                         // Store the file in the public storage
            //                         $filename = uniqid() . '.' . $attachment['file']->getClientOriginalExtension();
            //                         $path = Storage::disk('s3')->putFileAs(
            //                             $directory,
            //                             $attachment['file'],
            //                             $filename,
            //                             'public'
            //                         );

            //                         // Update the attachment details
            //                         $updatedArea[$areaIndex][$dynamicKey][$questionIndex]['attachments'][$key] = [
            //                             'file_url' => Storage::disk('s3')->path($path),
            //                             'original_name' => $attachment['file']->getClientOriginalName(),
            //                         ];
            //                     }
            //                 }
            //             }
            //         }
            //     } else {
            //         foreach ($questions as $questionIndex => $question) {
            //             if (isset($question['attachments']) && is_array($question['attachments'])) {
            //                 foreach ($question['attachments'] as $key => $attachment) {
            //                     // Store the file in the public storage
            //                     $filename = uniqid() . '.' . $attachment['file']->getClientOriginalExtension();
            //                     $path = Storage::disk('s3')->putFileAs(
            //                         $directory,
            //                         $attachment['file'],
            //                         $filename,
            //                         'public'
            //                     );

            //                     // Update the attachment details
            //                     $updatedArea[$areaIndex][$questionIndex]['attachments'][$key] = [
            //                         'file_url' => Storage::disk('s3')->path($path), // Get the public URL of the stored file
            //                         'original_name' => $attachment['file']->getClientOriginalName(),
            //                     ];
            //                 }
            //             }
            //         }
            //     }
            // }

            // $input['property_name'] = $input['property']['property_name'];
            // $input['other_property_name'] = $input['property']['other_property_name'];
            // $input['block'] = $input['property']['block'];
            // $input['level'] = $input['property']['level'];
            // $input['unit'] = $input['property']['unit'];
            // $input['status'] = 'submitted';
            // $input['metadata'] = json_encode($updatedArea);

            // $metadata = json_decode($input['metadata']);

            // $form = DefectInspectionForm::create($input);

            // Update complete date of the Progress
            $renoProgress = RenoProgress::find($renoProgressId);
            $diTask = $renoProgress->progressPhases[0]->jobs[1]->tasks[0];
            $diForm = $renoProgress->defectInspectionForm;

            $diTask->is_installed = 1;
            $diTask->install_date = Carbon::now();
            $diTask->status = 'submitted';
            $diTask->save();


            $diForm->contractor_name = $user->name;
            $diForm->contractor_email = $user->email;
            $diForm->status = 'submitted';
            $diForm->save();

            return $this->sendResponse('success', 'Form submitted successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
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


    // $request->input() = {
    //     "area": "bedrooms",
    //     "sub-area": "bedroom1",
    //     "question": "q1",
    //     "value": "has_defect",
    // }

    // $request->input() = {
    //     "area": "foyer",
    //     "question": "q2",
    //     "remark": "some remark",
    // }

    // $request->input() = {
    //     "area": "bathrooms",
    //     "sub-area": "bedroom2",
    //     "question": "q5",
    //     "attachment": "file_value"
    // }

    public function liveUpdateForm(Request $request, $renoProgressId)
    {
        $diForm = DefectInspectionForm::where('reno_progress_id', $renoProgressId)->first();

        if (is_null($diForm)) {
            return $this->sendError('Form not found.');
        }

        // s3 files directory
        $directory = 'form/defect/inspection/' . $renoProgressId;

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
        $diTask = $renoProgress->progressPhases[0]->jobs[1]->tasks[0];

        if (is_null($diForm)) {
            return $this->sendError('Form not found.');
        }

        $diForm->status = 'completed';
        $diForm->save();

        $diTask->status = 'completed';
        $diTask->save();

        return $this->sendResponse(new DefectInspectionFormResource($diForm), 'QC Form retrieved successfully.');
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