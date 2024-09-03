<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
            'users.birthday', 
            'users.email', 
            'users.email_verified_at', 
            'users.created_at', 
            'users.updated_at', 
        )
            ->withLastPurchaseDate()
            ->havingBirthdayThisWeek()
            ->orderBy('birthday')
            ->paginate($this->perPage);


        return view('livewire.users-table', [
            'users' => $users,
            'currentPage' => $users->currentPage(),
        ]);
    }
}