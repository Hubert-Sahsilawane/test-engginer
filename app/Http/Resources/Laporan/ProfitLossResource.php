<?php

namespace App\Http\Resources\Laporan;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfitLossResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'kategori' => $this['kategori'] ?? null,
            'total' => $this['total'] ?? 0,
        ];
    }
}
