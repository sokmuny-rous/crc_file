<x-layouts.app>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">User Management</h3>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form action="{{ route('admin.users.role', $user) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="form-select form-select-sm" style="max-width: 170px;">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ strtoupper($role) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
                            </form>
                        </td>
                        <td class="text-end">
                            @if(! auth()->user()->is($user))
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-secondary">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
</x-layouts.app>
