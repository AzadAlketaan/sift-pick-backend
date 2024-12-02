<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use JsValidator;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['permission:view_all_products'])->only('index');
        $this->middleware(['permission:add_product'])->only('create');        
        $this->middleware(['permission:edit_product'])->only('edit');
        $this->middleware(['permission:show_product'])->only('show');
        $this->middleware(['permission:delete_product'])->only('destroy');
    }

    public function index()
    {//dd('ss');
        $users = User::all();
        return Inertia::render('Users', [
            'users' => $users,
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        return Inertia::render('Products/create', [
            'validator' => JsValidator::make($this->readingListValidation(), $this->ValidationMessages)
        ]);
    }   

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->readingListValidation(), $this->ValidationMessages);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput($request->input());
        }

        try {
            $readingList = DB::transaction(function () use ($request) {
                $readingList = new ReadingList();
                $readingList->fill(
                    Arr::except($request->toArray(), ['order', 'is_suggested_list']) + [
                        'creation_source'           => ListCreationSource::MAJARRA->value,
                        'privacy'                   => ListPrivacy::PUBLIC->value,
                        'user_id'                   => auth()->id(),
                        'is_suggested_list'         => (bool) request()->is_suggested_list,
                        'order'                     => (bool) request()->is_suggested_list ? request()->order : null,
                        'set_suggested_list_date'   => (bool) request()->is_suggested_list ? now() : null,
                        'sharing_link'              => $this->shouldGenerateLink(ListPrivacy::PUBLIC->value, $request->status)
                                                        ? $this->firebaseService->generateDynamicLink($readingList)
                                                        : NUll
                    ]
                )->save();

                if ($request->hasFile("image")) {
                    $path = $this->storage->put("/photos/readingList", $request->file("image"));
                    $readingList->image = '/' . $path;
                }

                $readingList->save();

                $this->syncContents($readingList, $request->content_ids);
                $this->syncThawanies($readingList, $request->thawany_ids);

                /** Store Log */
                ReadingListLog::create([
                    'user_id' => auth()->id(),
                    'list_id' => $readingList->id,
                    'action'  => ListLogActions::CREATE->value,
                    'platform' => 'web'
                ]);

                return $readingList; 
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create Reading List: ' . $e->getMessage())->withInput($request->input());
        }

        return redirect(route("readingList.edit", $readingList->id))->with('success', 'Reading List Created successfully');
    }

    public function edit(Product $product): JsonResponse
    {
        if ($readingList->title == ReadingList::DefaultAudioTitle) {
            return back()->with('error', 'The list is a system list, you cannot update it.');
        }

        return view('dashboard.readingList.edit')
            ->with([
                'readingList' => $readingList,
                'page_title' => 'Edit ' . $readingList->title ,
                'validator' =>  JsValidator::make($this->readingListValidation('edit', $readingList->id), $this->ValidationMessages),
                "maxOrder" => $this->maxOrder
            ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make(request()->all(), $this->readingListValidation('edit', $readingList->id), $this->ValidationMessages);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput($request->input());
        }

        try {
            DB::transaction(function () use ($request, $readingList) {

                $readingList->fill([
                    ...Arr::except($request->toArray(), ['is_suggested_list', 'order', 'status', 'type', 'creation_display_name']),
                    ...[
                        'is_suggested_list'       => (bool) request()->is_suggested_list,
                        'order'                   => (bool) request()->is_suggested_list ? request()->order : null,
                        'creation_display_name'   => request()->creationSource != ListCreationSource::USER->value
                                                        ? request()->creation_display_name
                                                        : $readingList->user?->display_name,
                        'status'                  => request()->creationSource != ListCreationSource::USER->value
                                                        ? request()->status
                                                        : ListStatus::PUBLISHED->value,
                        'type'                    => request()->creationSource == ListCreationSource::MAJARRA->value
                                                        ? request()->type
                                                        : ListTypes::NORMAL->value,
                        'set_suggested_list_date' => (bool) request()->is_suggested_list && !$readingList->is_suggested_list
                                                        ? now()
                                                        : $readingList->set_suggested_list_date
                    ]
                ]);

                if ($request->hasFile("image")) {
                    if ($this->storage->exists($readingList->image)) {
                        $this->storage->delete($readingList->image);
                    }
                    $path = $this->storage->put("/photos/readingList", $request->file("image"));
                    $readingList->image =  '/' . $path;
                }

                $readingList->sharing_link = $this->shouldGenerateLink(
                        $readingList->privacy, $request->status, $request->hasFile("image"),
                        ($request->status !=  $readingList->status || $request->description !=  $readingList->description || $request->title !=  $readingList->title)
                    )   ? $this->firebaseService->generateDynamicLink($readingList)
                    : NUll;
                $readingList->save();
                
                $this->syncContents($readingList, $request->content_ids);
                $this->syncThawanies($readingList, $request->thawany_ids);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update Reading List: ' . $e->getMessage())->withInput($request->input());
        }

        return redirect(route("readingList.edit", $readingList->id))->with('success', 'Reading List Updated successfully');
    } 
    
    public function show(Product $product): JsonResponse
    {
        return view('dashboard.readingList.show')
            ->with([
                'readingList'   => $readingList,
                'page_title'    => 'Show ' . $readingList->title,
                'creator_email' => $readingList->creation_source == ListCreationSource::HIDDEN_USER->value
                                    ? ReadingListLog::getCreatorUserEmail($readingList->id)
                                    : $readingList->user?->email
            ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($readingList->title == ReadingList::DefaultAudioTitle) {
            return response()->json([
                'code' => 0,
                'msg' => 'The list is a system list, you cannot delete it.'
            ]);
        }

        $this->storage->delete("photos/readingList/". basename($readingList->image));
        $readingList->delete();

        return response()->json([
            'code' => 1,
            'msg' => 'Reading List Deleted successfully'
        ]);
    }

    public function readingListValidation(string $type = 'create', string $parameter = null)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255',
                Rule::unique('reading_lists', 'title')
                ->where(function ($query) {
                    $query->whereCreationSource(request()->creationSource)
                        ->whereStatus(request()->status)
                        ->whereType(ListTypes::NORMAL->value);
                }),
                new CheckForbiddenWords('title')
            ],
            'status' => ['required_if:creationSource,majarra', 'in:' . ListStatus::PUBLISHED->value . ',' . ListStatus::DRAFT->value],
            'creation_display_name' => ['required_if:creationSource,majarra', 'nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255', new CheckForbiddenWords('description')],
            "image" => ["required", "image", 'mimes:jpeg,png,jpg,gif,svg', 'max:2000'],
            'order' => ['required_if:is_suggested_list,on' , 'nullable', 'integer', 'min:1'],
            'type'  => ['required_if:creationSource,majarra' , 'nullable', 'in:' . ListTypes::SYSTEM->value . ',' . ListTypes::NORMAL->value],
            'content_ids' => ['string', 'nullable',
                Rule::requiredIf(function () {
                    return request()->status == ListStatus::PUBLISHED->value && !isset(request()->thawany_ids) ? true : false;
                })
            ],
            'thawany_ids' => ['string', 'nullable',
                Rule::requiredIf(function () {
                    return request()->status == ListStatus::PUBLISHED->value && !isset(request()->content_ids) ? true : false;
                })
            ],
            'items_order' => ['required', 'string',
                'in:' . ItemOrder::MOST_READ->value .
                    ',' . ItemOrder::RECENTLY_ADDED->value .
                    ',' . ItemOrder::RECENTLY_PUBLISHED->value.
                    ',' . ItemOrder::RANDOM_ORDER->value
                ]
        ];

        if ($type != 'create') {
            $rules['title'] = ['required', 'string', 'max:255',
                Rule::unique('reading_lists', 'title')
                ->where(function ($query) use ($parameter) {
                    $query->whereCreationSource(request()->creationSource)
                    ->whereStatus(request()->status)
                    ->wherePrivacy(request()->privacy)
                    ->whereType(ListTypes::NORMAL->value)
                    ->where('id', '<>', $parameter)
                    ->when(request()->creationSource == ListCreationSource::USER->value, function (RuleBuilder $builder) use ($parameter){
                        $builder->whereUserId(ReadingList::find($parameter)->user_id);
                    });
                })
            ];

            $rules['image'] = ["image", 'mimes:jpeg,png,jpg,gif,svg', 'max:2000', 
                Rule::requiredIf(function () {
                    if (isset(request()->existsImage)) {
                        return request()->existsImage == 'notExists' ? true : false;
                    }
                    return false;
                })
            ];
        }

        return $rules;
    }

    protected $ValidationMessages = [
        'content_ids.required' => 'You must select at least one thawany or content item.',
        'thawany_ids.required' => 'You must select at least one thawany or content item.'
    ];
}