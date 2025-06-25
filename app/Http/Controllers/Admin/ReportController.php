<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoundaryDelimitation;
use App\Models\Property;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyInaccessible;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\User;
use App\Models\RegistryMeter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    private $assessments;

    public function __construct()
    {
        $this->assessments = Property::query();
    }


    public function index(Request $request)
    {

        $organizationTypes = collect(json_decode(file_get_contents(storage_path('data/organizationTypes.json')), true))->pluck('label', 'value');

        $town = BoundaryDelimitation::distinct()->orderBy('section')->pluck('section', 'section');
        $chiefdom = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort()->prepend('Select Chiefdom', '');;
        //$district = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort();
        $district = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort()->prepend('Select District', '');
        //$province = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort();
        $province = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort()->prepend('Select Province', '');
        //$ward = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
        $ward = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select Ward', '');
        $constituency = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort()->prepend('Select Constituency', '');;

        $types = PropertyType::pluck('label', 'id')->prepend('Property Type', '');
        $wallMaterial = PropertyWallMaterials::pluck('label', 'id')->prepend('Wall Material', '');
        $roofMaterial = PropertyRoofsMaterials::pluck('label', 'id')->prepend('Roof Material', '');
        $propertyDimension = PropertyDimension::pluck('label', 'id')->prepend('Dimensions', '');
        $valueAdded = PropertyValueAdded::pluck('label', 'id')->prepend('Value Added', '');

        $digital_address = PropertyGeoRegistry::distinct()->orderBy('property_id')->pluck('digital_address', 'digital_address')->sort()->prepend('Select', '');
        //$meter_number = PropertyGeoRegistry::distinct()->orderBy('property_id')->pluck('meter_number', 'meter_number')->sort()->prepend('Select', '');
        $meter_number = RegistryMeter::distinct()->orderBy('property_id')->pluck('number', 'number')->sort()->prepend('Select', '');
        $postcode = Property::distinct()->orderBy('id')->pluck('postcode', 'postcode')->sort()->prepend('Select', '');
        $assessmentUser = User::distinct()->orderBy('id')->pluck('name', 'name')->sort()->prepend('Select', '');

        $this->applyFilter($request);
        //\DB::enableQueryLog();

        $assessment1 = clone  $this->assessments;
        $assessment2 = clone  $this->assessments;
        $assessment3 = clone  $this->assessments;
        $assessmentData = $assessment1->select()->addSelect(\DB::raw('COUNT(user_id) as count'), 'user_id')->with('user')->groupBy('user_id')->get();


        $assessmentMonthlyData = $assessment2->select()->addSelect(\DB::raw('COUNT(user_id) as count'), 'properties.section', 'user_id', \DB::raw("MONTH(properties.created_at) as month"), \DB::raw("YEAR(properties.created_at) as year"), \DB::raw('SUM(property_assessment_details.property_rate_with_gst) as total'))->leftJoin('property_assessment_details', 'property_assessment_details.property_id', '=', 'properties.id')->with(['user'])->groupBy('user_id', \DB::raw("MONTH(properties.created_at)"))->orderBy('properties.id','DESC')->get();

        //dd(\DB::getQueryLog());
        $monitoringData = $assessment3->select()->addSelect(\DB::raw('COUNT(user_id) as count'), 'user_id')->addSelect(\DB::raw('SUM(property_assessment_details.property_rate_with_gst) as total'))->with(['user'])
            ->leftJoin('property_assessment_details', 'property_assessment_details.property_id', '=', 'properties.id')->groupBy('user_id')->get();

        $property_inaccessibles = PropertyInaccessible::where('is_active', 1)->pluck('label', 'id')->prepend('Select Property Inaccessible');

        $assessmentData = $this->getAssessmentGraph($assessmentData);
        $monitoringData = $this->getMonitoringGraph($monitoringData);

        $months = array(1 => 'Jan.', 2 => 'Feb.', 3 => 'Mar.', 4 => 'Apr.', 5 => 'May', 6 => 'Jun.', 7 => 'Jul.', 8 => 'Aug.', 9 => 'Sep.', 10 => 'Oct.', 11 => 'Nov.', 12 => 'Dec.');

        $property = Property::orderBy('id')->first();

        return view('admin.report.index', compact(
            'request',
            'town',
            'district',
            'province',
            'ward',
            'types',
            'wallMaterial',
            'roofMaterial',
            'propertyDimension',
            'valueAdded',
            'assessmentUser',
            'digital_address',
            'postcode',
            'meter_number',
            'assessmentData',
            'chiefdom',
            'constituency',
            'monitoringData',
            'assessmentMonthlyData',
            'months',
            'property_inaccessibles',
            'property',
            'organizationTypes'
        ));
    }

    public function applyFilter(Request $request)
    {
        $filterBy = $request->filter_by ?: '';

        if ($filterBy == 'daily') {
            $this->assessments = $this->assessments->whereDate('properties.created_at', Carbon::today());
        }

        if ($filterBy == 'weekly') {
            $this->assessments = $this->assessments->where('properties.created_at', '>',   Carbon::today()->subDays(7));
        }

        if ($filterBy == 'monthly') {
            $this->assessments = $this->assessments->whereMonth(
                'properties.created_at',
                '=',
                Carbon::now()->subMonth()->month
            );
        }
        if ($filterBy == 'yearly') {
            $this->assessments = $this->assessments->whereYear(
                'properties.created_at',
                '=',
                Carbon::now()->year
            );
        }
        if ($request->from != '' and $request->to != '') {
            $this->assessments = Property::where('properties.created_at', '>=', $request->from . ' 00:00:00')->where('properties.created_at', '<=', $request->to . ' 23:59:59');
        }

        !$request->name ?: $this->assessments->whereHas('user', function ($query) use ($request) {
            return $query->where('name', $request->name);
        });

        !$request->town ?: $this->assessments->where('properties.section', $request->town);
        !$request->chiefdom ?: $this->assessments->where('properties.chiefdom', $request->chiefdom);
        !$request->constituency ?: $this->assessments->where('properties.constituency', $request->constituency);
        !$request->ward ?: $this->assessments->where('properties.ward', $request->ward);
        !$request->district ?: $this->assessments->where('properties.district', $request->district);
        !$request->province ?: $this->assessments->where('properties.province', $request->province);
        !$request->postcode ?: $this->assessments->where('properties.postcode', $request->postcode);
        !$request->is_completed ?: $this->assessments->where('properties.is_completed', ($request->is_completed == 'yes' ? true : false));

        !$request->type ?: $this->assessments->whereHas('types', function ($query) use ($request) {
            return $query->where('id', $request->type);
        });

        !$request->property_inaccessible ?: $this->assessments->whereHas('propertyInaccessible', function ($query) use ($request) {
            return $query->where('id', $request->property_inaccessible);
        });

        !$request->wall_material ?: $this->assessments->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_wall_materials', $request->wall_material);
        });

        !$request->roof_material ?: $this->assessments->whereHas('assessment', function ($query) use ($request) {
            return $query->where('roofs_materials', $request->roof_material);
        });

        !$request->property_dimension ?: $this->assessments->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_dimension', $request->property_dimension);
        });

        !$request->value_added ?: $this->assessments->whereHas('valueAdded', function ($query) use ($request) {
            return $query->where('id', $request->value_added);
        });

        if ($request->input('is_organization') == 1 && $request->input('organization_type')) {
            $this->assessments->where('organization_type', $request->input('organization_type'))->where('is_organization', true);
        }

        if ($request->input('is_organization') == 0) {
            $this->assessments->where('is_organization', false);
        }

        /*!$request->owned_tenecy ?: $this->assessments->whereHas('occupancy', function ($query) use ($request){
            return $query->where('owned_tenancy', $request->owned_tenecy == 'yes' ? true : false);
        });

        !$request->rented ?: $this->assessments->whereHas('occupancy', function ($query) use ($request){
            return $query->where('rented', $request->rented == 'yes' ? true : false);
        });

        !$request->unoccupied ?: $this->assessments->whereHas('occupancy', function ($query) use ($request){
            return $query->where('unoccupied_house', $request->unoccupied == 'yes' ? true : false);
        });*/

        !$request->occupancy_type ?: $this->assessments->whereHas('occupancy', function ($query) use ($request) {
            return $query->where('type', $request->occupancy_type);
        });

        // !$request->meter_number ?: $this->assessments->whereHas('geoRegistry', function ($query) use ($request) {
        //     return $query->where('meter_number', $request->meter_number);
        // });        

        !$request->meter_number ?: $this->assessments->whereHas('registryMeters', function ($query) use ($request) {
            return $query->where('number', $request->meter_number);
        });

        !$request->address ?: $this->assessments->whereHas('geoRegistry', function ($query) use ($request) {
            return $query->where('digital_address', $request->address);
        });

        //!$request->address ?: $this->properties->where('properties.id', $request->address);

        $this->assessments->whereHas('user', function ($query) use ($request) {
            return $query->where('id', '!=', 0);
        });
    }

    public function getAssessmentGraph($assessmentData)
    {
        if ($assessmentData->count()) {
            foreach ($assessmentData as $assessment) {
                $record[$assessment->user->getName()] = $assessment->count;
            }

            return $record;
        }

        return [];
    }

    public function getMonitoringGraph($monitoringData)
    {
        if ($monitoringData->count()) {
            foreach ($monitoringData as $monitoring) {

                $record[$monitoring->user->getName()] = floatval($monitoring->total);
            }

            return $record;
        }

        return [];
    }
}
