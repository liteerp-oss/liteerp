<?php

namespace Core\Business\Http\Controllers;

use Core\Business\Application\UseCases\CreateBusiness;
use Core\Business\Application\Queries\IndexQuery;
use Core\Business\Application\UseCases\ShowBusiness;
use Core\Business\Application\UseCases\UpdateBusiness;
use Core\Business\Http\Requests\CreateBusinessRequest as FormRequest;
use Core\Business\Http\Requests\IndexBusinessRequest;
use Core\Business\Http\Requests\ShowBusinessRequest;
use Core\Business\Http\Requests\UpdateBusinessRequest;
use Illuminate\Support\Facades\Auth;

class BusinessController
{
    public function index(IndexBusinessRequest $request, IndexQuery $useCase) {
        $request->merge(['user_id' => Auth::guard('sanctum')->user()->id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function store(FormRequest $request, CreateBusiness $useCase)
    {
        $request->merge(['user_id' => Auth::guard('sanctum')->user()->id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function show(ShowBusinessRequest $request,string $id, ShowBusiness $useCase) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function update(UpdateBusinessRequest $request, UpdateBusiness $useCase,string $id)
    {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}