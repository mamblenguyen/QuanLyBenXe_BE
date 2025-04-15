<?php

namespace App\Http\Resources\System\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,  // Added phone number
            'address' => $this->address, 
            'avatar' => $this->avatar, 
            'password' => $this->password, // Added address
            'birthday' => $this->birthday, 
            'gender' => $this->gender, 
            'identity_card' => $this->identity_card,  // Added identity card
            'identity_issue_place' => $this->identity_issue_place,  // Added place of issue
            'identity_issue_date' => $this->identity_issue_date,  // Added date of issue
            'status' => $this->status,  // Added status
            'license' => $this->license,  // Added license number
            'license_issue_date' => $this->license_issue_date,  // Added license issue date
            'license_expiry_date' => $this->license_expiry_date,  // Added license expiry date
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'roles' => $this->roles,  // Added roles information
            'garage' => $this->Garage,  // Added garage details
        ];
    }
}
