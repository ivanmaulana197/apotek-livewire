<?php

namespace App\Traits;

trait ScopeSearchable
{

    /**
     * Contoh Penggunaan:
     * Barang::search($this->search, ['nama', 'kode'], ['kategori' => ['nama']]);
     */
    public function scopeSearch($query, $search, $columns = ['nama'], $relations = [])
    {
        // $query->whereAny(['nama'], 'like', "%$search%");

        $tokens = explode(' ', $search);

        // Pencarian di kolom utama (tabel Barang)
        $query->where(function ($query) use ($tokens, $columns) {
            foreach ($tokens as $token) {
                $query->whereAny($columns, 'like', "%$token%");
            }
        });

        // Pencarian di tabel relasi (misalnya kategori.nama)
        foreach ($relations as $relation => $relationColumns) {
            $query->orWhereHas($relation, function ($q) use ($tokens, $relationColumns) {
                foreach ($tokens as $token) {
                    $q->whereAny($relationColumns, 'like', "%$token%");
                }
            });
        }

        // Prioritas hasil yang cocok dengan awalan pencarian di kolom utama
        $primaryColumn = $columns[0]; // Ambil kolom utama untuk sorting
        $query->orderByRaw(
            "CASE
            WHEN $primaryColumn LIKE ? THEN 1
            WHEN $primaryColumn LIKE ? THEN 2
            ELSE 3
        END",
            ["$search%", "%$search%"],
        );
    }

    
}
