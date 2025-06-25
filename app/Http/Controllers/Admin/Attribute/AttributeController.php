<?php


namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Traits\AlertMessage;
use Eav\Attribute;
use Eav\AttributeGroup;
use App\Models\AttributeOption;
use Eav\AttributeSet;
use Eav\Entity;
use Eav\EntityAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AttributeController extends Controller
{
    use AlertMessage;

    public $module = "Attribute";

    public function index(Request $request)
    {
        $query = Attribute::select([
            'attributes.*',
            'attribute_sets.attribute_set_name',
            'attribute_groups.attribute_group_name'
        ])
        ->leftJoin('entity_attributes', 'entity_attributes.attribute_id', '=', 'attributes.attribute_id')
        ->leftJoin('attribute_sets', 'entity_attributes.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
        ->leftJoin('attribute_groups', 'entity_attributes.attribute_group_id', '=', 'attribute_groups.attribute_group_id')
        ->where('attributes.user_id', Auth::user()->id);

        $grid = (new Grid())
            ->setQuery($query)
            ->setColumns([
                [
                    'field' => 'frontend_label',
                    'label' => 'Label',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'attribute_code',
                    'label' => 'Code',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'sequence',
                    'label' => 'Sequence',
                    'sortable' => true,
                    
                ],
                [
                    'field' => 'frontend_type',
                    'label' => 'Type',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'attribute_set_name',
                    'label' => 'Attribute Set',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'attribute_group_name',
                    'label' => 'Group',
                    'sortable' => true,
                    'filterable' => true
                ],
            ])->setButtons([
                [
                    'label' => 'Edit',
                    'icon' => 'edit',
                    'url' => function ($item) {
                        if(Auth::user()->user_type == 'shop'){
                            return route('admin.seller-attribute.edit', $item->attribute_id);
                        }else{
                            return route('admin.attribute.edit', $item->attribute_id);
                        }
                    }
                ]
            ])->generate();

        return view('admin.attribute.attribute.grid', compact('grid'));
    }

    public function create()
    {
        $attributeSets = AttributeSet::pluck('attribute_set_name', 'attribute_set_name');

        $attributeGroups = AttributeGroup::where('attribute_set_id', AttributeSet::first()->attribute_set_id)->pluck('attribute_group_name', 'attribute_group_name');

        return view('admin.attribute.attribute.create')->with(compact('attributeSets', 'attributeGroups'));
    }

    public function store(Request $request)
    {
        // return $request;
        $this->validator()->validate();

        $attribute = DB::transaction(function () use ($request) {

            /** @var Entity $entity */
            $entity = Entity::whereHas('attributeSet', function ($query) use ($request) {
                $query->where('attribute_set_name', $request->input('attribute_set'));
            })->first();

            $attribute = Attribute::add([
                'attribute_code' => $request->input('attribute_code'),
                'entity_code' => $entity->getCode(),
                'backend_class' => null,
                'backend_type' => 'string',
                'backend_table' => null,
                'frontend_class' => null,
                'frontend_type' => $request->input('frontend_type'),
                'frontend_label' => $request->input('frontend_label'),
                'default_value' => 0,
                'is_required' => $request->filled('is_required'),
                'is_filterable' => 0,
                'is_searchable' => 0,
                'required_validate_class' => null
            ]);

            EntityAttribute::map([
                'attribute_code' => $request->input('attribute_code'),
                'entity_code' => $entity->getCode(),
                'attribute_set' => $request->input('attribute_set'),
                'attribute_group' => $request->input('attribute_group')
            ]);

            if ($attribute->frontend_type == 'select') {
                foreach($request->input('options') as $option) {
                    AttributeOption::create([
                        'is_featured' => ! empty($option['is_featured']),
                        'attribute_id' => $attribute->attribute_id,
                        'label' => $option['value'],
                        'value' => $option['value']
                    ]);
                }
            }

            
            DB::table('attributes')->where('attribute_id',$attribute->attribute_id)->update(['sequence'=>$request->sequence,'user_id'=>$request->user_id]);
            return $attribute;
        });
        if (Auth::user()->user_type == 'shop') {
            return redirect()->route('admin.seller-attribute.index')->with($this->createResponse());
        }
        else{
            return redirect()->route('admin.attribute.edit', ['attribute' => $attribute->attribute_id])->with($this->createResponse());
        }
        
    }

    public function edit(Attribute $attribute, $id = 0)
    {
        if (Auth::user()->user_type == 'shop') {
            $attribute = Attribute::where('attribute_id',$id)->first();
        }
        // return $attribute;
        /** @var EntityAttribute $entityAttribute */
        $entityAttribute = EntityAttribute::where('attribute_id', $attribute->attributeId())->first();

        $attributeSets = AttributeSet::pluck('attribute_set_name', 'attribute_set_name');

        $attributeGroups = AttributeGroup::where('attribute_set_id', $entityAttribute->attribute_set_id ?? AttributeSet::first()->attribute_set_id ?? 0)->pluck('attribute_group_name', 'attribute_group_name');

        if ($entityAttribute) {
            $attribute->attribute_set = AttributeSet::find($entityAttribute->attribute_set_id)->attribute_set_name;
            $attribute->attribute_group = AttributeGroup::find($entityAttribute->attribute_group_id)->attribute_group_name;
        }

        $attribute->options = $attribute->optionValues()->get()->toArray();

        return view('admin.attribute.attribute.edit')
            ->with(compact('attribute', 'attributeSets', 'attributeGroups'));
    }

    public function update(Attribute $attribute, Request $request)
    {
        // return $request;
        if (Auth::user()->user_type == 'shop') {
            $attribute = Attribute::where('attribute_id',$request->attribute_id)->first();
        }
        // return $attribute;
        $request->request->set('frontend_type', $attribute->frontend_type);

        $this->validator()->validate();

        DB::transaction(function () use ($attribute, $request) {

            /** @var Entity $entity */
            $entity = Entity::whereHas('attributeSet', function ($query) use ($request) {
                $query->where('attribute_set_name', $request->input('attribute_set'));
            })->first();

            $attribute->fill(['entity_id' => $entity->entity_id]);

            $attribute->fill($request->except('code', 'entity_code', 'frontend_type'))->save();

            EntityAttribute::where('attribute_id', $attribute->attribute_id)->delete();

            /** @var Entity $entity */
            $entity = Entity::whereHas('attributeSet', function ($query) use ($request) {
                $query->where('attribute_set_name', $request->input('attribute_set'));
            })->first();

            EntityAttribute::map([
                'attribute_code' => $request->input('attribute_code'),
                'entity_code' => $entity->getCode(),
                'attribute_set' => $request->input('attribute_set'),
                'attribute_group' => $request->input('attribute_group')
            ]);

            if ($attribute->frontend_type == 'select') {

                AttributeOption::where('attribute_id', $attribute->attributeId())->delete();

                foreach($request->input('options') as $option) {
                    AttributeOption::create([
                        'is_featured' => ! empty($option['is_featured']),
                        'attribute_id' => $attribute->attribute_id,
                        'label' => $option['value'],
                        'value' => $option['value']
                    ]);
                }
            }
        });
        DB::table('attributes')->where('attribute_id',$attribute->attribute_id)->update(['sequence'=>$request->sequence]);
        return redirect()->back()->with($this->updateResponse());
    }

    public function destroy(Attribute $attribute, Request $request)
    {
        if (Auth::user()->user_type == 'shop') {
            $attribute = Attribute::where('attribute_id',$request->attribute_id)->first();
        }
        // return $attribute;
        $attribute->delete();
        if (Auth::user()->user_type == 'shop') {
            return redirect()->route('admin.seller-attribute.index')->with($this->deleteResponse());
        }else{
            return redirect()->route('admin.attribute.index')->with($this->deleteResponse());
        }
        
    }

    public function attributeGroupsByAttributeSet($name): JsonResponse
    {
        /** @var AttributeSet $attributeSet */
        $attributeSet = AttributeSet::where("attribute_set_name", $name)->first();

        return response()->json($attributeSet->attributeGroup()->get()->toArray());
    }

    protected function validator(): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'frontend_label' => ['required', 'string', 'max:100'],
            'attribute_code' => ['required', 'string', 'alpha_dash', 'max:100', Rule::unique('attributes', 'attribute_code')->ignore(
                \request()->route('attribute') ? request()->route('attribute')->attribute_id : null,
                'attribute_id'
            )],
            'attribute_set' => ['required'],
            'attribute_group' => ['required'],
            'frontend_type' => [(request()->route('attribute') ? 'nullable' : 'required'), Rule::in(['input', 'select'])],
            'is_required' => ['nullable', 'boolean'],
            'options' => ['required_if:frontend_type,select', 'array'],
        ];

        if (\request('frontend_type') == 'select') {
            $rules['options.*.value'] = ['required_if:frontend_type,select', 'string', 'max:100'];
        }

        return Validator::make(\request()->all(), $rules);
    }
}
