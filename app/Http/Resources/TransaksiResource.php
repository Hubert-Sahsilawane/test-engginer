<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'coa' => new CoaResource($this->whenLoaded('coa')),
        ];
    }
}
