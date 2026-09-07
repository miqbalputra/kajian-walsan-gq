<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Backup Data')]
class BackupIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.backup-index');
    }
}
