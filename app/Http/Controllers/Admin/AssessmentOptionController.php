<?php

namespace App\Http\Controllers\Admin;

use App\Grids\PropertyCategoriesGrid;
use App\Grids\PropertyDimensionsGrid;
use App\Grids\PropertyInaccessiblesGrid;
use App\Grids\PropertyRoofsMaterialsGrid;
use App\Grids\PropertyTypesGrid;
use App\Grids\PropertyUsesGrid;
use App\Grids\PropertyValueAddedsGrid;
use App\Grids\PropertyWallMaterialsGrid;
use App\Grids\PropertyZonesGrid;
use App\Grids\SwimmingsGrid;
use App\Grids\SanitationsGrid;
use App\Http\Controllers\Controller;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyInaccessible;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\Property;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\Swimming;
use App\Models\PropertySanitationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssessmentOptionController extends Controller
{

    //For PropertyCategories CURD

    public function propertyCategories(PropertyCategoriesGrid $categoriesGrid, Request $request)
    {
        return $categoriesGrid
            ->create(['query' => PropertyCategory::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.property-category.list');
    }
    public function propertyCategoryCreate(Request $request)
    {

        $pc = new PropertyCategory();
        if (isset($request->property_category))
            $pc = PropertyCategory::where('id', $request->property_category)->first();
        return view('admin.assessment-options.property-category.create', compact('pc'));
    }
    public function propertyCategoryStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pc = PropertyCategory::findorFail($request->id);
            $pc->label = $request->label;
            $pc->value = $request->value;
            $pc->is_active = $request->is_active;
            $pc->save();
        } else {
            $pc = new PropertyCategory();
            $pc->label = $request->label;
            $pc->value = $request->value;
            $pc->is_active = $request->is_active;
            $pc->save();
        }
        return Redirect()->route('admin.list.property.category')->with('success', 'Record Saved Successfully !');
    }
    public function propertyCategoryDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('property_categories', $request->property_category)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.category')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } elseif ($request->property_category == 6) {
            return Redirect()->route('admin.list.property.category')->with('error', 'Record Has Restricted  Can Not Delete  !');
        } else {
            $pc = PropertyCategory::findorFail($request->property_category);
            $pc->delete();
            return Redirect()->route('admin.list.property.category')->with('success', 'Record Deleted Successfully !');
        }
    }

    // For Property Dimensions CURD

    public function propertyDimensions(PropertyDimensionsGrid $dimensionsGrid, Request $request)
    {
        return $dimensionsGrid
            ->create(['query' => PropertyDimension::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.property-dimension.list');
    }
    public function propertyDimensionCreate(Request $request)
    {

        $pc = new PropertyDimension();
        if (isset($request->propertydimension))
            $pc = PropertyDimension::where('id', $request->propertydimension)->first();
        return view('admin.assessment-options.property-dimension.create', compact('pc'));
    }
    public function propertyDimensionStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pd = PropertyDimension::findorFail($request->id);
            $pd->label = $request->label;
            $pd->value = $request->value;
            $pd->is_active = $request->is_active;
            $pd->save();
        } else {
            $pd = new PropertyDimension();
            $pd->label = $request->label;
            $pd->value = $request->value;
            $pd->is_active = $request->is_active;
            $pd->save();
        }
        return Redirect()->route('admin.list.property.dimension')->with('success', 'Record Saved Successfully !');
    }
    public function propertyDimensionDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('property_dimension', $request->propertydimension)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.dimension')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pd = PropertyDimension::findorFail($request->propertydimension);
            $pd->delete();
            return Redirect()->route('admin.list.property.dimension')->with('success', 'Record Deleted Successfully !');
        }
    }


    // For Property Types CURD

    public function propertyTypes(PropertyTypesGrid $propertyTypesGrid, Request $request)
    {
        return $propertyTypesGrid
            ->create(['query' => PropertyType::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.property-type.list');
    }
    public function propertyTypeCreate(Request $request)
    {

        $pc = new PropertyType();
        if (isset($request->property_type))
            $pc = PropertyType::where('id', $request->property_type)->first();
        return view('admin.assessment-options.property-type.create', compact('pc'));
    }
    public function propertyTypeStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pt = PropertyType::findorFail($request->id);
            $pt->label = $request->label;
            $pt->value = $request->value;
            $pt->is_active = $request->is_active;
            $pt->save();
        } else {
            $pt = new PropertyType();
            $pt->label = $request->label;
            $pt->value = $request->value;
            $pt->is_active = $request->is_active;
            $pt->save();
        }
        return Redirect()->route('admin.list.property.type')->with('success', 'Record Saved Successfully !');
    }
    public function propertyTypeDelete(Request $request)
    {


        $assessment = PropertyType::find($request->property_type)->properties()->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.type')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pt = PropertyType::findorFail($request->property_type);
            $pt->delete();
            return Redirect()->route('admin.list.property.type')->with('success', 'Record Deleted Successfully !');
        }
    }

    // For Property Roof Materials CURD

    public function propertyRoofMaterials(PropertyRoofsMaterialsGrid $propertyRoofsMaterialsGrid, Request $request)
    {
        return $propertyRoofsMaterialsGrid
            ->create(['query' => PropertyRoofsMaterials::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.roof-material.list');
    }
    public function propertyRoofMaterialCreate(Request $request)
    {

        $pc = new PropertyRoofsMaterials();
        if (isset($request->property_roofs_material))
            $pc = PropertyRoofsMaterials::where('id', $request->property_roofs_material)->first();
        return view('admin.assessment-options.roof-material.create', compact('pc'));
    }
    public function propertyRoofMaterialStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $prf = PropertyRoofsMaterials::findorFail($request->id);
            $prf->label = $request->label;
            $prf->value = $request->value;
            $prf->is_active = $request->is_active;
            $prf->save();
        } else {
            $prf = new PropertyRoofsMaterials();
            $prf->label = $request->label;
            $prf->value = $request->value;
            $prf->is_active = $request->is_active;
            $prf->save();
        }
        return Redirect()->route('admin.list.property.roof-material')->with('success', 'Record Saved Successfully !');
    }
    public function propertyRoofMaterialDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('roofs_materials', $request->property_roofs_material)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.roof-material')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $prf = PropertyRoofsMaterials::findorFail($request->property_roofs_material);
            $prf->delete();
            return Redirect()->route('admin.list.property.roof-material')->with('success', 'Record Deleted Successfully !');
        }
    }



    // For Property Wall Materials CURD

    public function propertyWallMaterials(PropertyWallMaterialsGrid $propertyWallMaterialsGrid, Request $request)
    {
        return $propertyWallMaterialsGrid
            ->create(['query' => PropertyWallMaterials::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.wall-material.list');
    }
    public function propertyWallMaterialCreate(Request $request)
    {

        $pc = new PropertyWallMaterials();
        if (isset($request->property_wall_material))
            $pc = PropertyWallMaterials::where('id', $request->property_wall_material)->first();
        return view('admin.assessment-options.wall-material.create', compact('pc'));
    }
    public function propertyWallMaterialStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pwf = PropertyWallMaterials::findorFail($request->id);
            $pwf->label = $request->label;
            $pwf->value = $request->value;
            $pwf->is_active = $request->is_active;
            $pwf->save();
        } else {
            $pwf = new PropertyWallMaterials();
            $pwf->label = $request->label;
            $pwf->value = $request->value;
            $pwf->is_active = $request->is_active;
            $pwf->save();
        }
        return Redirect()->route('admin.list.property.wall-material')->with('success', 'Record Saved Successfully !');
    }
    public function propertyWallMaterialDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('property_wall_materials', $request->property_wall_material)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.wall-material')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pwf = PropertyWallMaterials::findorFail($request->property_wall_material);
            $pwf->delete();
            return Redirect()->route('admin.list.property.wall-material')->with('success', 'Record Deleted Successfully !');
        }
    }


    // For Property Zone CURD

    public function propertyZones(PropertyZonesGrid $propertyZonesGrid, Request $request)
    {
        return $propertyZonesGrid
            ->create(['query' => PropertyZones::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.zone.list');
    }
    public function propertyZoneCreate(Request $request)
    {

        $pc = new PropertyZones();
        if (isset($request->property_zone))
            $pc = PropertyZones::where('id', $request->property_zone)->first();
        return view('admin.assessment-options.zone.create', compact('pc'));
    }
    public function propertyZoneStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pz = PropertyZones::findorFail($request->id);
            $pz->label = $request->label;
            $pz->value = $request->value;
            $pz->is_active = $request->is_active;
            $pz->save();
        } else {
            $pz = new PropertyZones();
            $pz->label = $request->label;
            $pz->value = $request->value;
            $pz->is_active = $request->is_active;
            $pz->save();
        }
        return Redirect()->route('admin.list.property.zone')->with('success', 'Record Saved Successfully !');
    }
    public function propertyZoneDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('zone', $request->property_zone)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.zone')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pz = PropertyZones::findorFail($request->property_zone);
            $pz->delete();
            return Redirect()->route('admin.list.property.zone')->with('success', 'Record Deleted Successfully !');
        }
    }

    // For Property Use CURD

    public function propertyUse(PropertyUsesGrid $propertyUsesGrid, Request $request)
    {
        return $propertyUsesGrid
            ->create(['query' => PropertyUse::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.use.list');
    }
    public function propertyUseCreate(Request $request)
    {

        $pc = new PropertyUse();
        if (isset($request->propertyuse))
            $pc = PropertyUse::where('id', $request->propertyuse)->first();
        return view('admin.assessment-options.use.create', compact('pc'));
    }
    public function propertyUseStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pu = PropertyUse::findorFail($request->id);
            $pu->label = $request->label;
            $pu->value = $request->value;
            $pu->is_active = $request->is_active;
            $pu->save();
        } else {
            $pu = new PropertyUse();
            $pu->label = $request->label;
            $pu->value = $request->value;
            $pu->is_active = $request->is_active;
            $pu->save();
        }
        return Redirect()->route('admin.list.property.use')->with('success', 'Record Saved Successfully !');
    }
    public function propertyUseDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('property_use', $request->propertyuse)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.use')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pu = PropertyUse::findorFail($request->propertyuse);
            $pu->delete();
            return Redirect()->route('admin.list.property.use')->with('success', 'Record Deleted Successfully !');
        }
    }


    // For Property Value Added CURD

    public function propertyValueAdded(PropertyValueAddedsGrid $propertyValueAddedsGrid, Request $request)
    {
        return $propertyValueAddedsGrid
            ->create(['query' => PropertyValueAdded::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.value-added.list');
    }

    public function propertyValueAddedCreate(Request $request)
    {

        $pc = new PropertyValueAdded();
        if (isset($request->propertyvalueadded))
            $pc = PropertyValueAdded::where('id', $request->propertyvalueadded)->first();
        return view('admin.assessment-options.value-added.create', compact('pc'));
    }

    public function propertyValueAddedStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pva = PropertyValueAdded::findorFail($request->id);
            $pva->label = $request->label;
            $pva->value = $request->value;
            $pva->is_active = $request->is_active;
            $pva->save();
        } else {
            $pva = new PropertyValueAdded();
            $pva->label = $request->label;
            $pva->value = $request->value;
            $pva->is_active = $request->is_active;
            $pva->save();
        }
        return Redirect()->route('admin.list.property.value-added')->with('success', 'Record Saved Successfully !');
    }
    public function propertyValueAddedDelete(Request $request)
    {


        $assessment = PropertyValueAdded::find($request->propertyvalueadded)->properties()->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.value-added')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } elseif ($request->propertyvalueadded == 8 or $request->propertyvalueadded == 9) {
            return Redirect()->route('admin.list.property.value-added')->with('error', 'Record Has Restricted  Can Not Delete  !');
        } else {
            $pva = PropertyValueAdded::findorFail($request->propertyvalueadded);
            $pva->delete();
            return Redirect()->route('admin.list.property.value-added')->with('success', 'Record Deleted Successfully !');
        }
    }


    // For Property Zone CURD

    public function propertySwimmings(SwimmingsGrid $propertySwimmingGrid, Request $request)
    {
        return $propertySwimmingGrid
            ->create(['query' => Swimming::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.swimming.list');
    }

    public function propertySwimmingCreate(Request $request)
    {
        $pc = new Swimming();
        if (isset($request->swimming))
            $pc = Swimming::where('id', $request->swimming)->first();
        return view('admin.assessment-options.swimming.create', compact('pc'));
    }

    public function propertySwimmingStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pz = Swimming::findorFail($request->id);
            $pz->label = $request->label;
            $pz->value = $request->value;
            $pz->is_active = $request->is_active;
            $pz->save();
        } else {
            $pz = new Swimming();
            $pz->label = $request->label;
            $pz->value = $request->value;
            $pz->is_active = $request->is_active;
            $pz->save();
        }
        return Redirect()->route('admin.list.property.swimming')->with('success', 'Record Saved Successfully !');
    }

    public function propertySwimmingDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('swimming_id', $request->swimming)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.swimming')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pz = Swimming::findorFail($request->swimming);
            $pz->delete();
            return Redirect()->route('admin.list.property.swimming')->with('success', 'Record Deleted Successfully !');
        }
    }


    // For Property Sanitation CRUD
    public function propertySanitation(SanitationsGrid $propertySanitationGrid, Request $request)
    {
        return $propertySanitationGrid
            ->create(['query' => PropertySanitationType::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.sanitation.list');
    }


    public function propertySanitationCreate(Request $request)
    {
        $pc = new PropertySanitationType();
        if (isset($request->sanitation))
            $pc = PropertySanitationType::where('id', $request->sanitation)->first();
        return view('admin.assessment-options.sanitation.create', compact('pc'));
    }

    public function propertySanitationStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'value' => 'required|regex:/^[0-9]+(\.[0-9]{1,4})?$/',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pz = PropertySanitationType::findorFail($request->id);
            $pz->label = $request->label;
            $pz->value = $request->value;
            $pz->is_active = $request->is_active;
            $pz->save();
        } else {
            $pz = new PropertySanitationType();
            $pz->label = $request->label;
            $pz->value = $request->value;
            $pz->is_active = $request->is_active;
            $pz->save();
        }
        return Redirect()->route('admin.list.property.sanitation')->with('success', 'Record Saved Successfully !');
    }

    public function propertySanitationDelete(Request $request)
    {
        $assessment = PropertyAssessmentDetail::where('sanitation', $request->swimming)->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.sanitation')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pz = PropertySanitationType::findorFail($request->sanitation);
            $pz->delete();
            return Redirect()->route('admin.list.property.sanitation')->with('success', 'Record Deleted Successfully !');
        }
    }


    // For Property Zone CURD

    public function propertyInaccessible(PropertyInaccessiblesGrid $propertyInaccessiblesGrid, Request $request)
    {
        return $propertyInaccessiblesGrid
            ->create(['query' => PropertyInaccessible::query(), 'request' => $request])
            ->renderOn('admin.assessment-options.property-inaccessible.list');
    }

    public function propertyInaccessibleCreate(Request $request)
    {
        $pc = new PropertyInaccessible();
        if (isset($request->property_inaccessible))
            $pc = PropertyInaccessible::where('id', $request->property_inaccessible)->first();
        return view('admin.assessment-options.property-inaccessible.create', compact('pc'));
    }

    public function propertyInaccessibleStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'label' => 'required|string',
            'is_active' => 'required|boolean'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        if (isset($request->id) and $request->id != null) {
            $pz = PropertyInaccessible::findorFail($request->id);
            $pz->label = $request->label;
            $pz->is_active = $request->is_active;
            $pz->save();
        } else {
            $pz = new PropertyInaccessible();
            $pz->label = $request->label;
            $pz->is_active = $request->is_active;
            $pz->save();
        }
        return Redirect()->route('admin.list.property.inaccessible')->with('success', 'Record Saved Successfully !');
    }

    public function propertyInaccessibleDelete(Request $request)
    {

        $assessment = PropertyInaccessible::find($request->property_inaccessible)->properties()->first();
        if ($assessment) {
            return Redirect()->route('admin.list.property.inaccessible')->with('error', 'Record Has Associated Property Found Can Not Delete  !');
        } else {
            $pz = PropertyInaccessible::findorFail($request->property_inaccessible);
            $pz->delete();
            return Redirect()->route('admin.list.property.inaccessible')->with('success', 'Record Deleted Successfully !');
        }
    }
}
