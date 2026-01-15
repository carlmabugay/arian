<div>
    <table class="fi-ta-table">
        <thead>
        <tr>
            <th class="fi-ta-header-cell">User</th>
            <th class="fi-ta-header-cell">Assigned By</th>
            <th class="fi-ta-header-cell">Assigned on</th>
            <th class="fi-ta-header-cell">Returned on</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($assignments as $assignment)
            <tr class="fi-ta-row">
                <td class="fi-ta-cell">
                    <div class="fi-ta-text">{{ $assignment->user->name }}</div>
                </td>
                <td class="fi-ta-cell">
                    <div class="fi-ta-text">{{ $assignment->assignedBy->name }}</div>
                </td>
                <td class="fi-ta-cell">
                    <div class="fi-ta-text">{{ $assignment->assigned_at?->format('M d, Y') }}</div>
                </td>
                <td class="fi-ta-cell">
                    <div class="fi-ta-text">{{ $assignment->returned_at?->format('M d, Y') ?? '—' }}</div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
