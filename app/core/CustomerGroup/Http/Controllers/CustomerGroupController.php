<?php

namespace Core\CustomerGroup\Http\Controllers;

use Core\CustomerGroup\Application\UseCases\CreateCustomerGroup;
use Core\CustomerGroup\Application\DTOs\CreateCustomerGroupRequest;
use Core\CustomerGroup\Application\DTOs\DeleteCustomerGroupRequest as DTOsDeleteCustomerGroupRequest;
use Core\CustomerGroup\Application\DTOs\ShowCustomerGroupRequest;
use Core\CustomerGroup\Application\UseCases\DeleteCustomerGroup;
use Core\CustomerGroup\Application\UseCases\ShowCustomerGroup;
use Core\CustomerGroup\Application\UseCases\UpdateCustomerGroup;
use Core\CustomerGroup\Http\Requests\CreateCustomerGroupRequest as FormRequest;
use Core\CustomerGroup\Http\Requests\DeleteCustomerGroupRequest;
use Core\CustomerGroup\Http\Requests\IndexCustomerGroupRequest;
use Core\CustomerGroup\Http\Requests\ShowCustomerGroupRequest as RequestsShowCustomerGroupRequest;
use Core\CustomerGroup\Http\Requests\UpdateCustomerGroupRequest;
use Core\CustomerGroup\Application\Queries\IndexQuery;

class CustomerGroupController
{
    public function store(FormRequest $request, CreateCustomerGroup $useCase)
    {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function update(
        UpdateCustomerGroupRequest $request,
        UpdateCustomerGroup $useCase,
        string $id
    ) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function index(
        IndexCustomerGroupRequest $request,
        IndexQuery $useCase
    ) {
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function show(
        RequestsShowCustomerGroupRequest $request,
        ShowCustomerGroup $useCase,
        string $id
    ) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
    public function destroy(
        DeleteCustomerGroupRequest $request,
        DeleteCustomerGroup $useCase,
        string $id
    ) {
        $request->merge(['id' => $id]);
        $entity = $useCase->handle($request->all());
        return response()->json(['message' => $entity]);
    }
}
