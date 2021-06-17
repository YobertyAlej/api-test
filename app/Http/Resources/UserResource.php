<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'nombre' => $this->name,
            'email' => $this->email,
            'edad' => $this->age,
            'fecha_de_nacimiento' => $this->dob,
            'sexo' => $this->gender,
            'dni' => $this->dni,
            'direccion' => $this->address,
            'pais' => $this->country,
            'telefono' => $this->phone,
            'roles' => $this->roles->pluck('name')
        ];
    }
}
