<?php

namespace App\Http\Controllers;

use App\Helpers\Folder;
use App\Http\Requests\KeywordEditRequest;
use App\Http\Requests\KeywordRequest;
use App\Keyword;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;

class KeywordsController extends Controller
{
    use ControllerTrait;

    public function __construct()
    {
        $this->parm['search'] = 'src_keyword';
    }

    /**
     * Returns view of services
     */
    public function index()
    {
        $keyword = new Keyword;

        $search = session()->get($this->parm['search']);
        $keywords = $keyword->filter($search)->getPaginated();

        $total_record = $keywords->total();
        $page_title = trans('keywords.general');
        $filter = 'admin.keywords.filter';

        return view('admin.keywords.list', compact(
            'keywords',
            'filter',
            'search',
            'total_record',
            'page_title'
        ));
    }

    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = trans('keywords.new');
        $back_url = route('admin.keywords.index');
        $keyword = new Keyword;

        return view('admin.keywords.edit', compact(
            'page_title',
            'back_url',
            'keyword'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param KeywordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(KeywordRequest $request)
    {
        $keyword = Keyword::create($request->except(
            '_token',
            'image'
        ));
        if ($file = $request->file('image')) {
            Folder::saveImage($keyword, Folder::KEYWORDS_DIR, $file, ['width' => 200, 'height' => 150, 'folder' => Folder::KEYWORDS_THUMB_DIR]);
        }

        return response()->json([
            'message' => trans('messages.store_successful'),
            'redirect' => route('admin.keywords.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Keyword $keyword
     * @return \Illuminate\Http\Response
     */
    public function edit(Keyword $keyword)
    {
        $page_title = trans('keywords.edit_title');
        $back_url = route('admin.keywords.index');

        return view('admin.keywords.edit', compact(
            'page_title',
            'back_url',
            'keyword'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param KeywordEditRequest $request
     * @param Keyword $keyword
     * @return \Illuminate\Http\Response
     */
    public function update(KeywordEditRequest $request, Keyword $keyword)
    {
        $keyword->update($request->except(
            '_token',
            'image'
        ));
        if ($file = $request->file('image')) {
            Folder::saveImage($keyword, Folder::KEYWORDS_DIR, $file, ['width' => 200, 'height' => 150, 'folder' => Folder::KEYWORDS_THUMB_DIR]);
        }

        return response()->json([
            'message' => trans('messages.store_successful'),
            'redirect' => route('admin.keywords.edit', $keyword),
        ]);
    }

    public function delete(Keyword $keyword)
    {
        return view('admin.keywords.delete', compact('keyword'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Keyword $keyword
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Keyword $keyword, Request $request)
    {
        Folder::removeImage($keyword);
        $keyword->delete();

        return back()->with('status', trans('messages.delete_successful'));
    }
}
