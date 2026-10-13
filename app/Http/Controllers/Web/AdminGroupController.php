<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGroupController extends Controller
{
    public function index(): View
    {
        $groups = Group::query()
            ->orderBy(Group::DESCRIPTION)
            ->paginate(20);

        return view('admin.groups.index', [
            'groups' => $groups,
        ]);
    }

    public function new(): View
    {
        $group = new Group;

        return $this->editOrNew($group);
    }

    public function edit(Group $group): View
    {
        return $this->editOrNew($group);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $group = $request->input('id')
            ? Group::findOrFail($request->input('id'))
            : new Group;

        $group->fill($validated);
        if (!in_array($group->name, [
            Group::ADMINISTRATORS_GROUP,
            Group::DOCUMENT_CREATORS_GROUP,
            Group::DOCUMENT_APPROVERS_GROUP,
        ])) {
            $group->name = $this->normalizeName($group->description);
        }
        $group->save();


        // Sync group members
        $memberIds = array_filter(
            array_map('trim', explode(',', $request->input('member_ids', ''))),
            fn ($id) => is_numeric($id)
        );
        $group->users()->sync($memberIds);

        return redirect()->route('admin.groups.index');
    }

    public function destroy(Group $group): RedirectResponse|JsonResponse
    {
        if (in_array($group->name, [
            Group::ADMINISTRATORS_GROUP,
            Group::DOCUMENT_CREATORS_GROUP,
            Group::DOCUMENT_APPROVERS_GROUP,
        ])) {
            return redirect()->route('admin.groups.index')->withErrors(['msg' => 'No se puede eliminar el grupo <b>' . $group->description . '</b>']);;
        }

        $group->delete();

        return redirect()->route('admin.groups.index');
    }

    private function editOrNew(Group $group): View
    {
        $users = User::query()
            ->orderBy(User::NAME)
            ->get();

        $memberIds = $group->users->pluck('id')->implode(',');

        return view('admin.groups.form', [
            'group' => $group,
            'users' => $users,
            'memberIds' => $memberIds,
        ]);
    }

    /**
     * Normalize file name by removing diacritics, replacing spaces with underscores, and converting to lowercase
     */
    private function normalizeName(string $name): string
    {
        // Normalize: remove diacritics and convert special characters
        $normalized = $this->removeDiacritics($name);

        // Replace spaces with underscores
        $normalized = str_replace(' ', '_', $normalized);

        // Convert to lowercase
        $normalized = strtolower($normalized);

        return $normalized;
    }

    /**
     * Remove diacritical marks from a string
     */
    private function removeDiacritics(string $str): string
    {
        // Use PHP's Normalizer to decompose accented characters
        $str = normalizer_normalize($str, \Normalizer::FORM_D);

        // Remove all combining diacritical marks
        $str = preg_replace('/[\x{0300}-\x{036f}]/u', '', $str);

        // Handle special cases
        $replacements = [
            'ñ' => 'n',
            'Ñ' => 'n',
        ];

        return strtr($str, $replacements);
    }
}
