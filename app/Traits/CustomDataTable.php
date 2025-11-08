<?php
namespace App\Traits;

trait CustomDataTable
{
    // public string $search = '';
    // public int $perPage = '';
    public $search  = '';
    public $perPage = 5;
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public $defaultSortBy  = 'updated_at';
    public $sortBy         = 'updated_at';
    public $defaultSortDir = 'desc';
    public $sortDir        = 'desc';

    public function doSort($column)
    {
        if ($this->sortBy === $column) {
            if ($this->sortDir === ($this->defaultSortDir == 'desc' ? 'asc' : 'desc')) {
                // Reset ke default: asc by updated_at
                $this->sortBy  = $this->defaultSortBy;
                $this->sortDir = $this->defaultSortDir == 'desc' ? 'desc' : 'asc';
            } else {
                // Ubah ke desc
                $this->sortDir = $this->defaultSortDir == 'desc' ? 'asc' : 'desc';
            }
            // dump($this->sortDir);
            // $this->sortDir = $this->sortDir === 'desc' ? 'asc' : 'desc';

            return;
        }
        $this->sortBy  = $column;
        $this->sortDir = $this->defaultSortDir;
    }

    public function highlight($text)
    {
        $tokens  = explode(' ', $this->search);
        $pattern = '/' . implode('|', array_map('preg_quote', $tokens)) . '/i';

        return preg_replace($pattern, '<span class="highlight">$0</span>', $text);
    }
}
