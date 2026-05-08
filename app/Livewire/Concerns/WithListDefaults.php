<?php

namespace App\Livewire\Concerns;

trait WithListDefaults
{
    public int $perPage = 15;

    public function updatedSearch(): void
    {
        if (property_exists($this, 'search')) {
            $this->resetPage();
        }
    }
}
