<?php


namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Traits\AlertMessage;
use Eav\AttributeSet;
use Eav\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class AttributeSetController extends Controller
{
    use AlertMessage;

    public $module = "Attribute Set";

    public function index(Request $request)
    {
    
            $grid = (new Grid())
            ->setQuery(AttributeSet::query()->where('user_id', Auth::user()->id))
            ->setColumns([
                [
                    'field' => 'attribute_set_name',
                    'label' => 'Name',
                    'sortable' => true,
                    'filterable' => true
                ],

            ])->setButtons([
                [
                    'label' => 'Edit',
                    'icon' => 'edit',
                    'url' => function ($item) {
                        if(Auth::user()->user_type == 'shop'){
                            return route('admin.seller-attribute-set.edit', $item->attribute_set_id);
                        }
                        else{
                            return route('admin.attribute-set.edit', $item->attribute_set_id);
                        }
                        
                    }
                ]
            ])->generate();

        return view('admin.attribute.attribute-set.grid', compact('grid'));
        
       
    }

    public function create()
    {
        $entities = Entity::pluck('entity_code', 'entity_id');

        return view('admin.attribute.attribute-set.create')->with('entities', $entities);
    }

    public function store(Request $request)
    {
        $this->validator()->validate();

        $attributeSet = AttributeSet::create([
            'attribute_set_name' => $request->input('attribute_set_name'),
            'entity_id' => $request->input('entity_id'),
            'user_id' => Auth::user()->id
        ]);
        DB::table('attribute_sets')->where('attribute_set_id',$attributeSet->attribute_set_id)->update(['user_id'=>Auth::user()->id]);
        // return $attributeSet->attribute_set_id;
        if (Auth::user()->user_type == 'shop') {
            return redirect()->route('admin.seller-attribute-set.index')->with($this->createResponse());
        }else{
            return redirect()->route('admin.attribute-set.index', ['attribute_set' => $attributeSet->attribute_set_id])->with($this->createResponse());
        }
        
    }

    public function edit(AttributeSet $attributeSet, Request $request, $id = 0)
    {
        // return $id;
        // return $attributeSet;
        if (Auth::user()->user_type == 'shop') {
            $attributeSet = AttributeSet::where('attribute_set_id',$id)->first();
        }

        // return $attributeSet;
        $entities = Entity::pluck('entity_code', 'entity_id');

        return view('admin.attribute.attribute-set.edit')
            ->with(compact('entities', 'attributeSet'));
    }

    public function update(AttributeSet $attributeSet, Request $request)
    {
        // return $request;
        if (Auth::user()->user_type == 'shop') {
            $attributeSet = AttributeSet::where('attribute_set_id',$request->attribute_set_id)->first();
        }
        // return $attributeSet;
        $this->validator()->validate();

        $attributeSet->fill($request->all())->save();

        return redirect()->back()->with($this->updateResponse());
    }

    public function destroy(AttributeSet $attributeSet, Request $request)
    {
        // return $request;
        if (Auth::user()->user_type == 'shop') {
            $attributeSet = AttributeSet::where('attribute_set_id',$request->attribute_set_id)->first();
        }
        // return $attributeSet;
        $attributeSet->delete();

        if (Auth::user()->user_type == 'shop') {
            return redirect()->route('admin.seller-attribute-set.index')->with($this->deleteResponse());
        }else{
            return redirect()->route('admin.attribute-set.index')->with($this->deleteResponse());
        }

        
    }

    protected function validator()
    {
        $attributeSetId = \request()->route('attribute_set') ? \request()->route('attribute_set')->attribute_set_id : null;

        return Validator::make(\request()->all(), [
            'attribute_set_name' => ['required', 'string', 'max:100', Rule::unique('attribute_sets', 'attribute_set_name')->ignore(
                $attributeSetId,
                'attribute_set_id'
            )],
            'entity_id' => ['required']
        ]);
    }
}
