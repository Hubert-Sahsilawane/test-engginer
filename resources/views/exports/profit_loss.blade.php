<table>
    <thead>
        <tr>
            <th colspan="{{ count($bulanList) + 1 }}" style="background-color: yellow;">
                Contoh Laporan Profit/Loss
            </th>
        </tr>
        <tr style="background-color: yellow;">
            <th>Category</th>
            @foreach ($bulanList as $bulan)
                <th>{{ $bulan }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data['income'] as $kategori => $values)
            <tr style="background-color: #CCFFCC;">
                <td>{{ $kategori }}</td>
                @foreach ($bulanList as $bulan)
                    <td>{{ number_format($values[$bulan] ?? 0) }}</td>
                @endforeach
            </tr>
        @endforeach

        <tr style="font-weight: bold; background-color: #99FF99;">
            <td>Total Income</td>
            @foreach ($bulanList as $bulan)
                <td>{{ number_format($data['total_income'][$bulan] ?? 0) }}</td>
            @endforeach
        </tr>

        @foreach ($data['expense'] as $kategori => $values)
            <tr style="background-color: #FFCC99;">
                <td>{{ $kategori }}</td>
                @foreach ($bulanList as $bulan)
                    <td>{{ number_format($values[$bulan] ?? 0) }}</td>
                @endforeach
            </tr>
        @endforeach

        <tr style="font-weight: bold; background-color: #FF9966;">
            <td>Total Expense</td>
            @foreach ($bulanList as $bulan)
                <td>{{ number_format($data['total_expense'][$bulan] ?? 0) }}</td>
            @endforeach
        </tr>

        <tr style="font-weight: bold;">
            <td>Net Income</td>
            @foreach ($bulanList as $bulan)
                <td>{{ number_format(($data['total_income'][$bulan] ?? 0) - ($data['total_expense'][$bulan] ?? 0)) }}</td>
            @endforeach
        </tr>
    </tbody>
</table>
