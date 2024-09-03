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
        // $users = User::select(
        //     'users.id', 
        //     'users.name', 
        //     'users.birthdate', 
        //     'users.email', 
        //     'users.email_verified_at', 
        //     'users.created_at', 
        //     'users.updated_at', 
        // )
        // ->withLastPurchaseDate()
        // ->havingBirthdayThisWeek()
        // ->orderByBirthday()
        // ->paginate($this->perPage);

        // $users = DB::table('users')->select(
        //     'users.id', 
        //     'users.name', 
        //     'users.birthdate', 
        //     'users.email', 
        //     'users.email_verified_at', 
        //     'users.created_at', 
        //     'users.updated_at', 
        //     DB::raw('MAX(purchases.purchase_date) as last_purchase_date')
        // )
        // ->leftJoin('purchases', 'users.id', '=', 'purchases.user_id')
        // ->groupBy('users.id')
        // ->get();

        // $filteredUsers = $users->filter(function($user) use ($startOfWeek, $endOfWeek) {
        //     $birthdate = Carbon::parse($user->birthdate);
        //     $birthdateThisYear = $birthdate->copy()->year(now()->year);

        //     return $birthdateThisYear->between($startOfWeek, $endOfWeek);
        // });

        // $paginatedUsers = $this->paginate($filteredUsers);


        // $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        // $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);
        Debugbar::startMeasure('getUsers');

        // $users = DB::table('users')->select(
        //     'users.id', 
        //     'users.name', 
        //     'users.birthdate', 
        //     'users.email', 
        //     'users.email_verified_at', 
        //     'users.created_at', 
        //     'users.updated_at', 
        //     DB::raw('MAX(purchases.purchase_date) as last_purchase_date'),
        // )
        // ->leftJoin('purchases', 'users.id', '=', 'purchases.user_id')
        // ->groupBy('users.id')
        // ->whereBetween(
        //     DB::raw('strftime(\'%m-%d\', birthdate)'), 
        //     [
        //         $startOfWeek->format('m-d'), 
        //         $endOfWeek->format('m-d')
        //     ]
        // )
        // ->orderByRaw('strftime(\'%m\', birthdate) ASC, strftime(\'%d\', birthdate) ASC')
        // ->paginate($this->perPage);

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

        Debugbar::stopMeasure('getUsers');

        return view('livewire.users-table', [
            'users' => $users,
            'currentPage' => $users->currentPage(),
        ]);
    }

    // public function paginate($items, $page = null, $options = [])
    // {
    //     $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

    //     $items = $items instanceof Collection ? $items : Collection::make($items);

    //     $paginatedItems = new LengthAwarePaginator(
    //         $items->forPage($page, $this->perPage), 
    //         $items->count(), 
    //         $this->perPage, 
    //         $page, 
    //         $options
    //     );
    //     $paginatedItems->setPath(request()->url());

    //     return $paginatedItems;
    // }
}