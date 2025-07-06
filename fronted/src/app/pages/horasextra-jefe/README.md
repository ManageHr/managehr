# Componente Horas Extra Jefe

Este componente permite a los jefes de personal gestionar las solicitudes de horas extra de los empleados de su área.

## Funcionalidades

- Visualización de solicitudes de horas extra
- Filtrado por estado (pendiente, aprobado, rechazado)
- Búsqueda por nombre o documento
- Aprobación/Rechazo de solicitudes con comentarios
- Estadísticas
- Responsive (tabla y tarjetas)

## Estructura de Archivos

```
horasextra-jefe/
├── horasextra-jefe.component.ts
├── horasextra-jefe.component.html
├── horasextra-jefe.component.scss
├── horasextra-jefe.component.spec.ts
└── README.md
```

## Modelos de Datos

### SolicitudHorasExtraJefe
```typescript
interface SolicitudHorasExtraJefe {
  idHorasExtra?: number;
  descripcion: string;
  fecha: string;
  tipoHorasId: number;
  nHorasExtra: number;
  contratoId: number;
  estado?: 'pendiente' | 'aprobado' | 'rechazado';
  empleado?: {
    numDocumento: number;
    nombre: string;
    apellido: string;
  };
}
```

### RespuestaSolicitudHorasExtra
```typescript
interface RespuestaSolicitudHorasExtra {
  idHorasExtra: number;
  estado: 'aprobado' | 'rechazado';
  comentario?: string;
}
```

## Endpoints del Backend

- `GET /api/solicitudes-horasextra-jefe` - Obtener todas las solicitudes
- `GET /api/solicitudes-horasextra-jefe/estadisticas` - Obtener estadísticas
- `GET /api/solicitudes-horasextra-jefe/{id}` - Obtener solicitud específica
- `PUT /api/solicitudes-horasextra-jefe/{id}/aprobar` - Aprobar solicitud
- `PUT /api/solicitudes-horasextra-jefe/{id}/rechazar` - Rechazar solicitud

## Uso

```html
<app-horasextra-jefe></app-horasextra-jefe>
```

## Notas Importantes

- El estado se maneja visualmente si la tabla no tiene campo estado
- Solo muestra solicitudes de empleados del área del jefe autenticado
- Se requiere autenticación JWT para acceder a las funcionalidades 