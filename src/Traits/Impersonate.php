<?php

namespace Module\System\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

trait Impersonate
{
    /**
     * The putImpersonate function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function putImpersonate(): JsonResponse
    {
        Session::put('impersonate', $this->id);

        return response()->json([
            'success' => true,
        ], 200);
    }

    /**
     * The forgetImpersonate function
     *
     * @return JsonResponse
     */
    public function forgetImpersonate(): JsonResponse
    {
        Session::forget('impersonate');

        return response()->json([
            'success' => true,
        ], 200);
    }

    /**
     * The hasImpersonate function
     *
     * @return boolean
     */
    public function hasImpersonate(): bool
    {
        return Session::has('impersonate');
    }
}
