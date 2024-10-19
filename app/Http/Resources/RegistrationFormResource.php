<?php

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $salutationOptions = [
            'mr' => 'Mr',
            'ms' => 'Ms',
            'mrs' => 'Mrs',
            'doctor' => 'Doctor',
            'datuk' => 'Datuk',
            'dato' => 'Dato',
            'datin' => 'Datin',
            'datuk_seri' => 'Datuk Seri',
            'dato_seri' => 'Dato Seri',
            'datin_seri' => 'Datin Seri',
        ];

        $options = [
            'quest_1' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
            ],
            'quest_2' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
            ],
            'quest_3' => [
                'done' => 'Done',
                'not_yet' => 'Not Yet',
            ],
            'quest_4' => [
                'done' => 'Done',
                'not_yet' => 'Not Yet',
            ],
            'quest_5' => [
                'done' => 'Done',
                'not_yet' => 'Not Yet',
            ],
            'quest_6' => [
                'done' => 'Done',
                'not_yet' => 'Not Yet',
                'in_progress' => 'In Progress',
            ],
            'quest_7' => [
                'done' => 'Done',
                'not_yet' => 'Not Yet',
                'no_defect' => 'No Defect',
            ],
            'quest_8' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
        ];

        return [
            'id' => $this->id,
            'salutations' => $salutationOptions[$this->salutations] ?? null,
            'name_first' => $this->name_first,
            'name_last' => $this->name_last,
            'name_preferred' => $this->name_preferred,
            'email' => $this->email,
            'phone_no' => $this->phone_no,
            'address' => [
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'city' => $this->city,
                'state' => $this->state,
                'postcode' => $this->postcode,
            ],
            'ic' => $this->ic,
            'property' => [
                'property_name' => Property::find($this->property_name)->name,
                'block' => $this->block,
                'level' => $this->level,
                'unit' => $this->unit,
                'layout_type' => $this->layout_type,
                'sqft' => $this->sqft,
            ],
            'questions' => [
                'quest_1' => $options['quest_1'][$this->quest_1] ?? null,
                'quest_2' => $options['quest_2'][$this->quest_2] ?? null,
                'quest_3' => $options['quest_3'][$this->quest_3] ?? null,
                'quest_4' => $options['quest_4'][$this->quest_4] ?? null,
                'quest_5' => $options['quest_5'][$this->quest_5] ?? null,
                'quest_6' => $options['quest_6'][$this->quest_6] ?? null,
                'quest_7' => $options['quest_7'][$this->quest_7] ?? null,
                'quest_8' => $options['quest_8'][$this->quest_8] ?? null,
            ],
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
