<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\RealEstateInterestedUser;

class InterestedRealEstateExport implements FromCollection
{
    protected $users;
    protected $realestateInterestedUser;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(Request $request)
    {
        $this->realestateInterestedUser = RealEstateInterestedUser::join('users as u', 'u.id', 'real_estate_interested_user.user_id')
            ->join('real_estates as re', 're.id', 'real_estate_interested_user.real_estate_id')->select('u.id as user_id', 'u.name as user_name', 're.id as real_estates_id', 're.name as real_estates_name', 'real_estate_interested_user.created_at');


        $this->applyFilters($request);
    }

    public function collection()
    {

        $users = $this->realestateInterestedUser->get()->toArray();

        $addressArray[] = ['User', 'Real Estate', 'Created At'];
        foreach ($users as $key => $value) {
            $addressArray[] = [$value['user_name'], $value['real_estates_name'], $value['created_at']];
        }
        return new Collection($addressArray);
    }

    public function applyFilters($request)
    {

        !$request->user || $this->realestateInterestedUser->where('u.name', 'like', "%{$request->user}%");
        !$request->realEstate || $this->realestateInterestedUser->where('re.name', 'like', "%{$request->realEstate}%");
    }
}
