<?php

namespace App\Http\Controllers;

use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\DefectInspectionForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\DefectInspectionFormResource;

class DefectInspectionFormController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $diForms = DefectInspectionForm::where('created_by', $user->id)->get();

        return $this->sendResponse(DefectInspectionFormResource::collection($diForms), 'Defect forms retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function submitForm(Request $request)
    {
        try {
            $input = $request->all();

            $updatedArea = $input['area'];

            $directory = 'form/defect/inspection/' . now()->format('Y-m-d_H-i-s');

            foreach ($updatedArea as $areaIndex => $questions) {
                if ($areaIndex === 'bedrooms' || $areaIndex === 'bathrooms') {

                    $dynamicField = $questions;

                    foreach ($dynamicField as $dynamicKey => $questions) {
                        foreach ($questions as $questionIndex => $question) {
                            if (isset($question['attachments']) && is_array($question['attachments'])) {
                                foreach ($question['attachments'] as $key => $attachment) {
                                    // Store the file in the public storage
                                    $filename = uniqid() . '.' . $attachment['file']->getClientOriginalExtension();
                                    $path = Storage::disk('s3')->putFileAs(
                                        $directory,
                                        $attachment['file'],
                                        $filename,
                                        'public'
                                    );

                                    // Update the attachment details
                                    $updatedArea[$areaIndex][$dynamicKey][$questionIndex]['attachments'][$key] = [
                                        'file_url' => Storage::disk('s3')->path($path),
                                        'original_name' => $attachment['file']->getClientOriginalName(),
                                    ];
                                }
                            }
                        }
                    }
                } else {
                    foreach ($questions as $questionIndex => $question) {
                        if (isset($question['attachments']) && is_array($question['attachments'])) {
                            foreach ($question['attachments'] as $key => $attachment) {
                                // Store the file in the public storage
                                $filename = uniqid() . '.' . $attachment['file']->getClientOriginalExtension();
                                $path = Storage::disk('s3')->putFileAs(
                                    $directory,
                                    $attachment['file'],
                                    $filename,
                                    'public'
                                );

                                // Update the attachment details
                                $updatedArea[$areaIndex][$questionIndex]['attachments'][$key] = [
                                    'file_url' => Storage::disk('s3')->path($path), // Get the public URL of the stored file
                                    'original_name' => $attachment['file']->getClientOriginalName(),
                                ];
                            }
                        }
                    }
                }
            }

            $input['property_name'] = $input['property']['property_name'];
            $input['other_property_name'] = $input['property']['other_property_name'];
            $input['block'] = $input['property']['block'];
            $input['level'] = $input['property']['level'];
            $input['unit'] = $input['property']['unit'];
            $input['status'] = 'submitted';
            $input['metadata'] = json_encode($updatedArea);

            $metadata = json_decode($input['metadata']);

            $form = DefectInspectionForm::create($input);

            // Update complete date of the Progress
            $renoProgress = RenoProgress::find($input['reno_progress_id']);
            $diTask = $renoProgress->progressPhases[0]->jobs[1]->tasks[0];

            $diTask->is_installed = 1;
            $diTask->install_date = Carbon::now();

            $diTask->save();

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DefectInspectionForm $defectInspectionForm)
    {
        //
    }
}


// Invoice status
// paid, overdue, pending