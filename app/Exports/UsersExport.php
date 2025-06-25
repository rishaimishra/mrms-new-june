<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    protected $User;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(Request $request)
    {
        $this->User = User::orderBy('created_at', 'desc');
        $this->applyFilters($request);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $users = $this->User->get()->toArray();
        $userArray[] = ['Name', 'Email', 'Mobile', 'Created At'];
        foreach ($users as $key => $value) {
            $userArray[] = [$value['name'], $value['email'], $value['mobile_number'], $value['created_at']];
        }
        return new Collection($userArray);
    }

    public function applyFilters($request)
    {
        !$request->name || $this->User->orWhere('users.name', 'like', "%{$request->name}%");

        !$request->mobile_number || $this->User->orWhere('users.mobile_number', 'like', "%{$request->mobile_number}%");

        !$request->email || $this->User->orWhere('users.email', 'like', "%{$request->email}%");

    }
}
