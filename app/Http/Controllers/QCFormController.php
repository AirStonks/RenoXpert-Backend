<?php

namespace App\Http\Controllers;

use App\Http\Resources\QCFormResource;
use App\Models\QCForm;
use App\Models\Operations\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QCFormController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $qcForms = QCForm::where('created_by', $user->id)->get();

        return $this->sendResponse(QCFormResource::collection($qcForms), 'QC forms retrieved successfully.');
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
            $user = Auth::user();

            $updatedArea = $input['area'];

            $directory = 'form/qc/' . now()->format('Y-m-d_H-i-s');

            foreach ($updatedArea as $areaIndex => $sections) {
                if ($areaIndex === 'bedrooms' || $areaIndex === 'bathrooms') {
                    $dynamicField = $sections;

                    foreach ($dynamicField as $dynamicKey => $sections) {
                        foreach ($sections as $questionIndex => $questions) {

                            unset($updatedArea[$areaIndex][$dynamicKey][$questionIndex]['label']);

                            foreach ($questions as $qkey => $question) {
                                unset($updatedArea[$areaIndex][$dynamicKey][$questionIndex][$qkey]['label']);
                            }


                            if (isset($questions['attachments']) && is_array($questions['attachments'])) {
                                foreach ($questions['attachments'] as $key => $attachment) {
                                    // Store the file in the public storage
                                    $filename = uniqid() . '.' . $attachment->getClientOriginalExtension();
                                    $path = Storage::disk('s3')->putFileAs(
                                        $directory,
                                        $attachment,
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
                    foreach ($sections as $questionIndex => $questions) {

                        unset($updatedArea[$areaIndex][$questionIndex]['label']);

                        foreach ($questions as $qkey => $question) {
                            unset($updatedArea[$areaIndex][$questionIndex][$qkey]['label']);
                        }

                        if (isset($questions['attachments']) && is_array($questions['attachments'])) {

                            foreach ($questions['attachments'] as $key => $attachment) {
                                // Store the file in the public storage
                                $path = $attachment['file']->store($directory, 'public');

                                // Update the attachment details
                                $updatedArea[$areaIndex][$questionIndex]['attachments'][$key] = [
                                    'file_url' => Storage::url($path), // Get the public URL of the stored file
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
            $input['include_commune_living'] = $input['include_commune_living'] === true ? 1 : 0;
            $input['status'] = 'submitted';
            $input['inspector_first_name'] = $user->name_first;
            $input['inspector_last_name'] = $user->name_last;
            $input['metadata'] = json_encode($updatedArea);

            $metadata = json_decode($input['metadata']);

            $form = QCForm::create($input);


            $renoProgress = RenoProgress::find($input['reno_progress_id']);
            $qcTask = $renoProgress->progressPhases[2]->jobs[0]->tasks[0];

            $qcTask->is_installed = 1;
            $qcTask->install_date = Carbon::now();

            $qcTask->save();

            return $this->sendResponse(new QCFormResource($form), 'QC Form submitted successfully.');
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
    public function show(QCForm $qCForm)
    {
        //
    }

    public function fetch($id)
    {
        $user = Auth::user();

        $qcForm = QCForm::where('created_by', $user->id)
            ->where('id', $id)
            ->first();

        return $this->sendResponse(new QCFormResource($qcForm), 'QC Form retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QCForm $qCForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QCForm $qCForm)
    {
        //
    }
}
