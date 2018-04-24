<?php

namespace App\Http\Controllers;

use App\Library\Util\ImageUtil;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     * @author Xiaoming <minco.wang@outlook.com>
     */
    public function upload(Request $request)
    {
        $input = $request->only('file', 'target');
        if (!$request->hasFile('file')) {
            return redirect()->back();
        }
        $targetName = 'images/' . $input['target'];
        $image      = ImageUtil::upload($request->file->getPathname(), $targetName);
        return response()->json($image);
    }
}
