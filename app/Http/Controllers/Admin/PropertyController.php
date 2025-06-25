<?php

namespace App\Http\Controllers\Admin;
use App\Grids\LandLordVerifyGrid;
use App\Exports\PropertyExport;
use App\Exports\Vipsdownload;
use App\Exports\WaybillExport;
use App\Exports\SummaryExport;
use App\Grids\PropertiesGrid;
use App\Http\Controllers\Controller;
use App\Jobs\PropertyInBulk;
use App\Jobs\PropertyEnvpBulk;
use App\Jobs\PropertyNotice;
use App\Jobs\PropertyStickers;
use App\Logic\SystemConfig;
use App\Models\BoundaryDelimitation;
use App\Models\Property;
use App\Models\Summary;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyInaccessible;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyPayment;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\RegistryMeter;
use App\Models\PropertyWindowType;
use App\Models\LandlordDetail;
use App\Models\UserTitleTypes;
use App\Models\PropertySanitationType;
use App\Models\AdjustmentValue;
use App\Models\Adjustment;
use App\Models\Swimming;
use App\Models\User;
use App\Models\Bulk;
use App\Models\District;
use App\Models\InaccessibleProperty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio;
use App\Notifications\PaymentRequestSMS;
use DB;
use App\Exports\BulkExport;
use App\Imports\BulkImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SmsToProperty;
use App\Models\CounsilAdjustmentGroupA;
use App\Models\PropertyToCounsilGroupA;
use App\UserAssignedProperty;

class PropertyController extends Controller
{
    private $properties;

     public function updateYearsValue()
    {
        // set_time_limit(24600);
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;
        
        $propertyAssessmentDetail = PropertyAssessmentDetail::all();
        // $propertyAssessmentDetail = PropertyAssessmentDetail::whereYear('created_at', $previousYear)->get();
        foreach($propertyAssessmentDetail as $row)
        {
            $new_property_assessment_detail = PropertyAssessmentDetail::where('property_id',$row->property_id)->latest()->first();
            // return $new_property_assessment_detail;
            if(date("Y",strtotime($new_property_assessment_detail->created_at)) != $currentYear)
            {
                $newRecord = $new_property_assessment_detail->replicate();
                $newRecord->created_at = date("Y-m-d H:i:s");
                $newRecord->save();
            }
        }

        dd(111);
    }

    public function list(PropertiesGrid $usersGrid, Request $request)
    {
        // return $request;
        DB::enableQueryLog();

        $organizationTypes = collect(json_decode(file_get_contents(storage_path('data/organizationTypes.json')), true))->pluck('label', 'value');

        $this->properties = Property::with([
            'user',
            'landlord',
            'assessment' => function ($query) use ($request) {
                if ($request->filled('demand_draft_year')) {
                    $query->whereYear('created_at', $request->demand_draft_year);
                    // $query->whereYear('created_at', '<=', $request->demand_draft_year);

                }
            },
            'geoRegistry',
            'user',
            'occupancies',
            'propertyInaccessible',
            'payments',
            'districts',
            'images',
            'assessment',
        ])
            ->whereHas('assessment', function ($query) use ($request) {

                if ($request->filled('demand_draft_year')) {
                    $query->whereYear('created_at', $request->demand_draft_year);
                    // $query->whereYear('created_at', '<=', $request->demand_draft_year);

                }
                    // dd($request->demand_draft_year);
                if ($request->filled('is_printed')) {

                    if ($request->input('is_printed') === '1') {
                        $query->whereNotNull('last_printed_at');
                    }

                    if ($request->input('is_printed') === '0') {
                        $query->whereNull('last_printed_at');
                    }
                }

                if ($request->is_gated_community) {
                    $query->where('gated_community', $request->gated_community);
                }

            })
            ->whereHas('districts', function($query) {
                $query->where('id',13);
            });

        if (request()->user()->hasRole('Super Admin') OR request()->user()->hasRole('Super Admin Cus')) {
        } else {
            $this->properties->where('district', request()->user()->assign_district);
        }
        
        if ($request->start_date && $request->end_date) {
            $this->properties->whereBetween('properties.created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            !$request->start_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::parse($request->start_date), Carbon::now()]);
            !$request->end_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::now()->subYear(5), Carbon::parse($request->end_date)->endOfDay()]);
        }

        if ($request->unpaid_start_date && $request->unpaid_end_date) {

            $year = date('Y', strtotime($request->unpaid_start_date));
            //$this->properties->whereYear('properties.created_at', $year);

            $this->properties->doesntHave('payments');
        }

       

        if ($payment_status = $request->input('paid')) {
            if ($payment_status == 'paid') {
                $this->properties->whereHas('payments');
            } else {
                $this->properties->doesntHave('payments');
            }
        }

        if ($request->paid_start_date && $request->paid_end_date) {
            $year = date('Y', strtotime($request->paid_start_date));
            //$this->properties->whereYear('property_payments.created_at', $year);

            $this->properties->whereHas('payments', function ($query) use ($request) {
                return $query->whereBetween('property_payments.created_at', [Carbon::parse($request->paid_start_date)->startOfDay(), Carbon::parse($request->paid_end_date)->endOfDay()]);
            });
        }

        !$request->occupancy_type ?: $this->properties->whereHas('occupancy', function ($query) use ($request) {
            return $query->where('type', $request->occupancy_type);
        });

         if($request->counsil_adjustmnt){
            if($request->counsil_adjustmnt=="Yes"){
                $allDataFromCounsilTable=PropertyToCounsilGroupA::pluck('property_id')->toArray();
                $Unique=array_unique($allDataFromCounsilTable);
                // dd($Unique);
            $request->filled('counsil_adjustmnt') && $this->properties->whereIn('properties.id', $allDataFromCounsilTable);
            }  else{
                $allDataFromCounsilTable=PropertyToCounsilGroupA::pluck('property_id')->toArray();
                $Unique=array_unique($allDataFromCounsilTable);
                // dd($Unique);
            $request->filled('counsil_adjustmnt') && $this->properties->whereNotIn('properties.id', $allDataFromCounsilTable);
            }
        }

         if($request->property_id){
            $property_ids=explode(",",$request->property_id);
            // dd($property_ids);
            $request->filled('property_id') && $this->properties->whereIn('properties.id', $property_ids);
        }

        // $request->filled('property_id') && $this->properties->where('properties.id', $request->input('property_id'));

        !$request->town ?: $this->properties->where('properties.section', $request->town);
        !$request->street_name ?: $this->properties->where('properties.street_name', 'like', "%{$request->street_name}%");
        !$request->street_number ?: $this->properties->where('properties.street_number', $request->street_number);
        !$request->postcode ?: $this->properties->where('properties.postcode', $request->postcode);
        !$request->ward ?: $this->properties->where('properties.ward', $request->ward);
        !$request->district ?: $this->properties->where('properties.district', $request->district);
        !$request->province ?: $this->properties->where('properties.province', $request->province);
        !$request->chiefdom ?: $this->properties->where('properties.chiefdom', $request->chiefdom);
        !$request->constituency ?: $this->properties->where('properties.constituency', $request->constituency);

        $request->is_accessible == "0" ? $this->properties->where('is_property_inaccessible', 0) : null;
        $request->is_accessible == "1" ? $this->properties->where('is_property_inaccessible', 1) : null;

        $request->is_draft_delivered == "0" ? $this->properties->whereHas('assessment', function ($query) use ($request) {
            $query->whereYear('created_at', now()->format('Y'))->whereNull('demand_note_delivered_at');
        }) : null;
        $request->is_draft_delivered == "1" ? $this->properties->whereHas('assessment', function ($query) use ($request) {
            if ($request->dd_start_date && $request->dd_end_date) {
                $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::parse($request->dd_start_date), Carbon::parse($request->dd_end_date)]);
            } else {
                if ($request->dd_start_date) {
                    $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::parse($request->dd_start_date), Carbon::now()]);
                } else if ($request->dd_end_date) {
                    $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::now()->subYear(5),  Carbon::parse($request->dd_end_date)]);
                } else {
                    $query->whereYear('created_at', now()->format('Y'))->whereNotNull('demand_note_delivered_at');
                }
            }
        }) : null;

        //!$request->open_location_code ?: $this->properties->where('properties.id', $request->open_location_code);

        //!$request->open_location_code ?: $this->properties->where('properties.id', $request->open_location_code);

        !$request->digital_address ?: $this->properties->where('properties.id', $request->digital_address);

        !$request->old_digital_address ?: $this->properties->where('properties.id', $request->old_digital_address);

        !$request->is_completed ?: $this->properties->where('properties.is_completed', ($request->is_completed == 'yes' ? true : false));

        !$request->type ?: $this->properties->whereHas('types', function ($query) use ($request) {
            return $query->where('id', $request->type);
        });

        !$request->wall_material ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_wall_materials', $request->wall_material);
        });

        !$request->compound_name ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('compound_name', 'like', "%$request->compound_name%");
        });
        //rishu
        !$request->form_price ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->whereBetween('property_rate_without_gst', [ $request->form_price, $request->to_price])->whereYear('created_at', $request->demand_draft_year);
            // return $query->whereBetween('property_rate_without_gst', [ $request->form_price, $request->to_price])->whereYear('created_at', '<=', $request->demand_draft_year);
        });
        
        // !$request->landlord_name ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
        //     return $query->where('landlord_name', 'like', "%$request->landlord_name%");
        // });

        !$request->payee_name ?:$this->properties->whereHas('payments', function ($query) use ($request) {
            return $query->where('payee_name', 'like', "%$request->payee_name%");
        });
        
        !$request->payment_method ?:$this->properties->whereHas('payments', function ($query) use ($request) {
            return $query->where('payment_type', 'like', "%$request->payment_method%");
        });

        //rishu

        !$request->roof_material ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('roofs_materials', $request->roof_material);
        });

        !$request->property_dimension ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_dimension', $request->property_dimension);
        });

        !$request->value_added ?: $this->properties->whereHas('valueAdded', function ($query) use ($request) {
            return $query->where('id', $request->value_added);
        });

        !$request->property_inaccessible ?: $this->properties->whereHas('propertyInaccessible', function ($query) use ($request) {
            return $query->where('id', $request->property_inaccessible);
        });

        $this->properties->whereHas('landlord', function ($query) use ($request) {
            //
            if ($request->owner_first_name)
                $query = $query->where('first_name', 'like', "%{$request->owner_first_name}%");

            if ($request->owner_last_name)
                $query = $query->where('surname', 'like', "%{$request->owner_last_name}%");

            if ($request->input('mobile')) {
                $query->where('mobile_1', $request->input('mobile'));
            }

            return $query;
        });

        $this->properties->whereHas('occupancy', function ($query) use ($request) {
            //
            if ($request->tenant_first_name)
                $query = $query->where('tenant_first_name', 'like', "%{$request->tenant_first_name}%");

            if ($request->tenant_middle_name)
                $query = $query->where('middle_name', 'like', "%{$request->tenant_middle_name}%");

            if ($request->tenant_last_name)
                $query = $query->where('surname', 'like', "%{$request->tenant_last_name}%");


            return $query;
        });
        //!$request->landloard_name || $this->properties->orWhere('organization_name', 'like', '%' . $request->landloard_name . '%');

        !$request->telephone_number || $this->properties->whereHas('landlord', function ($query) use ($request) {
            return $query->where('mobile_1', 'like', '%' . $request->telephone_number . '%');
        });

        !$request->open_location_code || $this->properties->whereHas('geoRegistry', function ($query) use ($request) {
          return $query->where('open_location_code', $request->open_location_code);
        });

        !$request->name ?: $this->properties->whereHas('user', function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->name . '%');
        });

        if ($request->input('is_organization') == 1 && $request->input('organization_type')) {
            $this->properties->where('organization_type', $request->input('organization_type'))->where('is_organization', true);
        }

        if ($request->input('is_organization') && $request->input('is_organization') == 0) {
            $this->properties->where('is_organization', false);
        }


        $data['types'] = PropertyType::pluck('label', 'id')->prepend('Property Type', '');
        $data['wallMaterial'] = PropertyWallMaterials::pluck('label', 'id')->prepend('Wall Material', '');
        $data['roofMaterial'] = PropertyRoofsMaterials::pluck('label', 'id')->prepend('Roof Material', '');
        $data['propertyDimension'] = PropertyDimension::pluck('label', 'id')->prepend('Dimensions', '');
        $data['valueAdded'] = PropertyValueAdded::where('is_active', true)->pluck('label', 'id')->prepend('Value Added', '');
        $data['town'] = BoundaryDelimitation::distinct()->orderBy('section')->pluck('section', 'section')->prepend('Select Town', '');;

        if (request()->user()->hasRole('Super Admin')) {
            if(getPropertyWardPermission('property_filter'))
            {
                $user_assigned_property = UserAssignedProperty::where('user_id', \Auth::guard('admin')->user()->id)->pluck('ward_id');
                $data['ward'] = BoundaryDelimitation::whereIn('ward',$user_assigned_property)->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select All Ward', '');
            }
            else
            {
                $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select All Ward', '');
            }
            $data['district'] = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort()->prepend('Select District', '');
            $data['province'] = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort()->prepend('Select Province', '');
            // $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select All Ward', '');
            $data['chiefdom'] = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort()->prepend('Select Chiefdom', '');
            $data['constituency'] = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort()->prepend('Select Constituency', '');
        }
        elseif (request()->user()->hasRole('Super Admin Cus')) {
            // if(getPropertyWardPermission('property_filter'))
            // {
            //     dd("asdsa");
            //     $user_assigned_property = UserAssignedProperty::where('user_id', \Auth::guard('admin')->user()->id)->pluck('ward_id');
            //     $data['ward'] = BoundaryDelimitation::whereIn('ward',$user_assigned_property)->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select All Ward', '');
            // }
            // else
            // {
            //     $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select All Ward', '');
            // }
            $data['district'] = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort()->prepend('Select District', '');
            $data['province'] = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort()->prepend('Select Province', '');
            $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select All Ward', '');
            $data['chiefdom'] = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort()->prepend('Select Chiefdom', '');
            $data['constituency'] = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort()->prepend('Select Constituency', '');
        } else {
            $data['district'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('district')->pluck('district', 'district')->sort()->prepend('Select District', '');
            $data['province'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('province')->pluck('province', 'province')->sort()->prepend('Select Province', '');;
            $data['ward'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select All Ward', '');
            $data['chiefdom'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort()->prepend('Select Chiefdom', '');
            $data['constituency'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort()->prepend('Select Constituency', '');
        }
        
        $data['digital_address'] = PropertyGeoRegistry::distinct()->orderBy('property_id')->pluck('digital_address', 'digital_address')->sort()->prepend('Select Digital Address', '');

        $data['request'] = $request;

        $data['property_inaccessibles'] = PropertyInaccessible::where('is_active', 1)->pluck('label', 'id')->prepend('Select Property Inaccessible');

        $data['street_names'] = Property::distinct('street_name')->orderBy('street_name')->pluck('street_name', 'street_name');
        $data['street_numbers'] = Property::distinct('street_number')->orderBy('street_number')->pluck('street_number', 'street_number');
        $data['postcodes'] = Property::distinct('postcode')->orderBy('postcode')->pluck('postcode', 'postcode');
        $data['organizationTypes'] = $organizationTypes;

        //return view('admin.payments.bulk-receipt')->with(['properties' => $this->properties->latest()->get()]);

        if ($request->download_pdf_in_bulk && $request->download_pdf_in_bulk == 1) {
            $bulkDemand = new PropertyInBulk();
            return $bulkDemand->handle($this->properties, $request->demand_draft_year);
        }

        if ($request->download_stickers && $request->download_stickers == 1) {

            $stickers = new PropertyStickers();

            $nProperty = $this->properties->withAssessmentCalculation($request->input('demand_draft_year'))
                ->having('current_year_payment', '>', 0)
                ->having('total_payable_due', 0)
                ->orderBy('total_payable_due')
                ->get();
            return $stickers->handle($nProperty, $request);
        }

        if ($request->download_notice && $request->download_notice == 1) {

            //dd($this->properties->get());

            $notices = new PropertyNotice();

            return $notices->handle($this->properties->latest()->get());
        }

        if ($request->download_excel_in_bulk && $request->download_excel_in_bulk == 1) {
            
                $this->properties->with([
                    'assessment' => function ($query) use ($request) {
                        $query->whereYear('created_at', $request->input('demand_draft_year'))
                            ->with('categories', 'types', 'valuesAdded', 'dimension', 'wallMaterial', 'roofMaterial', 'zone', 'swimming');
                    },
                ]);
    
                $this->properties->whereHas('assessment', function ($query) use ($request) {
                    $query->whereYear('created_at', $request->input('demand_draft_year'));
                });
    
                return \Excel::download(new PropertyExport($this->properties), date('Y-m-d-H-i-s') . '-mod-properties.xlsx');


        }

      
        if ($request->bulk_demand && $request->bulk_demand == 2 && $this->properties->count() > 0) {

           return $coordinates = $this->getMapCoordinates();
            $points = $coordinates[0];
            $center = $coordinates[1];
            // return $this->properties->limit(5)->get();
            // foreach ($this->properties->whereHas('payments')->limit(5)->get() as $key => $prop) {
            //     return $prop;
            // }
            return view('admin.properties.poly-map', compact('points', 'center'));

            $polygons = $this->properties->latest()->get();
        }

        if (!isset($request->sort_by)) {
            $this->properties = $this->properties->latest('properties.updated_at');
        }
        if ($request->sort_by == "is_completed") {
            $this->properties = $this->properties->orderBy('is_completed', $request->sort_dir)->orderBy('is_draft_delivered', $request->sort_dir);
        }


        // $data['list_user'] = User::pluck('name', 'name')->toArray();
        //dd($this->properties->toSql());

        // $property_assesment_value = $this->properties->join('property_assessment_details', 'property_assessment_details.property_id', '=', 'properties.id')
        //     ->select('property_assessment_details.property_rate_without_gst')
        //     ->get();
        //     $valuess = array();

        //     foreach ($property_assesment_value as $key => $value) {
        //         array_push($valuess, $value->property_rate_without_gst);
        //     }
        //     $unique_values = array_unique($valuess);
        //     $sum_values = array_sum($unique_values);
        //     dd($sum_values);
        //     $data['sum_values'] = $sum_values;


// dd($request->all());
// dd($data);
// return  $this->properties->count();
        return $usersGrid
            ->create(['query' => $this->properties, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.properties.list', $data);
        //return view('admin.properties.list',$data);

    }


















































    public function listInaccessibleProperties()
    {
        $property = InaccessibleProperty::where('id','>',0)->get();
        return view('admin.properties.inaccessiblelist',compact('property'));
    }

    public function deleteMeter($id)
    {
        $registryMeter = RegistryMeter::findOrFail($id);

        if ($registryMeter->hasImage()) {
            unlink($registryMeter->getImage());
        }

        $registryMeter->delete();

        return response()->json(['success' => true]);
    }

    public function getMapCoordinates()
    {
       return $properties = $this->properties->latest(5)->get();
        $points = [];
        $center = null;

        if ($properties->count()) {
            foreach ($properties as $key => $property) {

                if (optional($property->geoRegistry)->dor_lat_long) {
                    $point = explode(', ', $property->geoRegistry->dor_lat_long);
                } else {
                    continue;
                }
                // if ($property->assessment->getCurrentYearTotalDue() - $property->assessment->getCurrentYearTotalPayment() != 0) {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
                // } else if ($property->assessment->getCurrentYearTotalDue() - $property->assessment->getCurrentYearTotalPayment() == 0) {
                //     $icon = "https://maps.google.com/mapfiles/ms/icons/green-dot.png";
                // } else if($property->user->assign_district_id != null){
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                // }
                // else{
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/pink-dot.png";
                // }
                if ($property->is_admin_created == 1) {
                    $icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                } else if ($property->assessment->getCurrentYearTotalDue() - $property->assessment->getCurrentYearTotalPayment() == 0) {
                    $icon = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
                } else {
                    $icon = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
                }

                // if ($property->is_admin_created == 1) {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                // } else if ($property->assessment->getCurrentYearTotalPayment() != 0) {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
                // } else {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
                // }
                if (count($point) == 2) {
                    $points[] = [$property->getAddress(), $point[0], $point[1], $key++, $icon];
                }

                if ($property->geoRegistry->dor_lat_long) {
                    $center = $property->geoRegistry->dor_lat_long;
                }
            }
        }

        return [json_encode($points), $center];
    }

    public function downloadPdf(Request $request)
    {
    //    return $request;
        $this->validate($request, [
            'properties' => 'required',

        ], [
            'properties.required' => 'Select at least one property'
        ]);

        $this->properties = Property::with([
            'landlord',
            'geoRegistry',
            'user',
            'districts'
        ]);
        if($request->delete_property){
            $this->properties->whereIn('properties.id', explode(',', $request->properties))->delete();
            return \Redirect::back()->with('success', 'Record deleted Successfully');
        }
        // return $request;
        if($request->send_sms){
            $allProperty=$request->properties;
            $arr=explode(',', $allProperty);
            // dd($arr);
             (new \App\Helper\CustomHelperBulkSms)->send_sms($arr);
            $this->sendPaymentRequestSMS(explode(',', $request->properties));
            return \Redirect::back()->with('success', 'SMS sent Successfully');
        }

        $this->properties = $this->properties->whereIn('properties.id', explode(',', $request->properties));
        
        if ($request->download_excel) {
            
            $this->properties->with([
                'assessment' => function ($query) use ($request) {
                    $query->whereYear('created_at', '<=',$request->input('demand_draft_year'))
                        ->with('categories', 'types', 'valuesAdded', 'dimension', 'wallMaterial', 'roofMaterial', 'zone', 'swimming');
                },
            ]);

            $this->properties->whereHas('assessment', function ($query) use ($request) {
                $query->whereYear('created_at', '<=', $request->input('demand_draft_year'));
            });
            // return $this->properties->get();
            return \Excel::download(new PropertyExport($this->properties), date('Y-m-d-H-i-s') . '-mod-properties.xlsx');
        }
        if ($request->download_stickers && $request->download_stickers == 1) {

            $stickers = new PropertyStickers();

            $nProperty = $this->properties->withAssessmentCalculation($request->input('demand_draft_year'))
                ->having('current_year_payment', '>', 0)
                ->having('total_payable_due', 0)
                ->orderBy('total_payable_due')
                ->get();

            return $stickers->handle($nProperty, $request);
        }

        if ($request->download_envelope) {
            $bulkDemand = new PropertyEnvpBulk();

            return $bulkDemand->handle($this->properties, $request->demand_draft_year);        
        }
        
        if ($request->download_pdf_ass_landlord) {
            $bulkDemand = new PropertyInBulk();
            return $bulkDemand->handleAssessmentLandLord($this->properties);        
        }
        if($request->vips_download){
            $this->properties->with([
                'assessment' => function ($query) use ($request) {
                    $query->whereYear('created_at', '<=',$request->input('demand_draft_year'))
                        ->with('categories', 'types', 'valuesAdded', 'dimension', 'wallMaterial', 'roofMaterial', 'zone', 'swimming');
                },
            ]);

            $this->properties->whereHas('assessment', function ($query) use ($request) {
                $query->whereYear('created_at', '<=', $request->input('demand_draft_year'));
            });

            return \Excel::download(new Vipsdownload($this->properties), date('Y-m-d-H-i-s') . '-mod-properties.xlsx');
        }
        // dd($this->properties->get());
      
      //for download_pdf
        // dd(222);
        $bulkDemand = new PropertyInBulk();

        return $bulkDemand->handle($this->properties, $request->demand_draft_year);
    }
    









    public function show(Request $request)
    {
        // return "list";   
        // dd($request->property);
        
        /* @var $property Property */
        $property = Property::findOrFail($request->property);
        // dd($property);

        // Generate current year assessment if missing
        $property->generateAssessments();

        // load sub modals
        $property->load([
            'images',
            'occupancy',
            'assessments' => function ($query) {
                $query->with('types', 'valuesAdded', 'categories')->latest();
            },
            'geoRegistry',
            'payments',
            'landlord',
            'propertyInaccessible'
        ]);


        if (request()->user()->hasRole('Super Admin')) {
            $data['town'] = BoundaryDelimitation::distinct()->orderBy('section')->pluck('section', 'section');
            $data['chiefdom'] = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort();
            $data['district'] = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort();
            $data['province'] = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort();
            $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
            $data['constituency'] = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort();
        } else {
            $data['town'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('section')->pluck('section', 'section');
            $data['chiefdom'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort();
            $data['district'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('district')->pluck('district', 'district')->sort();
            $data['province'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('province')->pluck('province', 'province')->sort();
            $data['ward'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('ward')->pluck('ward', 'ward')->sort();
            $data['constituency'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('constituency')->pluck('constituency', 'constituency')->sort();
        }
        $data['categories'] = PropertyCategory::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['types'] = PropertyType::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['window_types'] = PropertyWindowType::distinct()->where('is_active', 1)->pluck('label', 'id');
        //$data['window_types_values'] = PropertyWindowType::distinct()->where('is_active', 1)->pluck('value', 'id');
        $data['wall_materials'] = PropertyWallMaterials::distinct()->where('is_active', 1)->pluck('label','id');
        $data['sanitation'] = PropertySanitationType::pluck('label','id');
        $data['adjustment_values'] = Adjustment::pluck('name','id');
        //$data['wall_material_values'] = PropertyWallMaterials::distinct()->where('is_active', 1)->pluck('value', 'id');
        $data['roofs_materials'] = PropertyRoofsMaterials::distinct()->where('is_active', 1)->pluck('label', 'id');
        //$data['roofs_material_values'] = PropertyRoofsMaterials::distinct()->where('is_active', 1)->pluck('value', 'id');
        $data['property_dimension'] = PropertyDimension::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['value_added'] = PropertyValueAdded::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['property_use'] = PropertyUse::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['zone'] = PropertyZones::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['occupancy_type'] = ['Owned Tenancy' => 'Owned Tenancy', 'Rented House' => 'Rented House', 'Unoccupied House' => 'Unoccupied House'];
        $data['id_type'] = ['National ID' => 'National ID', 'Passport' => 'Passport', 'Driver’s License' => 'Driver’s License', 'Voter ID' => 'Voter ID', 'other' => 'Other'];
        $data['org_type'] = ['Government' => 'Government', 'NGO' => 'NGO', 'Business' => 'Business', 'School' => 'School', 'Religious' => 'Religious', 'Diplomatic Mission' => 'Diplomatic Mission', 'Hospital' => 'Hospital', 'Other' => 'Other'];
        $data['gender'] = ['m' => 'Male', 'f' => 'Female'];
        $data['usertitles'] = UserTitleTypes::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['title'] = 'Details';
        $data['property'] = $property;
        $data['selected_occupancies'] = $property->occupancies->pluck('occupancy_type')->toArray();

        $data['property_inaccessable'] = PropertyInaccessible::where('is_active', 1)->pluck('label', 'id')->toArray();
        $data['selected_property_inaccessable'] = $property->propertyInaccessible()->pluck('id')->toArray();
        $data['swimmings'] = Swimming::where('is_active', 1)->pluck('label', 'id')->prepend('Select', '')->toArray();
        
        $data['ab_2020_data']=PropertyAssessmentDetail::where('created_at', '>', '2019-12-12')->where('property_id',$request->property)->first();
        //$data['2020_data']=PropertyAssessmentDetail::skip(1)->take(1)->where('property_id',$request->property)->first();
       //dd($data['2020_data']);
        // dd($data['property']->assessments);
    
        return view('admin.properties.view', $data);
    }





























    public function downloadEnvelope($id, $year = null)
    {
        $year = !$year ? date('Y') : $year;

        $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        $paymentInQuarter = $property->getPaymentsInQuarter($year);
        $district = District::where('name', $property->district)->first();
        $pdf = \PDF::loadView('admin.envelope.single-envelope', compact('property', 'paymentInQuarter', 'assessment', 'district', 'year'));

        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '-envelope.pdf');

        //return view('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));
    }

    public function create()
    {
    }

    // public function assignProperty(Request $request)
    // {

    //     $data['title'] = 'Details';
    //     $data['request'] = $request;
    //     $data['assessmentOfficer'] = $assessmentUser = User::pluck('name', 'id')->prepend('Select Officer', '');

    //     return view('admin.properties.assign', $data);
    // }


    public function verifyLandlord(Request $request)
    {

        
        if($request->has('search'))
        {
            $search = $request->search;
            $landlords = LandlordDetail::where('verified',0)->where('property_id',$search)->simplePaginate(10);
        }else{
            $landlords = LandlordDetail::where('verified',0)->simplePaginate(10);
        }
        
        $state = 0;
        return view('admin.properties.verifylandlords', compact('landlords','state'));

    }


    public function verifyProperty(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $properties = Property::where('verified',0)->where('id',$search)->simplePaginate(10);
        }else{
            $properties = Property::where('verified',0)->simplePaginate(10);
        }
        
        $state = 0;
        return view('admin.properties.verifyproperties', compact('properties','state'));

    }

    public function rejectedLandlord(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $landlords = LandlordDetail::where('verified',-2)->where('property_id',$search)->simplePaginate(10);
        }else{
            $landlords = LandlordDetail::where('verified',-2)->simplePaginate(10);
        }
        $state = -2;
        return view('admin.properties.verifylandlords', compact('landlords','state'));

    }

    public function rejectedProperty(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $properties = Property::where('verified',-2)->where('id',$search)->simplePaginate(10);
        }else{
            $properties = Property::where('verified',-2)->simplePaginate(10);
        }
        $state = -2;
        return view('admin.properties.verifyproperties', compact('properties','state'));

    }

    public function approvedLandlord(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $landlords = LandlordDetail::where('verified',1)->where('property_id',$search)->simplePaginate(10);
        }else{
            $landlords = LandlordDetail::where('verified',1)->simplePaginate(10);
        }
        $state = 1;
        return view('admin.properties.verifylandlords', compact('landlords','state'));

    }

    public function approvedProperty(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $properties = Property::where('verified',1)->where('id',$search)->simplePaginate(10);
        }else{
            $properties = Property::where('verified',1)->simplePaginate(10);
        }
        $state = 1;
        return view('admin.properties.verifyproperties', compact('properties','state'));

    }


    public function approveLandlord($id, Request $request)
    {

        $landlords = LandlordDetail::where('id',$id)->first();
        $landlords->verified = 1;
        $landlords->first_name = $landlords->temp_first_name;
        $landlords->middle_name = $landlords->temp_middle_name;
        $landlords->surname = $landlords->temp_surname;
        $landlords->street_number = $landlords->temp_street_number;
        $landlords->street_numbernew = $landlords->temp_street_numbernew;
        $landlords->street_name = $landlords->temp_street_name;
        $landlords->email = $landlords->temp_email;
        $landlords->mobile_1 = $landlords->temp_mobile_1;
        $landlords->save();
        return redirect()->route('admin.verify.landlord');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }

    public function approveProperty($id, Request $request)
    {

        $property = Property::where('id',$id)->first();
        $property->verified = 1;
        //$property->street_number = $property->temp_street_number;
        $property->street_numbernew = $property->temp_street_numbernew;
        $property->street_name = $property->temp_street_name;
        $property->save();
        return redirect()->route('admin.verify.property');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }


    public function rejectLandlord($id, Request $request)
    {

        $landlords = LandlordDetail::where('id',$id)->first();
        $landlords->verified = -2;
        $landlords->save();
        return redirect()->route('admin.verify.landlord');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }

    public function rejectProperty($id, Request $request)
    {

        $property = Property::where('id',$id)->first();
        $property->verified = -2;
        $property->save();
        return redirect()->route('admin.verify.property');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }


    // public function saveAssignProperty(Request $request)
    // {
    //     $this->validate($request, [
    //         'assessment_officer' => 'required|exists:users,id',
    //       //  'dor_lat_long' => 'required'
    //     ]);

    //     // If User uploads a Excel file
    //     if($request->bulk_lat_long_file) {
    //         $users = \Excel::toArray(new ExcelImport, $request->file('bulk_lat_long_file'));
            
    //         $phones = array_map(function($iter){
    //             $numbers = array();
    //             foreach($iter as $key => $item){
    //                     $numbers[] = $item[0];
    //             }
    //             return $numbers;
    //         }, $users);
    //         array_walk_recursive($phones, function ($value, $key) use (&$numbers){
    //             $numbers[] = $value;
    //         }, $numbers);
           
    //         for($i=0;$i<count($numbers);$i++){
    //             $assessmentOfficer = User::findOrFail($request->assessment_officer);
    //             $property = $assessmentOfficer->properties()->firstOrNew(['id' => null]);
    //             $property->is_admin_created = 1;
    //             $property->save();
    //             $property->landlord()->firstOrCreate(["property_id" => $property->id]);
    //             $property->occupancy()->firstOrCreate(["property_id" => $property->id]);
    //             if ($property->assessment()->exists()) {

    //                 $assessment = $property->generateAssessments();
    //             } else {
    //                 $assessment = $property->assessment()->firstOrCreate(["property_id" => $property->id]);
    //             }

    //             $geoRegistry = $property->geoRegistry()->firstOrCreate(["property_id" => $property->id]);
    //             //dd($numbers[$i]);
                

    //                     $geoRegistry->fill(['dor_lat_long' => $numbers[$i]]);
    //                     $geoRegistry->save();

    //        }
    //     }else{
    //      // If User uploads a Excel file
       
    //     $assessmentOfficer = User::findOrFail($request->assessment_officer);
    //     // $property = $assessmentOfficer->properties()->firstOrNew(['id' => null]);
    //     $property = $assessmentOfficer->properties()->firstOrNew(['user_id' => $request->assessment_officer]);
    //     $property->is_admin_created = 1;
    //     $property->save();
    //     $property->landlord()->firstOrCreate(["property_id" => $property->id]);
    //     $property->occupancy()->firstOrCreate(["property_id" => $property->id]);
    //     if ($property->assessment()->exists()) {

    //         $assessment = $property->generateAssessments();
    //     } else {
    //         $assessment = $property->assessment()->firstOrCreate(["property_id" => $property->id]);
    //     }

    //     $geoRegistry = $property->geoRegistry()->firstOrCreate(["property_id" => $property->id]);
        
    //         $geoRegistry->fill(['dor_lat_long' => $request->dor_lat_long]);
    //         $geoRegistry->save();

        
    //     }
        
    //     return redirect()->back()->with('success', 'New Property Assigned Successfully!');
    // }

    public function assignProperty(Request $request)
    {
        
        // $data['properties'] = Property::join('property_geo_registry','properties.id','=','property_geo_registry.property_id')->get();
        $data['title'] = 'Details';
        $data['request'] = $request;
        $data['assessmentOfficer'] = $assessmentUser = User::pluck('name', 'id')->prepend('Select Officer', '');
        return view('admin.properties.assign', $data);
    }

    public function saveAssignProperty(Request $request)
    {
        
        $this->validate($request, [
            'assessment_officer' => 'required|exists:users,id',
          //  'dor_lat_long' => 'required'
        ]);

        // If User uploads a Excel file
        if($request->bulk_lat_long_file) {
            $users = \Excel::toArray(new ExcelImport, $request->file('bulk_lat_long_file'));
            
            $phones = array_map(function($iter){
                $numbers = array();
                foreach($iter as $key => $item){
                        $numbers[] = $item[0];
                }
                return $numbers;
            }, $users);
            array_walk_recursive($phones, function ($value, $key) use (&$numbers){
                $numbers[] = $value;
            }, $numbers);
            for($i=0;$i<count($numbers);$i++){
                $assessmentOfficer = User::findOrFail($request->assessment_officer);
                $property = $assessmentOfficer->properties()->firstOrNew(['id' => null]);
                $property->is_admin_created = 1;
                $property->save();
                $property->landlord()->firstOrCreate(["property_id" => $property->id]);
                $property->occupancy()->firstOrCreate(["property_id" => $property->id]);
                if ($property->assessment()->exists()) {

                    $assessment = $property->generateAssessments();
                } else {
                    $assessment = $property->assessment()->firstOrCreate(["property_id" => $property->id]);
                }

                $geoRegistry = $property->geoRegistry()->firstOrCreate(["property_id" => $property->id]);
                //dd($numbers[$i]);
                

                        $geoRegistry->fill(['dor_lat_long' => $numbers[$i]]);
                        $geoRegistry->save();

           }
        }else{
         // If User uploads a Excel file 
        // dd($st);
        // '8.438348672618078,   -13.214159701019526'
        $st=str_replace(' ','',$request->dor_lat_long);
         $getProperty = PropertyGeoRegistry::where('dor_lat_long',$st)->first();

         if ($getProperty == null) {
            return redirect()->back()->with('success', 'Data not found!');
         }
        //  dd($getProperty);
         $assessmentOfficer = User::findOrFail($request->assessment_officer);
         Property::where('id',$getProperty->property_id)->update(['user_id'=>$assessmentOfficer->id]);
         
        $property = $assessmentOfficer->properties()->firstOrNew(['user_id' => $request->assessment_officer]);
        $property->is_admin_created = 1;
        $property->save();
        $property->landlord()->firstOrCreate(["property_id" => $property->id]);
        $property->occupancy()->firstOrCreate(["property_id" => $property->id]);
        if ($property->assessment()->exists()) {
            $assessment = $property->generateAssessments();
        } else {
            $assessment = $property->assessment()->firstOrCreate(["property_id" => $property->id]);
        }

        $geoRegistry = $property->geoRegistry()->firstOrCreate(["property_id" => $property->id]);
        
            $geoRegistry->fill(['dor_lat_long' => $request->dor_lat_long]);
            $geoRegistry->save();


        }
        
        return redirect()->back()->with('success', 'New Property Assigned Successfully!');
    }
    public function destroy(Request $request)
    {
        /* @var $property Property */
        $property = Property::findOrFail($request->property);

        $property->landlord()->delete();
        $property->occupancy()->delete();
        //$property->assessments()->delete();
        $property->geoRegistry()->delete();
        $property->categories()->detach();
        $property->occupancies()->delete();
        $property->payments()->delete();
        $property->registryMeters()->delete();
        $property->propertyInaccessible()->detach();

        $property->delete();

        return redirect()->back()->with($this->setMessage('Property successfully deleted', true));
    }


    public function saveLandlord(Request $request)
    {
        //dd($request->all());
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'landlord_id' => "required|integer",
            'is_organization' => 'required|boolean',
            'organization_name' => 'nullable|string|max:255',
            'organization_type' => 'nullable|string|max:255',
            'organization_tin' => 'nullable|string|max:255',
            'organization_addresss' => 'nullable|string|max:255',
            'first_name' => 'required_if:is_organization,0|nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required_if:is_organization,0|nullable|string|max:255',
            'sex' => 'required_if:is_organization,0|nullable|string|max:255',
            'street_number' => 'required|string',
            'email' => "nullable|email",
            'street_name' => 'required|string|max:255|nullable',
            'tin' => 'nullable|string|max:255',
            'id_type' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'ward' => 'required|string',
            'constituency' => 'required|string',
            'section' => 'required|string|max:255',
            'chiefdom' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput()->with('id', 'landloard');
        }
        $data = $request->all();
        unset($data['landlord_id']);
        unset($data['property_id']);
        unset($data['organization_name']);
        unset($data['organization_tin']);
        unset($data['organization_type']);
        unset($data['organization_addresss']);


        $property = Property::findorFail($request->property_id);

        $property->organization_name = $request->organization_name;
        $property->organization_type = $request->organization_type;
        $property->organization_tin = $request->organization_tin;
        $property->organization_addresss = $request->organization_addresss;
        $property->is_organization = $request->is_organization;
        $property->save();

        $landlord = $property->landlord()->first();

        if ($request->hasFile('image')) {
            if ($landlord->hasImage()) {
                unlink($landlord->getImage());
            }
            $data['image'] = $request->image->store(Property::ASSESSMENT_IMAGE);
        }

        $landlord->fill($data);

        $landlord->save();


        return redirect()->back()->with('success', 'Landlord details Updated Successfully !');
    }

    public function sensSmsLandlord(Request $request){
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'landlord_id' => "required|integer",
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput()->with('id', 'landloard');
        }
        $this->sendPaymentRequestSMS([$request->property_id]);
        return \Redirect::back()->with('success', 'SMS sent Successfully');

    }



    public function saveProperty(Request $request)
    {
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'street_number' => 'required|string',
            'street_name' => 'required|string|max:255|nullable',
            'ward' => 'required|string',
            'constituency' => 'required|string',
            'section' => 'required|string|max:255',
            'chiefdom' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->with('id', 'property');
        }

        $data = $request->all();

        $property = Property::findorFail($request->property_id);
        $property->fill($data);
        $property->is_property_inaccessible = ($request->property_inaccessable && count($request->property_inaccessable)) ? true : false;
        $property->is_draft_delivered = $request->is_draft_delivered ? $request->is_draft_delivered : 0;
        $property->delivered_name = $request->delivered_name;
        $property->delivered_number = $request->delivered_number;

        if ($request->hasFile('delivered_image')) {
            $property->delivered_image = $request->delivered_image->store(Property::DELIVERED_IMAGE);
        }

        $property->save();

        $property->propertyInaccessible()->sync($request->property_inaccessable);

        return redirect()->back()->with('success', 'Property details Updated Successfully !');
    }

    public function saveOccupancy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "occupancy_id" => "required|integer",
            "property_id" => "required|integer",
            'occupancy_type' => 'nullable|array',
            'occupancy_type.*' => 'nullable|in:Owned Tenancy,Rented House,Unoccupied House',
            "tenant_first_name" => "nullable|string|max:50",
            "middle_name" => "nullable|string|max:40",
            "surname" => "nullable|string|max:30",
            "mobile_1" => "nullable|string|max:15",
            "mobile_2" => "nullable|string|max:15"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->with('id', 'occupancy');
        }

        //dd($request->ownertenantTitle);
        $data = $request->all();


         
        $property = Property::findorFail($request->input('property_id'));

        $occupancy = $property->occupancy()->first();
        $occupancy->fill($data);
        $occupancy->save();

        if (count(array_filter($request->occupancy_type))) {
            foreach (array_filter($request->occupancy_type) as $types) {
                $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
            }
            $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
        }

        return redirect()->back()->with('success', 'Occupancy details Updated Successfully !');
    }












    public function saveAssessment(Request $request)
    {
        // dd($request->all());
        $request->validate([
            "assessment_id" => "required|integer",
            "property_id" => "required|integer",
            'property_categories' => 'nullable|array',
            'property_categories.*' => 'nullable|exists:property_categories,id',
            // "property_types" => "required|array|max:2",
            // "property_types.*" => 'required|exists:property_types,id',
            "property_types_total" => "nullable|array|max:2",
            // "property_types_total.*" => 'nullable|exists:property_types,id',
            "property_wall_materials" => "required|integer",
            "roofs_materials" => "required|integer",
            "property_dimension" => "nullable|integer",
            "property_sanitation" => "nullable|integer",
            "property_value_added.*" => "required|exists:property_value_added,id",
            "property_use" => "required|integer",
            "zone" => "required|integer",
            'assessment_images_1' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'assessment_images_2' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $data = $request->except(['property_types', 'property_types_total', 'property_value_added', 'assessment_images_1', 'assessment_images_2']);
        // $data = $request->except(['property_types', 'property_value_added', 'assessment_images_1', 'assessment_images_2']);
        /* @var $property Property */
        // dd($data);

        $property = Property::findorFail($request->input('property_id'));

        /* @var $assessment PropertyAssessmentDetail */
        $assessment = $property->assessment()->findOrFail($request->input('assessment_id'));

        if ($request->hasFile('assessment_images_1')) {
            if ($assessment->hasImageOne()) {
                unlink($assessment->getImageOne());
            }
            $data['assessment_images_1'] = $request->file('assessment_images_1')->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->hasFile('assessment_images_2')) {
            if ($assessment->hasImageTwo()) {
                unlink($assessment->getImageTwo());
            }
            $data['assessment_images_2'] = $request->file('assessment_images_2')->store(Property::ASSESSMENT_IMAGE);
        }

        /* @var $assessment PropertyAssessmentDetail */
        $data['gated_community'] = $data['gated_community'] ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : null;
        $data['sanitation'] = $request->property_sanitation;
        // dd($data);


        $water_percentage = 0;
        $electrical_percentage = 0;
        $waster_precentage = 0;
        $market_percentage = 0;
        $hazardous_percentage = 0;
        $drainage_percentage = 0;
        $informal_settlement_percentage = 0;
        $easy_street_access_percentage = 0;
        $paved_tarred_street_percentage = 0;
        if(is_array($request->property_council_adjustments)){
            $adjustmentsArray = $request->property_council_adjustments;
            foreach($adjustmentsArray as $id)
            {
                $name_perc = Adjustment::where('id',$id)->pluck('name');
                if($id == 1){
                    $water_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 2){
                    $electrical_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 3){
                    $waster_precentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 4){
                    $market_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 5){
                    $hazardous_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 6){
                    $informal_settlement_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 7){
                    $easy_street_access_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 8){
                    $paved_tarred_street_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }else{
                    $drainage_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }
            }
        }




 // dd($data);

         // ----start for new counsil adjustment step -1 for insert update and delete and add ---------//
             //first find and delete previous data
             $srch=PropertyToCounsilGroupA::where('property_id',$property->id)->where('year',date("Y"))->first();

             if($srch){
                $dltall=PropertyToCounsilGroupA::where('property_id',$property->id)->where('year',date("Y"))->delete();
             }
             //insert new data
             $sumOfPercentage=0;
            if(@$request->newAdjustmentIds){
             foreach(@$request->newAdjustmentIds as $val ){
               // if(@$val->amount || @$val->value){
               //  }else{
                //find counsil adjustment details
                $adjustmentDetails=CounsilAdjustmentGroupA::where('id',$val)->first();
                // dd($adjustmentDetails);

                if($adjustmentDetails->sign=="+"){
                 $sumOfPercentage=$sumOfPercentage+(int)$adjustmentDetails->percentage;
                }else{
                  $sumOfPercentage=$sumOfPercentage-(int)$adjustmentDetails->percentage;
                }

                 $insData=new PropertyToCounsilGroupA;
                 $insData->property_id=$property->id;
                 $insData->adjustment_id=$val;
                 $insData->year=date("Y");
                 $insData->save();

             // }// end if for amount
            }//foreach end
           }

           $prevData=PropertyAssessmentDetail::where('property_id',$property->id)->whereYear('created_at',date("Y"))->first();
           $prevPercent=$prevData->total_adjustment_percent;
           // dd($prevPercent);



            //update the percentage to propert assement details table
            $updt=PropertyAssessmentDetail::where('property_id',$property->id)->whereYear('created_at',date("Y"))->update(['total_adjustment_percent'=>$sumOfPercentage]);

            $baseAmount=($data['property_rate_without_gst']*100)/ (100+($prevPercent));
            // dd($data['property_rate_without_gst']);

                $newRateAmount =  $baseAmount * ((100+($sumOfPercentage))/100); 
         // dd($baseAmount,$prevPercent,$data['property_rate_without_gst'],$sumOfPercentage,$newRateAmount);
              
            // ------------------------------------ end-1 -----------------------------------------









        $data['water_percentage'] = $water_percentage;
        $data['electrical_percentage'] = $electrical_percentage;
        $data['waste_management_percentage'] = $waster_precentage;
        $data['market_percentage'] = $market_percentage;
        $data['hazardous_precentage'] = $hazardous_percentage;
        $data['informal_settlement_percentage'] = $informal_settlement_percentage;
        $data['easy_street_access_percentage'] = $easy_street_access_percentage;
        $data['paved_tarred_street_percentage'] = $paved_tarred_street_percentage;
        $data['drainage_percentage'] = $drainage_percentage;
       

        $data['property_rate_without_gst'] = $newRateAmount;  //not req to div 1000 bcz already data comming is div by 1000 , means property_rate_without_gst already 1000 divided while api insert. and that data is comming here


        // dd("a",$data);
        $assessment->fill($data);
        $assessment->swimming()->associate($request->input('swimming_pool'));
        $assessment->save();

        $categories = getSyncArray($request->input('property_categories'), ['property_id' => $property->id]);

        $assessment->categories()->sync($categories);

        /* Property type (Habitat) multiple value */
        // $types = getSyncArray($request->input('property_types'), ['property_id' => $property->id]);
        // $assessment->types()->sync($types);

        /* Property type (typesTotal) multiple value */
        $typesTotal = getSyncArray($request->input('property_types_total'), ['property_id' => $property->id]);
        $assessment->typesTotal()->sync($typesTotal);

        /* Property value added multiple value */
        $valuesAdded = getSyncArray($request->input('property_value_added'), ['property_id' => $property->id]);
        $assessment->valuesAdded()->sync($valuesAdded);







       







        return redirect()->back()->with('success', 'Assessment details Updated Successfully!');
    }






















    public function saveGeoRegistry(Request $request)
    {
        /* @var $geoRegistry PropertyGeoRegistry */
        $geoRegistry = PropertyGeoRegistry::findOrFail($request->input('property_geo_registry_id'));

       $validator = Validator::make($request->all(), [
            'digital_address' => 'required|unique:property_geo_registry,digital_address,' . $geoRegistry->id,
            'dor_lat_long' => 'required'
        ]);


       $validator =  $validator->after(function ($validator) use ($request,$geoRegistry) {

            if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
                list($lat, $lng) = explode(',', $request->dor_lat_long);
                $openLocationCode = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
            }
            
            $geoExist = PropertyGeoRegistry::where('id', '<>', $geoRegistry->id)
            ->where('open_location_code', $openLocationCode)->first();

            if ($geoExist) {
                $validator->errors()->add('dor_lat_long', 'This dor lat lng is already exist');
            }

        });


        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
            // return $this->error(ApiStatusCode::VALIDATION_ERROR, [
            //     'errors' => $validator->errors()
            // ]);
        }

        $geoRegistry->fill($request->all());

        // if($request->digital_address!=''){
        //     $pos = strpos($request->digital_address, ' ');
        //     $locationCode = substr($request->digital_address, $pos+1);
        //     $latlngArr =  explode(' ', $locationCode);
        //     // echo $locationCode;
        //     // print_r( $latlngArr);
        //     // echo \OpenLocationCode\OpenLocationCode::encode($latlngArr[0], $latlngArr[1]);
        //     // exit;
        //     $geoRegistry->open_location_code = \OpenLocationCode\OpenLocationCode::encode($latlngArr[0], $latlngArr[1]);
        // }

        // Edited by KB 23-04-2019
        if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
            list($lat, $lng) = explode(',', $request->dor_lat_long);
            $geoRegistry->open_location_code = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
        }        


        $geoRegistry->save();

        /*$v = Validator::make($request->all(), [
            "georegistry_id" => "required|integer",
            "property_id" => "required|integer",
            'registry' => 'required|array',
            'registry.*.meter_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png','max:5120'] ,
            'registry.*.meter_number' => 'nullable|string|max:255',
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->with('id','geo-registry');
        }*/

        /* @var $property Property */
        $property = Property::findOrFail($request->property_id);

        if (count($request->registry) and is_array($request->registry)) {

            foreach (array_filter($request->registry) as $key => $registry) {

                if (isset($registry['id']) && $registry['id'] != null) {
                    $registryImageId[] = $registry['id'];
                    $regdata['number'] = $registry['meter_number'];

                    $registryMeters = $property->registryMeters()->where('id', $registry['id'])->first();
                    $regdata['image'] = $registryMeters->image;
                    if ($request->hasFile('registry.' . $key . '.meter_image')) {
                        if ($registryMeters->hasImage())
                            unlink($registryMeters->getImage());
                        $regdata['image'] = $registry['meter_image']->store(Property::METER_IMAGE);
                    }

                    $property->registryMeters()->where('id', $registry['id'])->update($regdata);
                } else {
                    if ($registry['meter_number'] != null) {

                        $Cregdata['number'] = $registry['meter_number'];
                        if ($request->hasFile('registry.' . $key . '.meter_image')) {

                            $Cregdata['image'] = $registry['meter_image']->store(Property::METER_IMAGE);
                        }
                        $property->registryMeters()->create($Cregdata);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Assessment details Updated Successfully!');
    }

    public function sendPaymentRequestSMS($property_ids){
        $properties = Property::whereIn('id', $property_ids)->get();

        foreach ($properties as $property) {
            $year = \Carbon\Carbon::parse($property->assessment->created_at)->format('Y');
            $dueamount = number_format($property->assessment->getCurrentYearTotalDue());
            $council_short_name = ($property->districts)? $property->districts->council_short_name: '';
            // $mobileNo = $property->landlord->mobile_1;

            if ($mobile_number = $property->landlord->mobile_1) {
                if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                    $property->landlord->notify(new PaymentRequestSMS($dueamount, $year, $council_short_name));
                }
            }


            // Twilio::message(
            //     $mobileNo,
            //     [
            //         "body" => "Dear Property Owner, you have arrears of Le {$dueamount} for your {$year} {$council_short_name} PropertyRate. Kindly make payments soon. Ignore if already paid or 76864861 for query.",
            //         "from" => config('services.twilio.alphanumeric_sender')
            //     ]
            // );
        }


    }


    public function updatePropertyAssessmentPensionDiscount($id,Request $request)
    {
        
        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->pensioner_discount = 1;
        $detail->save();
        return redirect()->back();
        
    }

    public function rejectPropertyAssessmentPensionDiscount($id,Request $request)
    {
        
        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->pensioner_discount = 0;
        $detail->is_rejected_pensioner = 1;
        $detail->save();
        return redirect()->back();
        
    }

    public function updatePropertyAssessmentDisabilityDiscount($id,Request $request)
    {
        
        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->disability_discount = 1;
        $detail->save();
        return redirect()->back();
        
    }

    public function rejectPropertyAssessmentDisabilityDiscount($id,Request $request)
    {
        
        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->disability_discount = 0;
        $detail->is_rejected_disability = 1;
        $detail->save();
        return redirect()->back();
        
    }


    public function loadGMap()
    {
        return view('admin.properties.loadmap');
    }

    public function PropertyBckup(){
        

        $property_ids=array();
            $property_assesments = array();
 
 

       
       for ($i=0; $i <count($property_ids) ; $i++) { 
        // dd($property_ids[$i]);
        $get_Property_details = PropertyPayment::where('property_id', '=', $property_ids[$i])->whereYear('created_at', '=', '2021')->get();
        // $get_Property_details_2022 = PropertyPayment::where('property_id', '=', $property_ids[$i])->whereYear('created_at', '=', '2022')->get();

        // dd($get_Property_details_2022);
        // if (count($get_Property_details_2022)) {
        //     echo 'property '.$property_ids[$i].' already there';
        //     echo '<br>';
        // }

        // dd($get_Property_details);
        if (count($get_Property_details) == 0) {
            // dd('no data');
            echo 'property '.$property_ids[$i].' not found\n';
        }
        else{

            foreach ($get_Property_details as $key => $value) {
                {
                    $ins = [];
                   
                    $ins['property_rate_without_gst'] =$property_assesments[$i];
                  
                    // $ins['last_printed_at'] = Carbon::now();
                    $ins['window_type_type'] = $value->window_type_type;
                    // $ins['created_at'] = Carbon::now();
                    // $ins['updated_at'] = Carbon::now();
                
                     // dd($ins);
                    try {
                    DB::table('property_assessment_details')->where('property_id',$property_ids[$i])->whereYear('created_at',2021)->update($ins);
                        
                        //code...
                    } catch (\Throwable $th) {
                        dd($th);
                        //throw $th;
                    }
                    // PropertyAssessmentDetail::create($ins);
                }

        }
      
       }
        
      
        
       
       echo "property".$i."added";
       " <h3> ... </h3>\n";  


    }
    
   

    // dd('all properties added');

   

}

public function PropertyAssesmentBckup(){
        

    $property_ids=array(16957,
    16958,
    16959,
    16960,
    16961,
    16962,
    16963,
    16964,
    16965,
    16966,
    16967,
    16968,
    16969,
    16970,
    16971,
    16972,
    16973,
    16974,
    16975,
    16976,
    16977,
    16978,
    16979,
    16980,
    16981,
    16982,
    16983,
    16984,
    16985,
    16986,
    16987,
    16988,
    16989,
    17004,
    17005,
    17006,
    17007,
    17008,
    17009,
    17010,
    17011,
    17012,
    17013,
    17014,
    17015,
    17016,
    17017,
    17018,
    17019,
    17020,
    17021,
    17022,
    17023,
    17024,
    17025,
    17026,
    17027,
    17028,
    17029,
    17030,
    17031,
    17032,
    17033,
    17034,
    17035);
        // $property_assesments = array();

    //      $property_ids=array('17',
    // '18',
    // '19',
    // '20',);



   
   for ($i=0; $i <count($property_ids) ; $i++) { 
    // dd($property_ids[$i]);
    $get_Property_details = PropertyAssessmentDetail::where('property_id', '=', $property_ids[$i])->whereYear('created_at', '=', '2021')->get();
    $get_Property_details_2022 = PropertyAssessmentDetail::where('property_id', '=', $property_ids[$i])->whereYear('created_at', '=', '2019')->get();

    // dd(count($get_Property_details_2022));
    // if (count($get_Property_details_2022)) {
    //     echo 'property '.$property_ids[$i].' already there';
    //     echo '<br>';
    // }

    // dd($get_Property_details);
    if (count($get_Property_details) == 0) {
        // dd('no data');
        echo 'property '.$property_ids[$i].' not found\n';
    }
    else{

        if (count($get_Property_details_2022) == 0) {
            foreach ($get_Property_details as $key => $value) {
                {
                    $ins = [];
                   
                  
                    $ins['last_printed_at'] = Carbon::createFromDate(2019, 8, 14, 'America/Chicago');
                    $ins['property_id'] = $value->property_id;
                    $ins['property_categories'] = $value->property_categories;
                    $ins['property_wall_materials'] = $value->property_wall_materials;
                    $ins['roofs_materials'] = $value->roofs_materials;
                    $ins['property_window_type'] = $value->property_window_type;
                    $ins['property_dimension'] = $value->property_dimension;
                    $ins['length'] = $value->length;
                    $ins['breadth'] = $value->breadth;
                    $ins['square_meter'] = $value->square_meter;
                    $ins['property_rate_without_gst'] = $value->property_rate_without_gst;
                    // $ins['property_gst'] = $value->property_gst;
                    // $ins['property_rate_with_gst'] = $value->property_rate_with_gst;
                    // $ins['property_use'] = $value->property_use;
                    
                    $ins['property_rate_without_gst'] = 0;
                    $ins['property_gst'] = 0;
                    $ins['property_rate_with_gst'] = 0;

                    $ins['property_use'] = $value->property_use;
                    $ins['zone'] = $value->zone;
                    $ins['no_of_mast'] = $value->no_of_mast;
                    $ins['no_of_shop'] = $value->no_of_shop;
                    $ins['no_of_compound_house'] = $value->no_of_compound_house;
                    $ins['compound_name'] = $value->compound_name;
                    $ins['compound_name'] = $value->compound_name;
                    $ins['swimming_id'] = $value->swimming_id;
                    $ins['assessment_images_2'] = $value->assessment_images_2;
                    $ins['assessment_images_1'] = $value->assessment_images_1;
                    $ins['demand_note_delivered_at'] = $value->demand_note_delivered_at;
                    $ins['demand_note_recipient_name'] = $value->demand_note_recipient_name;
                    $ins['demand_note_recipient_mobile'] = $value->demand_note_recipient_mobile;
                    $ins['demand_note_recipient_photo'] = $value->demand_note_recipient_photo;
                    $ins['window_type_type'] = $value->window_type_type;
                    $ins['total_adjustment_percent'] = $value->total_adjustment_percent;
                    $ins['group_name'] = $value->group_name;
                    $ins['mill_rate'] = $value->mill_rate;
                    $ins['wall_material_percentage'] = $value->wall_material_percentage;
                    $ins['wall_material_type'] = $value->wall_material_type;
                    $ins['roof_material_percentage'] = $value->roof_material_percentage;
                    $ins['roof_material_type'] = $value->roof_material_type;
                    $ins['value_added_percentage'] = $value->value_added_percentage;
                    $ins['value_added_type'] = $value->value_added_type;
                    $ins['window_type_percentage'] = $value->window_type_percentage;
                    $ins['is_map_set'] = $value->is_map_set;
                    $ins['water_percentage'] = $value->water_percentage;
                    $ins['electricity_percentage'] = $value->electricity_percentage;
                    $ins['waste_management_percentage'] = $value->waste_management_percentage;
                    $ins['market_percentage'] = $value->market_percentage;
                    $ins['hazardous_precentage'] = $value->hazardous_precentage;
                    $ins['drainage_percentage'] = $value->drainage_percentage;
                    $ins['informal_settlement_percentage'] = $value->informal_settlement_percentage;
                    $ins['easy_street_access_percentage'] = $value->easy_street_access_percentage;
                    $ins['paved_tarred_street_percentage'] = $value->paved_tarred_street_percentage;
                    $ins['pensioner_discount'] = $value->pensioner_discount;
                    $ins['disability_discount'] = $value->disability_discount;
                    $ins['sanitation'] = $value->sanitation;
                    $ins['is_rejected_pensioner'] = $value->is_rejected_pensioner;
                    $ins['is_rejected_disability'] = $value->is_rejected_disability;
                    $ins['council_group_name'] = $value->council_group_name;
                    $ins['arrear_calc'] = $value->arrear_calc;
                    $ins['created_at'] = Carbon::createFromDate(2019, 8, 14, 'America/Chicago');
                    $ins['updated_at'] = Carbon::createFromDate(2019, 8, 14, 'America/Chicago');
                
                     // dd($ins);
                    try {
                    DB::table('property_assessment_details')->where('property_id',$property_ids[$i])->whereYear('created_at',2021)->insert($ins);
                        
                        //code...
                    } catch (\Throwable $th) {
                        dd($th);
                        //throw $th;
                    }
                    // PropertyAssessmentDetail::create($ins);
                }
    
        }
      
       }

        
   }
    
  
    
   
   echo "property".$i."added";
   " <h3> ... </h3>\n"; 
   echo "<br>"; 


}



// dd('all properties added');



}
public function import(Request $request){
    Excel::import(new BulkImport,request()->file('file'));
       
    return back();
    dd($request->all());
     // If User uploads a Excel file
     
}

public function importExportView()
{
   return view('importexport');
}

public function PropertyAssesmentBckupGeo(){
    $getProperty = PropertyGeoRegistry::get();
        foreach($getProperty as $data){
            $str = str_replace(' ','',$data->dor_lat_long);
            // dd($data->id);
            PropertyGeoRegistry::where('id',$data->id)->update(['dor_lat_long'=>$str]);
        }
        dd("operation succesfully");

}


public function PropertyAssesmentBckupTWO(){
        

    $property_ids=array();
        // $property_assesments = array();

    //      $property_ids=array('17',
    // '18',
    // '19',
    // '20',);



   
//    for ($i=0; $i <count($property_ids) ; $i++) { 
    // dd($property_ids[$i]);
    // $get_Property_details = PropertyAssessmentDetail::whereYear('created_at', '=', '2021')->get();
    $get_Property_details_2020 = PropertyAssessmentDetail::whereYear('created_at', '=', '2020')->get();

    //  dd(count($get_Property_details_2020));
    // if (count($get_Property_details_2020)) {
    //     echo 'property '.$property_ids[$i].' already there';
    //     echo '<br>';
    // }

    if (count($get_Property_details_2020) > 0){
        foreach ($get_Property_details_2020 as $key => $value) {
            echo($value->property_id);
            echo('<br>');

            $ins = [];
                   
            $ins['property_rate_without_gst'] =$value->property_rate_without_gst;
          
            try {
            DB::table('property_assessment_details')->where('property_id',$value->property_id)->whereYear('created_at',2021)->update($ins);
            DB::table('property_assessment_details')->where('property_id',$value->property_id)->whereYear('created_at',2022)->update($ins);
                
                //code...
            } catch (\Throwable $th) {
                dd($th);
                //throw $th;
            }

            
        }
    }

    // dd($get_Property_details);
    // if (count($get_Property_details) == 0) {
    //     // dd('no data');
    //     echo 'property '.$property_ids[$i].' not found\n';
    // }
    // else{

//         if (count($get_Property_details_2019) > 0) {
//             foreach ($get_Property_details as $key => $value) {
//                     $ins = [];
                   
                  
//                     $ins['last_printed_at'] = Carbon::createFromDate(2022, 8, 14, 'America/Chicago');;
//                     $ins['property_id'] = $value->property_id;
//                     $ins['property_categories'] = $value->property_categories;
//                     $ins['property_wall_materials'] = $value->property_wall_materials;
//                     $ins['roofs_materials'] = $value->roofs_materials;
//                     $ins['property_window_type'] = $value->property_window_type;
//                     $ins['property_dimension'] = $value->property_dimension;
//                     $ins['length'] = $value->length;
//                     $ins['breadth'] = $value->breadth;
//                     $ins['square_meter'] = $value->square_meter;
//                     $ins['property_rate_without_gst'] = 0;
//                     $ins['property_gst'] = 0;
//                     $ins['property_rate_with_gst'] = 0;
//                     $ins['property_use'] = $value->property_use;
//                     $ins['zone'] = $value->zone;
//                     $ins['no_of_mast'] = $value->no_of_mast;
//                     $ins['no_of_shop'] = $value->no_of_shop;
//                     $ins['no_of_compound_house'] = $value->no_of_compound_house;
//                     $ins['compound_name'] = $value->compound_name;
//                     $ins['compound_name'] = $value->compound_name;
//                     $ins['swimming_id'] = $value->swimming_id;
//                     $ins['assessment_images_2'] = $value->assessment_images_2;
//                     $ins['assessment_images_1'] = $value->assessment_images_1;
//                     $ins['demand_note_delivered_at'] = $value->demand_note_delivered_at;
//                     $ins['demand_note_recipient_name'] = $value->demand_note_recipient_name;
//                     $ins['demand_note_recipient_mobile'] = $value->demand_note_recipient_mobile;
//                     $ins['demand_note_recipient_photo'] = $value->demand_note_recipient_photo;
//                     $ins['window_type_type'] = $value->window_type_type;
//                     $ins['total_adjustment_percent'] = $value->total_adjustment_percent;
//                     $ins['group_name'] = $value->group_name;
//                     $ins['mill_rate'] = $value->mill_rate;
//                     $ins['wall_material_percentage'] = $value->wall_material_percentage;
//                     $ins['wall_material_type'] = $value->wall_material_type;
//                     $ins['roof_material_percentage'] = $value->roof_material_percentage;
//                     $ins['roof_material_type'] = $value->roof_material_type;
//                     $ins['value_added_percentage'] = $value->value_added_percentage;
//                     $ins['value_added_type'] = $value->value_added_type;
//                     $ins['window_type_percentage'] = $value->window_type_percentage;
//                     $ins['is_map_set'] = $value->is_map_set;
//                     $ins['water_percentage'] = $value->water_percentage;
//                     $ins['electricity_percentage'] = $value->electricity_percentage;
//                     $ins['waste_management_percentage'] = $value->waste_management_percentage;
//                     $ins['market_percentage'] = $value->market_percentage;
//                     $ins['hazardous_precentage'] = $value->hazardous_precentage;
//                     $ins['drainage_percentage'] = $value->drainage_percentage;
//                     $ins['informal_settlement_percentage'] = $value->informal_settlement_percentage;
//                     $ins['easy_street_access_percentage'] = $value->easy_street_access_percentage;
//                     $ins['paved_tarred_street_percentage'] = $value->paved_tarred_street_percentage;
//                     $ins['pensioner_discount'] = $value->pensioner_discount;
//                     $ins['disability_discount'] = $value->disability_discount;
//                     $ins['sanitation'] = $value->sanitation;
//                     $ins['is_rejected_pensioner'] = $value->is_rejected_pensioner;
//                     $ins['is_rejected_disability'] = $value->is_rejected_disability;
//                     $ins['council_group_name'] = $value->council_group_name;
//                     $ins['arrear_calc'] = $value->arrear_calc;
//                     $ins['created_at'] = Carbon::createFromDate(2019, 8, 14, 'America/Chicago');;
//                     $ins['updated_at'] = Carbon::createFromDate(2019, 8, 14, 'America/Chicago');;
                
//                     try {
//                     DB::table('property_assessment_details')->insert($ins);
                        
//                     } catch (\Throwable $th) {
//                         dd($th);
//                     }
                  
//                 echo "property".$value->property_id."added";
//    " <h3> ... </h3>\n"; 
//    echo "<br>"; 
    
//         }
      
//        }

        
//    }
    
  
    
   
   


// }



// dd('all properties added');



}











public function demand_note_page($id){
    // dd($id);
     // dd($id,$year,$request->all());

       //calculatd all datas for all years
       //-----------------get all details o sent in sms for each property
        $year="2023";
        $blankArray=[];

                  $property = Property::find($id);

                //part 1
                    $CurrentYearAssessmentAmount = 0;
                    $PastPayableDue = 0;
                    $Penalty = 0;
                    $CurrentYearTotalPayment2021 = 0;
                    $CurrentYearTotalDue = 0;
                    $CurrentYearTotalDue2021 = 0;

                    $PastPayableDue2022 = 0;
                    $CurrentYearTotalPayment2022 = 0;
                    $Penalty2022 = 0;
                    $CurrentYearTotalDue2022 = 0;
                    $PastPayableDue = 0;

                 // part -2
                for($i=0; $i<count(@$property->assessmentHistory); $i++){
   
                    $AssessmentYear = $property->assessmentHistory[$i]->created_at->year;
                    $CurrentYearAssessmentAmount = $property->assessmentHistory[$i]->current_year_assessment_amount;
                    if($PastPayableDue > 0){
                        $Penalty = $PastPayableDue*0.25;
                    }
                    else{
                        $Penalty =0;
                    }
                    
                    $CurrentYearTotalPayment = $property->assessmentHistory[$i]->getCurrentYearTotalPayment();
                    $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                   
                    
                  $arr2=[];
                  $arr2['propertyId']= $id;
    
                 $arr2['AssessmentYear']=$AssessmentYear ;
                 $arr2['CurrentYearAssessmentAmount']= $CurrentYearAssessmentAmount;
                 $arr2['PastPayableDue']= $PastPayableDue;
                 $arr2['Penalty']= $Penalty;
                 $arr2['CurrentYearTotalPayment']= $CurrentYearTotalPayment;
                 $arr2['CurrentYearTotalDue']= $CurrentYearTotalDue;

                 if($AssessmentYear!="2023"){
                       $PastPayableDue = $CurrentYearTotalDue;
                     }

                 array_push($blankArray,$arr2);
               
                }
                // dd($AssessmentYear,$CurrentYearAssessmentAmount,$Penalty,$PastPayableDue,$CurrentYearTotalPayment,$CurrentYearTotalDue,$blankArray);
                foreach($blankArray as $val){
                    if($val['AssessmentYear']==$year){
                        // dd($val,$year);
                        @$yr=$year;
                        $assed_value=$val['CurrentYearAssessmentAmount'];
                        $arrays=$val['PastPayableDue'];
                        $panelty_new=$val['Penalty'];
                        $payments=$val['CurrentYearTotalPayment'];
                        $due=$val['CurrentYearTotalDue'];

             }
                }


// dd(
// $yr,
// $assed_value,
// $arrays,
// $panelty_new,
// $payments,
// $due);
       



        // @$yr=$request->year;
        // $assed_value=$request->assed_value;
        // $arrays=$request->arrays;
        // $panelty_new=$request->panelty;
        // $payments=$request->payments;
        // $due=$request->due;
        $year = !$year ? date('Y') : $year;

        $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

        $assessment->setPrinted();

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        $paymentInQuarter = $property->getPaymentsInQuarter($year);
        $district = District::where('name', $property->district)->first();
        $data['type']="sms";

        return view ('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district','yr','assed_value','arrays','panelty_new','payments','due','data'));

        // $pdf = \PDF::loadView('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district','yr','assed_value','arrays','panelty_new','payments','due'));  //3


         // return view('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district','yr','assed_value','arrays','panelty_new','payments','due'));  //1

        // return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '.pdf');  //2
}














public function demand_note_page_pdf($id){

$year="2023";
$blankArray=[];

//update demand downlode status against a proprty id in SmsToProperty table
$updateSmsToProperty=SmsToProperty::where('property_id',$id)->update(['demand_downlode'=>'Y']);

                  $property = Property::find($id);

                //part 1
                    $CurrentYearAssessmentAmount = 0;
                    $PastPayableDue = 0;
                    $Penalty = 0;
                    $CurrentYearTotalPayment2021 = 0;
                    $CurrentYearTotalDue = 0;
                    $CurrentYearTotalDue2021 = 0;

                    $PastPayableDue2022 = 0;
                    $CurrentYearTotalPayment2022 = 0;
                    $Penalty2022 = 0;
                    $CurrentYearTotalDue2022 = 0;
                    $PastPayableDue = 0;

                 // part -2
                for($i=0; $i<count(@$property->assessmentHistory); $i++){
   
                    $AssessmentYear = $property->assessmentHistory[$i]->created_at->year;
                    $CurrentYearAssessmentAmount = $property->assessmentHistory[$i]->current_year_assessment_amount;
                    if($PastPayableDue > 0){
                        $Penalty = $PastPayableDue*0.25;
                    }
                    else{
                        $Penalty =0;
                    }
                    
                    $CurrentYearTotalPayment = $property->assessmentHistory[$i]->getCurrentYearTotalPayment();
                    $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                   
                    
                  $arr2=[];
                  $arr2['propertyId']= $id;
    
                 $arr2['AssessmentYear']=$AssessmentYear ;
                 $arr2['CurrentYearAssessmentAmount']= $CurrentYearAssessmentAmount;
                 $arr2['PastPayableDue']= $PastPayableDue;
                 $arr2['Penalty']= $Penalty;
                 $arr2['CurrentYearTotalPayment']= $CurrentYearTotalPayment;
                 $arr2['CurrentYearTotalDue']= $CurrentYearTotalDue;

                 if($AssessmentYear!="2023"){
                       $PastPayableDue = $CurrentYearTotalDue;
                     }

                 array_push($blankArray,$arr2);
               
                }
                // dd($AssessmentYear,$CurrentYearAssessmentAmount,$Penalty,$PastPayableDue,$CurrentYearTotalPayment,$CurrentYearTotalDue,$blankArray);
                foreach($blankArray as $val){
                    if($val['AssessmentYear']==$year){
                        // dd($val,$year);
                        @$yr=$year;
                        $assed_value=$val['CurrentYearAssessmentAmount'];
                        $arrays=$val['PastPayableDue'];
                        $panelty_new=$val['Penalty'];
                        $payments=$val['CurrentYearTotalPayment'];
                        $due=$val['CurrentYearTotalDue'];

             }
                }


// dd(
// $yr,
// $assed_value,
// $arrays,
// $panelty_new,
// $payments,
// $due);
       



        // @$yr=$request->year;
        // $assed_value=$request->assed_value;
        // $arrays=$request->arrays;
        // $panelty_new=$request->panelty;
        // $payments=$request->payments;
        // $due=$request->due;
        $year = !$year ? date('Y') : $year;

        $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

        $assessment->setPrinted();

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        $paymentInQuarter = $property->getPaymentsInQuarter($year);
        $district = District::where('name', $property->district)->first();
       
        $pdf = \PDF::loadView('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district','yr','assed_value','arrays','panelty_new','payments','due'));  //3


         // return view('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district','yr','assed_value','arrays','panelty_new','payments','due'));  //1

        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '.pdf');  //2
}







public function sms_status(){
    // dd(1);
    $data['data']=SmsToProperty::orderBy('id','desc')->get();
    return view('admin.properties.sms_status')->with($data);
}


















public function download_payment_excel(Request $request){
    // dd(1);
    $allProperty=Property::where('ward',$request->ward)->orderBy('id','desc')->get();

    $allDataFromCounsilTable=PropertyToCounsilGroupA::pluck('property_id')->toArray();
    $Unique=array_unique($allDataFromCounsilTable);
    // dd($allProperty,$Unique);

     $abc=$allProperty;
    // dd($abc);


    $data = '';
    
      $data .='<table>
      <tr>
      <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Property Id</th>
       <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Ward</th>
      <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Landlord Name</th>
      <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Mobile</th>
      <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Assesment Aount</th>
       <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Assesment Year</th>
   
      </tr>
      ';
      foreach (@$allProperty as $value) {
    
       $a="00";
       $year="2023";
       $blankArray=[];


                //part 1
                    $CurrentYearAssessmentAmount = 0;
                    $PastPayableDue = 0;
                    $Penalty = 0;
                    $CurrentYearTotalPayment2021 = 0;
                    $CurrentYearTotalDue = 0;
                    $CurrentYearTotalDue2021 = 0;

                    $PastPayableDue2022 = 0;
                    $CurrentYearTotalPayment2022 = 0;
                    $Penalty2022 = 0;
                    $CurrentYearTotalDue2022 = 0;
                    $PastPayableDue = 0;

                 // part -2
                for($i=0; $i<count(@$value->assessmentHistory); $i++){
   
                    $AssessmentYear = $value->assessmentHistory[$i]->created_at->year;
                    $CurrentYearAssessmentAmount = $value->assessmentHistory[$i]->current_year_assessment_amount;
                    if($PastPayableDue > 0){
                        $Penalty = $PastPayableDue*0.25;
                    }
                    else{
                        $Penalty =0;
                    }
                    
                    $CurrentYearTotalPayment = $value->assessmentHistory[$i]->getCurrentYearTotalPayment();
                    $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                   
                    
                  $arr2=[];
                  $arr2['propertyId']= $value->id;
    
                 $arr2['AssessmentYear']=$AssessmentYear ;
                 $arr2['CurrentYearAssessmentAmount']= $CurrentYearAssessmentAmount;
                 $arr2['PastPayableDue']= $PastPayableDue;
                 $arr2['Penalty']= $Penalty;
                 $arr2['CurrentYearTotalPayment']= $CurrentYearTotalPayment;
                 $arr2['CurrentYearTotalDue']= $CurrentYearTotalDue;

                 if($AssessmentYear!="2023"){
                       $PastPayableDue = $CurrentYearTotalDue;
                     }

                 array_push($blankArray,$arr2);
               
                }

                foreach($blankArray as $val){
                    if($val['AssessmentYear']=="2023"){
                        // dd($val,$year);
                        @$yr=$year;
                        $assed_value=$val['CurrentYearAssessmentAmount'];
                        $arrays=$val['PastPayableDue'];
                        $panelty_new=$val['Penalty'];
                        $payments=$val['CurrentYearTotalPayment'];
                        $due=$val['CurrentYearTotalDue'];

                    }
                }
        
        

        $data .= '
        <tr>
        <td style="border:1px solid black;">'.@$value->id.'</td>
         <td style="border:1px solid black;">'.@$request->ward.'</td>
        <td style="border:1px solid black;">'.@$value->landlord->first_name.' '.@$value->landlord->middle_name. ' '.@$value->landlord->surname  .'</td>
        <td style="border:1px solid black;">'.@$value->landlord->mobile_1.'</td>
        <td style="border:1px solid black;">'.@$assed_value.'</td>
         <td style="border:1px solid black;">'.'2023'.'</td>

        </tr>';
      }
      $data .= '</table>';
    
      //dd($data);
    header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=details.xls");  
    header("Pragma: no-cache"); 
    header("Expires: 0");
    //dd($data); 
    echo $data;

}
public function download_waybill_excel(Request $request)
{
    // return $request;
   

    // Retrieve the properties with the necessary relationships, sorted by ID in descending order, with offset and limit
    $propertiesQuery = Property::with([
        'user',
        'landlord',
        'assessments' => function ($query) {
                $query->with('types', 'valuesAdded', 'categories')->latest();
            },
        'geoRegistry',
        'user',
        'occupancies',
        'propertyInaccessible',
        'payments',
        'districts',
        'images',
        'assessment',
    ])
    ->whereHas('assessment', function ($query) use ($request) {

        if ($request->filled('demand_draft_year')) {
            $query->whereYear('created_at', $request->demand_draft_year);
            // $query->whereYear('created_at', '<=', $request->demand_draft_year);

        }
            // dd($request->demand_draft_year);
        if ($request->filled('is_printed')) {

            if ($request->input('is_printed') === '1') {
                $query->whereNotNull('last_printed_at');
            }

            if ($request->input('is_printed') === '0') {
                $query->whereNull('last_printed_at');
            }
        }

        if ($request->is_gated_community) {
            $query->where('gated_community', $request->gated_community);
        }

    })
    ->whereHas('districts', function($query) {
        $query->where('id', 13);
    }) ->whereIn('id', json_decode($request->page_property_ids))
    ->get();
    $batch = $request->page_no; // Replace 'XXX' with the default value or logic to fetch the batch value
    $ward = $request->ward_no; // Replace 'XXX' with the default value or logic to fetch the ward value
    // return $propertiesQuery->get();


    // $abc = $propertiesQuery[0]->assessments;

  
                                // $CurrentYearAssessmentAmount = 0;
                                // $PastPayableDue = 0;
                                // $Penalty = 0;
                                // $CurrentYearTotalPayment2021 = 0;
                                // $CurrentYearTotalDue = 0;
                                // $CurrentYearTotalDue2021 = 0;

                                // $PastPayableDue2022 = 0;
                                // $CurrentYearTotalPayment2022 = 0;
                                // $Penalty2022 = 0;
                                // $CurrentYearTotalDue2022 = 0;
                                // $PastPayableDue = 0;
                                
                                $due_2024 = [];    
                        for ($j=0; $j <count($propertiesQuery) ; $j++) { 
                            
                            $CurrentYearAssessmentAmount = 0;
                                $PastPayableDue = 0;
                                $Penalty = 0;
                                $CurrentYearTotalPayment2021 = 0;
                                $CurrentYearTotalDue = 0;
                                $CurrentYearTotalDue2021 = 0;

                                $PastPayableDue2022 = 0;
                                $CurrentYearTotalPayment2022 = 0;
                                $Penalty2022 = 0;
                                $CurrentYearTotalDue2022 = 0;
                                $PastPayableDue = 0;
                            $abc = '';
                            $abc = $propertiesQuery[$j]->assessments;
                            for($i=count($abc)-1; $i>=0; $i--){
                              
                                $property_id = $abc[$i]->property_id;
                                $AssessmentYear = $abc[$i]->created_at->year;
                                $CurrentYearAssessmentAmount = $abc[$i]->current_year_assessment_amount;
                                if($PastPayableDue > 0){
                                    $Penalty = $PastPayableDue*0.25;
                                }
                                else{
                                    $Penalty =0;
                                }
                                $CurrentYearTotalPayment = $abc[$i]->getCurrentYearTotalPayment();
                                $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                             
                                // echo "AssessmentYear : ".$AssessmentYear."<br>";
                                // echo "CurrentYearAssessmentAmount : ".number_format($CurrentYearAssessmentAmount,2)."<br>";
                                // echo "PastPayableDue : ".number_format($PastPayableDue,2)."<br>";
                                // echo "Penalty : ".number_format($Penalty,2)."<br>";
                                // echo "CurrentYearTotalPayment : ".number_format($CurrentYearTotalPayment,2)."<br>";
                                // echo "CurrentYearTotalDue : ".number_format($CurrentYearTotalDue,2)."<br>";
                                // echo "property_id : ".$property_id." AssessmentYear : ".$AssessmentYear. " CurrentYearAssessmentAmount : ".number_format($CurrentYearAssessmentAmount,2)." PastPayableDue : ".number_format($PastPayableDue,2). " Penalty : ".number_format($Penalty,2). " CurrentYearTotalPayment : ".number_format($CurrentYearTotalPayment,2). " CurrentYearTotalDue : ".number_format($CurrentYearTotalDue,2)."<br>";
                                
                                if($AssessmentYear == '2024'){
                                    $due_2024[] = number_format($CurrentYearTotalDue,2);
                                }
                                
                                $PastPayableDue = $CurrentYearTotalDue;
                                // $due_2024[] = array(
                                //     'assessmentyear' => $AssessmentYear,
                                //     'property_id' => $property_id,
                                //     'CurrentYearTotalDue' => number_format($CurrentYearTotalDue,2)
                                // );
                            
                                

                            }
                            
                            // die;
                        }
// return $due_2024;


                        // die;
                           
    return \Excel::download(new WaybillExport($propertiesQuery,$batch, $ward,$due_2024), date('Y-m-d-H-i-s') . '-mod-properties.xlsx');
}






















public function sms_excel_download(Request $request){
    // dd(1);
   $data['data']=SmsToProperty::orderBy('id','desc')->get();

     $abc=$data['data'];
    // dd($abc);


    $data = '';
    
      $data .='<table>
      <tr>
      <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Property Id</th>
       <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">sms sent status</th>
      <th style="border:1px solid white;background-color
      :#cc00cc;color:white;">Demand note download status</th>
     
   
      </tr>
      ';
      foreach (@$abc as $value) {
    
             if($value->sms_status=="Y"){
                $s1="yes";
             }else{
                $s1="No";
             }



             if($value->demand_downlode=="Y"){
                $s2="yes";
             }else{
                $s2="No";
             }
                

        $data .= '
        <tr>
        <td style="border:1px solid black;">'.@$value->property_id.'</td>
         <td style="border:1px solid black;">'.@$s1.'</td>
        <td style="border:1px solid black;">'.@$s2.'</td>

        </tr>';
      }
      $data .= '</table>';
    
      //dd($data);
    header("Content-Type: application/xls");    
    header("Content-Disposition: attachment; filename=sms_details.xls");  
    header("Pragma: no-cache"); 
    header("Expires: 0");
    //dd($data); 
    echo $data;

}












    public function test_update(){
        // whereIn('id', ['85903', '102455', '102824', '104255'])
        
         $properties = Property::whereHas('assessments', function ($query) {
             $query->whereYear('created_at' , '>=' , '2023')->groupBy('property_id')
             
                   ->havingRaw('COUNT(DISTINCT property_rate_without_gst) > 1');
         })
         ->with(['assessments' => function ($query) {
            $query->whereYear('created_at', '>=', '2023')->orderBy('created_at', 'asc');
        }])
         ->get();

         foreach ($properties as $key => $property) {
            if(count($property['assessments'])<2){
                continue;
            }
                     $oldestAssessment= $property['assessments']->first();
                   $property['assessments'] = $property['assessments']->filter(function ($assessment) use ($oldestAssessment) {
                    return $assessment->id != $oldestAssessment->id;
                });
               $ids = $property['assessments']->pluck('id')->toArray();
                    $property->assessments()->wherein('id', $ids)->update([
                        'property_rate_without_gst' => $oldestAssessment->property_rate_without_gst
                    ]);
        }
        return 'done';

    
    }
    //for all
        //    return $oldestAssessment= $property->assessments()->oldest()->first();
            // $property->assessments()->where('id', '!=', $oldestAssessment->id)->update([
            //     'property_rate_without_gst' => $oldestAssessment->property_rate_without_gst
            // ]);




        // public function delete_selected_prop(){
        //     $propertyIds =[154,
        //     527,
        //     940,
        //     1109,
        //     1110,
        //     2900,
        //     3050,
        //     3876,
        //     4507,
        //     7903,
        //     7922,
        //     9286,
        //     9387,
        //     10774,
        //     11461,
        //     11600,
        //     15524,
        //     41839,
        //     41840,
        //     41844];

        //     if (empty($propertyIds)) {
        //         return ['error' => 'No property IDs provided'];
        //     }
        //         $propertiesToDelete = Property::whereNotIn('id', $propertyIds)->pluck('id');

       
        //     DB::beginTransaction();
        
        //     try {
        //         foreach ($propertiesToDelete as $propertyId) {
        //             $property = Property::find($propertyId);
        
        //             if (!$property) {
        //                 continue;
        //             }
        
        //             // Delete associated assessment data
        //             $property->assessment()->delete();
        
        //             // get associated payment data
        //             $property->payments()->delete();
        
        //             // get related landlords, occupancies, and registry
        //             // $property->landlord()->get();
        //             // $property->occupancies()->get();
        //             // $property->registry()->get();
        
        //             // get property itself
        //             $property->delete();
        //         }
        
        //         DB::commit();
        //         return ['success' => 'Properties and associated data successfully deleted'];
        //     } catch (\Exception $e) {
        //         DB::rollback();
        //         return ['error' => 'Failed to delete properties and associated data', 'message' => $e->getMessage()];
        //     }
        // }



public function property_perpage(Request $request){
    // return $request;
      // Retrieve the properties with the necessary relationships, sorted by ID in descending order, with offset and limit
      $propertiesQuery = Property::with([
        'user',
        'landlord',
        'assessments' => function ($query) {
                $query->with('types', 'valuesAdded', 'categories')->latest();
            },
        'geoRegistry',
        'user',
        'occupancies',
        'propertyInaccessible',
        'payments',
        'districts',
        'images',
        'assessment',
    ])
    ->whereHas('assessment', function ($query) use ($request) {

        if ($request->filled('demand_draft_year')) {
            $query->whereYear('created_at', $request->demand_draft_year);
            // $query->whereYear('created_at', '<=', $request->demand_draft_year);

        }
            // dd($request->demand_draft_year);
        if ($request->filled('is_printed')) {

            if ($request->input('is_printed') === '1') {
                $query->whereNotNull('last_printed_at');
            }

            if ($request->input('is_printed') === '0') {
                $query->whereNull('last_printed_at');
            }
        }

        if ($request->is_gated_community) {
            $query->where('gated_community', $request->gated_community);
        }

    })
    ->whereHas('districts', function($query) {
        $query->where('id', 13);
    }) ->whereIn('id', json_decode($request->page_property_ids))
    ->orderBy('id','desc')
    ->get();
    $batch = $request->page_no; // Replace 'XXX' with the default value or logic to fetch the batch value
    $ward = $request->ward_no; // Replace 'XXX' with the default value or logic to fetch the ward value
    $year = $request->year; // Replace 'XXX' with the default value or logic to fetch the ward value
    // return $propertiesQuery->get();


    // $abc = $propertiesQuery[0]->assessments;

  
                                // $CurrentYearAssessmentAmount = 0;
                                // $PastPayableDue = 0;
                                // $Penalty = 0;
                                // $CurrentYearTotalPayment2021 = 0;
                                // $CurrentYearTotalDue = 0;
                                // $CurrentYearTotalDue2021 = 0;

                                // $PastPayableDue2022 = 0;
                                // $CurrentYearTotalPayment2022 = 0;
                                // $Penalty2022 = 0;
                                // $CurrentYearTotalDue2022 = 0;
                                // $PastPayableDue = 0;
                                
                                $due_2024 = [];    
                        for ($j=0; $j <count($propertiesQuery) ; $j++) { 
                            
                            $CurrentYearAssessmentAmount = 0;
                                $PastPayableDue = 0;
                                $Penalty = 0;
                                $CurrentYearTotalPayment2021 = 0;
                                $CurrentYearTotalDue = 0;
                                $CurrentYearTotalDue2021 = 0;

                                $PastPayableDue2022 = 0;
                                $CurrentYearTotalPayment2022 = 0;
                                $Penalty2022 = 0;
                                $CurrentYearTotalDue2022 = 0;
                                $PastPayableDue = 0;
                            $abc = '';
                            $abc = $propertiesQuery[$j]->assessments;
                            for($i=count($abc)-1; $i>=0; $i--){
                              
                                $property_id = $abc[$i]->property_id;
                                $AssessmentYear = $abc[$i]->created_at->year;
                                $CurrentYearAssessmentAmount = $abc[$i]->current_year_assessment_amount;
                                if($PastPayableDue > 0){
                                    $Penalty = $PastPayableDue*0.25;
                                }
                                else{
                                    $Penalty =0;
                                }
                                $CurrentYearTotalPayment = $abc[$i]->getCurrentYearTotalPayment();
                                $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                             
                                // echo "AssessmentYear : ".$AssessmentYear."<br>";
                                // echo "CurrentYearAssessmentAmount : ".number_format($CurrentYearAssessmentAmount,2)."<br>";
                                // echo "PastPayableDue : ".number_format($PastPayableDue,2)."<br>";
                                // echo "Penalty : ".number_format($Penalty,2)."<br>";
                                // echo "CurrentYearTotalPayment : ".number_format($CurrentYearTotalPayment,2)."<br>";
                                // echo "CurrentYearTotalDue : ".number_format($CurrentYearTotalDue,2)."<br>";
                                // echo "property_id : ".$property_id." AssessmentYear : ".$AssessmentYear. " CurrentYearAssessmentAmount : ".number_format($CurrentYearAssessmentAmount,2)." PastPayableDue : ".number_format($PastPayableDue,2). " Penalty : ".number_format($Penalty,2). " CurrentYearTotalPayment : ".number_format($CurrentYearTotalPayment,2). " CurrentYearTotalDue : ".number_format($CurrentYearTotalDue,2)."<br>";
                                
                                if($AssessmentYear == $request->year){
                                    $due_2024[] = number_format($CurrentYearTotalDue,2);
                                    $paid_2024[] = number_format($CurrentYearTotalPayment,2);
                                    $arrears[] = number_format($PastPayableDue,2);
                                    // Check if a summary exists for the current property
                                        $prev_summary = Summary::where('property_id', $property_id)
                                        ->where('assessment_year', $request->year)
                                        ->first();

                                    // If no summary exists, create a new one
                                    if (!$prev_summary) {
                                    $summary = new Summary();
                                    $summary->property_id = $property_id;
                                    $summary->assessment_amount = $CurrentYearAssessmentAmount;
                                    $summary->assessment_year = $request->year;
                                    $summary->arrears = $PastPayableDue;  // Save as is (no need to format)
                                    $summary->penalty = $Penalty;
                                    $summary->amount_paid = $CurrentYearTotalPayment;
                                    $summary->due = $CurrentYearTotalDue;  // Save as is (no need to format)
                                    $summary->save();
                                    }
                                }
                                
                                $PastPayableDue = $CurrentYearTotalDue;
                                // $due_2024[] = array(
                                //     'assessmentyear' => $AssessmentYear,
                                //     'property_id' => $property_id,
                                //     'CurrentYearTotalDue' => number_format($CurrentYearTotalDue,2)
                                // );
                            
                                

                            }
                            
                            // die;
                        }
// return $due_2024;


                        // die;
                           
    return \Excel::download(new SummaryExport($propertiesQuery,$batch, $ward,$due_2024,$paid_2024,$arrears,$year), date('Y-m-d-H-i-s') . '-mod-properties.xlsx');
}


}