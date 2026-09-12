@props(['name' => 'sun'])
@php
$paths = [
 'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.4 1.4m11.2 11.2L19 19M5 19l1.4-1.4M17.6 6.4 19 5"/>',
 'dashboard' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
 'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Zm6-3v15m6-12v15"/>',
 'farm' => '<path d="M3 19h18M5 15l2-10h10l2 10H5Zm1-5h12m-6-5v10m-3 0-1 4m7-4 1 4"/>',
 'bolt' => '<path d="m13 2-9 12h7l-1 8 10-13h-7l0-7Z"/>',
 'chart' => '<path d="M4 3v17h17M8 15V9m5 6V5m5 10v-7"/>',
 'alert' => '<path d="m10.3 4-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3l-8-14a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 4h.01"/>',
 'trend' => '<path d="m3 17 6-6 4 4 8-11m-6 0h6v6"/>',
 'code' => '<path d="m8 6-6 6 6 6m8-12 6 6-6 6m-3-16-2 20"/>',
 'book' => '<path d="M12 6C9 3 5 3 2 4v15c4-1 7-1 10 2 3-3 6-3 10-2V4c-3-1-7-1-10 2Zm0 0v15"/>',
 'arrow' => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
 'down' => '<path d="m6 9 6 6 6-6"/>',
 'plus' => '<path d="M12 5v14M5 12h14"/>',
 'download' => '<path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/>',
 'leaf' => '<path d="M20 3C9 1 2 7 5 15c3 8 17 5 15-12ZM4 21 16 9"/>',
 'users' => '<circle cx="9" cy="7" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3m1-17a3 3 0 0 1 0 6m2 5a5 5 0 0 1 3 5"/>',
 'pin' => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
 'search' => '<circle cx="10" cy="10" r="7"/><path d="m15 15 6 6"/>',
 'filter' => '<path d="M3 5h18M6 12h12M10 19h4"/>',
 'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
 'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
 'check' => '<path d="m4 12 5 5L20 6"/>',
 'edit' => '<path d="m16 3 5 5L9 20l-6 1 1-6L16 3Zm-3 3 5 5"/>',
 'lock' => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4m-4 4v3"/>',
 'logout' => '<path d="M9 4H4v16h5m6-12 4 4-4 4m-7-4h13"/>',
 'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>',
 'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10h.01"/>',
];
@endphp
<svg {{ $attributes->merge(['class'=>'icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? $paths['sun'] !!}</svg>
