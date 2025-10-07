<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Padrón de Jugadores - {{ $campeonato->nombre_torneo }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
        }
        .page-break {
            page-break-after: always;
        }
        .player-table {
            width: 100%;
            border-collapse: collapse;
        }
        .player-table thead {
            display: table-header-group;
        }
        .player-table th, .player-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }
        .player-table .header-columns th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .equipo-header-row th {
            background-color: #fff;
            border: none;
            text-align: center;
            padding-bottom: 20px;
        }
        .equipo-nombre {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .campeonato-nombre {
            font-size: 16px;
            color: #555;
        }
        .player-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }
        .text-center {
            text-align: center;
        }
        .text-uppercase {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @foreach ($campeonato->equipos as $equipo)
        <table class="player-table">
            <thead>
                <tr class="equipo-header-row">
                    <th colspan="6">
                        <div class="campeonato-nombre">{{ $campeonato->nombre_torneo }}</div>
                        <div class="equipo-nombre">{{ $equipo->nombre }}</div>
                    </th>
                </tr>
                <tr class="header-columns">
                    <th class="text-center" style="width: 80px;">Foto</th>
                    <th>Apellidos y Nombres</th>
                    <th>DNI</th>
                    <th class="text-center" style="width: 50px;">N°</th>
                    <th style="width: 120px;">Posición</th>
                    <th class="text-center" style="width: 60px;">Edad</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($equipo->jugadores->sortBy('apellido') as $jugador)
                    <tr>
                        <td class="text-center">
                            @if ($jugador->imagen_path && file_exists(public_path('storage/' . $jugador->imagen_path)))
                                <img src="{{ public_path('storage/' . $jugador->imagen_path) }}" alt="Foto" class="player-photo">
                            @else
                                <div style="width: 60px; height: 60px; background-color: #eee; border-radius: 50%; display: inline-block;"></div>
                            @endif
                        </td>
                        <td class="text-uppercase">{{ $jugador->apellido }}, {{ $jugador->nombre }}</td>
                        <td>{{ $jugador->dni }}</td>
                        <td class="text-center" style="font-size: 20px; font-weight: bold;">{{ $jugador->numero_camiseta }}</td>
                        <td>{{ $jugador->posicion }}</td>
                        <td class="text-center">{{ $jugador->edad }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Este equipo aún no tiene jugadores registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>