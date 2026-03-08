@php
$assetSections = collect($assets)->groupBy('section');
$liabSections = collect($liabilities)->groupBy('section');

$totalAssets = collect($assets)->sum('value');
$totalLiabs = collect($liabilities)->sum('balance');
@endphp

<div class="space-y-6">

    <h2 class="text-xl font-bold">Bilan au décès</h2>

    <div>
        <h3 class="font-semibold">Actif</h3>
        <table class="w-full text-sm">
            @foreach($assetSections as $section => $rows)
            <tr>
                <td>{{ $section }}</td>
                <td class="text-right">
                    ${{ number_format($rows->sum('value'), 2) }}
                </td>
            </tr>
            @endforeach
            <tr class="font-bold border-t">
                <td>Total Actif</td>
                <td class="text-right">
                    ${{ number_format($totalAssets, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <div>
        <h3 class="font-semibold">Passif</h3>
        <table class="w-full text-sm">
            @foreach($liabSections as $section => $rows)
            <tr>
                <td>{{ $section }}</td>
                <td class="text-right">
                    ${{ number_format($rows->sum('balance'), 2) }}
                </td>
            </tr>
            @endforeach
            <tr class="font-bold border-t">
                <td>Total Passif</td>
                <td class="text-right">
                    ${{ number_format($totalLiabs, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="text-lg font-bold border-t pt-4">
        Liquidités nettes disponibles :
        ${{ number_format($totalAssets - $totalLiabs, 2) }}
    </div>

</div>
