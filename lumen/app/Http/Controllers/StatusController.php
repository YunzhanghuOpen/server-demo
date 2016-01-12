<?php
/**
 * Created by PhpStorm.
 * User: philiptang
 * Date: 12/30/15
 * Time: 17:01
 */

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class StatusController
{

    /**
     * server 运行状态
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /status 查询运行状态
     * @apiName PostRegister
     * @apiGroup Status
     */
    public function index(Request $request)
    {
        $currentPage = 1;
        if ($request->has('page')) {
            $currentPage = $request->input('page');
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
        }

        $per_page = $request->has('per_page') ? $request->input('per_page') : 20;
        $result = Notice::orderBy('id', 'DESC')->paginate($per_page)->toArray();

        return view('status', ['notice' => $result['data'], 'current_page' => $currentPage, 'last_page' => $result['last_page']]);
    }

}