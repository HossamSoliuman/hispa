@props([
    'title'    => '',
    'value'    => '',
    'icon'     => 'bi bi-graph-up',
    'gradient' => null,
    'badge'    => null,
    'badgeText' => null,
    'stats'    => [],
    'footer'   => null,
    'colClass' => 'col-6 col-md-4 col-lg-3',
    'url'      => null,
    'trend'    => null,
    'trendValue' => null,
])

<x-stat-card
    :title="$title"
    :value="$value"
    :icon="$icon"
    :gradient="$gradient"
    :badge="$badge"
    :badge-text="$badgeText"
    :stats="$stats"
    :footer="$footer"
    :col-class="$colClass"
    :url="$url"
    :trend="$trend"
    :trend-value="$trendValue"
/>
