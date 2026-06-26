<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

class GroupController extends Controller
{
    public function index(): JsonResponse
    {
        $userGroups = Group::query()
            ->with(['users'])
            ->latest(Group::ID)
            ->get();

        return response()->json($userGroups);
    }

    public function show(Group $userGroup): JsonResponse
    {
        $userGroup->load(['users']);

        return response()->json($userGroup);
    }

    #[Post(
        path: '/api/user-groups',
        operationId: 'CreateUserGroup',
        summary: "Create new user group",
        security: [['cookieSecret' => []]],
        requestBody: new RequestBody(ref: "#/components/requestBodies/CreateUserGroupData"),
        tags: ['User Groups'],
        responses: [
            new Response(response: 201, description: "Created user group",
                content: new JsonContent(ref: "#/components/schemas/GroupResource")),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(ref: '#/components/responses/UnprocessableEntity', response: 422),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:user_groups,name'],
            'description' => ['sometimes', 'nullable', 'string'],
            'user_ids' => ['sometimes', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $userGroup = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['user_ids'])) {
            $userGroup->users()->sync($validated['user_ids']);
        }

        $userGroup->load(['users']);

        return response()->json($userGroup, 201);
    }

    public function update(Request $request, Group $userGroup): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:user_groups,name,' . $userGroup->id],
            'description' => ['sometimes', 'nullable', 'string'],
            'user_ids' => ['sometimes', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        if (array_key_exists('name', $validated)) {
            $userGroup->name = $validated['name'];
        }

        if (array_key_exists('description', $validated)) {
            $userGroup->description = $validated['description'];
        }

        $userGroup->save();

        if (array_key_exists('user_ids', $validated)) {
            $userGroup->users()->sync($validated['user_ids']);
        }

        $userGroup->load(['users']);

        return response()->json($userGroup);
    }

    public function destroy(Group $userGroup): JsonResponse
    {
        $userGroup->delete();

        return response()->json(status: 204);
    }
}
