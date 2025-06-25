<?php

namespace App\Http\Controllers\Admin;

use App\Grids\MetaValuesGrid;
use App\Models\MetaValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MetaValueController extends Controller
{
    public $filter_by;

    public function __invoke(Request  $request)
    {
        return view('admin.meta-values');

    }
    public function store(Request $request)
    {
        $meta = new MetaValue();

        if($request->input('first_name'))
        {
            $firstnames = explode(',',$request->input('first_name'));
            foreach ($firstnames as $firstname)
            {
                $data[] = ['name'=>'first_name','value'=>$firstname];
            }


        }

        if($request->input('surname'))
        {
            $surnames = explode(',',$request->input('surname'));
            foreach ($surnames as $surname)
            {
                $data[] = ['name'=>'surname','value'=>$surname];
            }


        }
        if($request->input('street_name'))
        {
            $street_names = explode(',',$request->input('street_name'));
            foreach ($street_names as $street_name)
            {
                $data[] = ['name'=>'street_name','value'=>$street_name];
            }


        }


        if($request->input('first_name') or $request->input('surname') or $request->input('street_name')) {
            $meta->insert($data);

            return Redirect()->back()->with('success', 'Record saved Successfully !');
        }else
        {
            return Redirect()->back()->with('error', 'No Record To save!');
        }

    }

    public function list($metaValuesGrid, $request)
    {
        $this->query = MetaValue::query();

        !$this->filter_by || $this->query->where('name', $this->filter_by);

        return $metaValuesGrid
            ->create(['query' => $this->query, 'request' => $request,])
            ->renderOn('admin.meta-list', [ 'heading' => $this->filter_by]);
    }

    public function surname(MetaValuesGrid $metaValuesGrid, Request $request)
    {

        $this->filter_by = 'surname';
        return $this->list( $metaValuesGrid, $request);
    }

    public function streetName(MetaValuesGrid $metaValuesGrid, Request $request)
    {

        $this->filter_by = 'street_name';
        return $this->list( $metaValuesGrid, $request);
    }

    public function firstName(MetaValuesGrid $metaValuesGrid, Request $request)
    {

        $this->filter_by = 'first_name';
        return $this->list( $metaValuesGrid, $request);
    }

    public function edit(Request $request)
    {

        $meta = MetaValue::firstOrNew(['id'=>$request->metavalue]);

        return view('admin.meta-edit',compact('meta'));

    }

    public function update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'value' => 'required|string|max:254',
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }

        $meta = MetaValue::firstOrNew(['id'=>$request->id]);

        $meta->fill(['value'=>$request->value]);
        $meta->save();

        return Redirect()->back()->with('success', 'Record updated Successfully !');

    }

    public function delete(Request $request)
    {
        $meta = MetaValue::findorFail($request->metavalue);
        $meta->delete();
        return Redirect()->route('admin.meta.value.index')->with('success','Record Deleted Successfully !');
    }
}
