<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * List contacts with search, filter by group and simple pagination.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $groupId = $request->query('group');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(50, (int) $request->query('perPage', 10)));

        $query = Contact::query()->with('group');

        if ($q !== '') {
            $qLower = mb_strtolower($q);

            $query->where(function ($sub) use ($qLower) {
                $sub->whereRaw('LOWER(name) LIKE ?', ['%' . $qLower . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $qLower . '%'])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . $qLower . '%'])
                    ->orWhereRaw('LOWER(note) LIKE ?', ['%' . $qLower . '%']);
            });
        }

        if (!empty($groupId)) {
            $query->where('group_id', (int) $groupId);
        }

        $total = $query->count();

        $contacts = $query
            ->orderByDesc('id')
            ->forPage($page, $perPage)
            ->get();

        $groups = Group::orderBy('name')->get();

        $totalPages = (int) ceil($total / $perPage);

        return view('contacts.index', [
            'contacts'   => $contacts,
            'groups'     => $groups,
            'q'          => $q,
            'groupId'    => $groupId,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Handle create form (GET - show form, POST - save).
     */
    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $this->validateForm($request);

            $contact = new Contact();
            $this->hydrate($contact, $data);
            $contact->save();

            return redirect()->route('contacts_index')
                ->with('status', 'Contact created successfully.');
        }

        $groups = Group::orderBy('name')->get();
        $item = new Contact();

        return view('contacts.form', [
            'item'   => $item,
            'groups' => $groups,
            'isEdit' => false,
        ]);
    }

    /**
     * Handle edit form (GET - show form, POST - update).
     */
    public function edit(int $id, Request $request)
    {
        $contact = Contact::findOrFail($id);

        if ($request->isMethod('post')) {
            $data = $this->validateForm($request);

            $this->hydrate($contact, $data);
            $contact->save();

            return redirect()->route('contacts_index')
                ->with('status', 'Contact updated successfully.');
        }

        $groups = Group::orderBy('name')->get();

        return view('contacts.form', [
            'item'   => $contact,
            'groups' => $groups,
            'isEdit' => true,
        ]);
    }

    /**
     * Delete contact.
     */
    public function delete(int $id, Request $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            $contact = Contact::find($id);
            if ($contact) {
                $contact->delete();
            }
        }

        return redirect()->route('contacts_index')
            ->with('status', 'Contact deleted.');
    }

    /**
     * Validate request data for create/update.
     */
    protected function validateForm(Request $request): array
    {
        return $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'string', 'email', 'max:180'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'note'     => ['nullable', 'string'],
            'group_id' => ['nullable', 'integer', 'exists:contact_groups,id'],
        ]);
    }

    /**
     * Fill the model from validated data.
     */
    protected function hydrate(Contact $contact, array $data): void
    {
        $contact->name = (string) ($data['name'] ?? '');
        $contact->email = (string) ($data['email'] ?? '');

        $phone = $data['phone'] ?? null;
        $note  = $data['note'] ?? null;

        $contact->phone = $phone !== '' ? $phone : null;
        $contact->note  = $note !== '' ? $note : null;

        $gid = $data['group_id'] ?? null;
        $contact->group_id = $gid ? (int) $gid : null;
    }
}
