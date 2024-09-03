<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UsersTable extends Component
{
    use WithPagination;

    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function paginationView()
    {
        return 'livewire.pagination-links';
    }

    public function render()
    {
        $users = User::select(
            'users.id', 
            'users.name', 
            'users.birthdate', 
            'users.email', 
            'users.email_verified_at', 
            'users.created_at', 
            'users.updated_at', 
        )
            ->withLastPurchaseDate()
            ->havingBirthdayThisWeek()
            ->orderByBirthday()
            ->paginate($this->perPage);


        return view('livewire.users-table', [
            'users' => $users,
            'currentPage' => $users->currentPage(),
        ]);
    }
}