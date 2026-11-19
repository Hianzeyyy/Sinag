<tbody>
    @forelse($rows as $row)
        <tr>
            <td>{{ $row['category'] }}</td>
            <td>{{ $row['male'] }} / {{ $row['male_percent'] }}%</td>
            <td>{{ $row['female'] }} / {{ $row['female_percent'] }}%</td>
            <td>{{ $row['total'] }}</td>
            @foreach(['Lesbian', 'Gay', 'Bisexual', 'Transsexual', 'Cisgender/Heterosexual', 'Non-Binary', 'Prefer Not to Say'] as $gender)
                <td>{{ $row['genders'][$gender] }}</td>
            @endforeach
        </tr>
    @empty
        <tr><td colspan="11">No records for this period</td></tr>
    @endforelse
    <tr><td colspan="11" style="font-size: 8px; text-align: left;">Male/Female row % = respective f ÷ (Male f + Female f) × 100.0; N = Male f + Female f. Gender/SOGIESC is a secondary layer; cells with f &lt; 5 are shown as &lt;5 to reduce re-identification risk. Sex values not recorded as Male or Female are excluded from this table's N and percentages.</td></tr>
</tbody>
