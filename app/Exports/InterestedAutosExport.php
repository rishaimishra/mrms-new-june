<?php

namespace App\Exports;

use App\Models\User;
use App\Models\AutoInterestedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class InterestedAutosExport implements FromCollection
{
    protected $users;
    protected $autoInterestedUser;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(Request $request)
    {
        $this->autoInterestedUser = AutoInterestedUser::join('users as u', 'u.id', 'auto_interested_user.user_id')
            ->join('autos as au', 'au.id', 'auto_interested_user.auto_id')->select('u.id as user_id', 'u.name as user_name', 'au.id as auto_id', 'au.name as auto_name', 'auto_interested_user.created_at');


        $this->applyFilters($request);
    }

    public function collection()
    {

        $users = $this->autoInterestedUser->get()->toArray();

        $addressArray[] = ['User', 'Auto', 'Created At'];
        foreach ($users as $key => $value) {
            $addressArray[] = [$value['user_name'], $value['auto_name'], $value['created_at']];
        }
        return new Collection($addressArray);
    }

    public function applyFilters($request)
    {
        !$request->user || $this->autoInterestedUser->where('u.name', 'like', "%{$request->user}%");
        !$request->auto || $this->autoInterestedUser->where('au.name', 'like', "%{$request->auto}%");
    }
}
