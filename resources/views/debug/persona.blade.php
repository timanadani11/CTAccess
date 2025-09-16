<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Debug Persona {{ $persona->idPersona }}</title>
  <style>
    body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 20px; }
    .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
    .title { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
    .subtitle { font-size: 18px; font-weight: 600; margin: 16px 0 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    th { background: #f9fafb; }
    .muted { color: #6b7280; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; background: #eef2ff; color: #3730a3; font-size: 12px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
    img.photo { max-width: 160px; border-radius: 8px; border: 1px solid #e5e7eb; }
    .meta { font-size: 12px; color: #6b7280; }
  </style>
</head>
<body>
  <div class="card">
    <div class="title">Persona #{{ $persona->idPersona }}</div>
    <div class="grid">
      <div>
        <div><span class="muted">Documento:</span> <strong>{{ $persona->documento ?? '—' }}</strong></div>
        <div><span class="muted">Nombre:</span> <strong>{{ $persona->Nombre }}</strong></div>
        <div><span class="muted">Tipo:</span> <span class="badge">{{ $persona->TipoPersona }}</span></div>
        <div class="meta">Creado: {{ $persona->created_at }} | Actualizado: {{ $persona->updated_at }}</div>
      </div>
      <div>
        @if($persona->Foto)
          <img class="photo" src="{{ $persona->Foto }}" alt="Foto de {{ $persona->Nombre }}">
        @else
          <div class="muted">Sin foto</div>
        @endif
      </div>
    </div>
  </div>

  <div class="card">
    <div class="subtitle">Portátiles ({{ $persona->portatiles->count() }})</div>
    @if($persona->portatiles->isEmpty())
      <div class="muted">No hay portátiles asociados.</div>
    @else
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>QR</th>
            <th>Marca</th>
            <th>Modelo</th>
          </tr>
        </thead>
        <tbody>
          @foreach($persona->portatiles as $p)
            <tr>
              <td>{{ $p->portatil_id }}</td>
              <td>{{ $p->qrCode }}</td>
              <td>{{ $p->marca }}</td>
              <td>{{ $p->modelo }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="card">
    <div class="subtitle">Vehículos ({{ $persona->vehiculos->count() }})</div>
    @if($persona->vehiculos->isEmpty())
      <div class="muted">No hay vehículos asociados.</div>
    @else
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Placa</th>
          </tr>
        </thead>
        <tbody>
          @foreach($persona->vehiculos as $v)
            <tr>
              <td>{{ $v->id }}</td>
              <td>{{ $v->tipo }}</td>
              <td>{{ $v->placa }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</body>
</html>
