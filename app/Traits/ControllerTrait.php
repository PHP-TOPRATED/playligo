<?php
namespace app\Traits;

use Illuminate\Http\Request;

trait ControllerTrait
{
    protected $parm;
    protected $search;
    protected $filters;

    public function search(Request $request)
    {
        if (!array_get($request->all(), 'reset_form')) {
            $request->session()->put($this->parm['search'], $request->all());

            $message = trans('form.search_content');
        } else {
            $request->session()->forget($this->parm['search']);

            $message = trans('form.clear_search_content');
            return response()->json(['cleared' => true]);
        }

        return back();
    }

    // Validate input
    public function validateInput($input, $rules)
    {
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return General::jsonBadResponse(implode("<br/>", $validator->errors()->all()));
        } else {
            return null;
        }
    }
}
