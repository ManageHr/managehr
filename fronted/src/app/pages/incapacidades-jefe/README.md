# Componente Incapacidades Jefe

Este componente permite a los jefes de personal gestionar las solicitudes de incapacidades de los empleados de su área.

## Funcionalidades

- **Visualización de solicitudes**: Muestra todas las solicitudes de incapacidades de empleados del área del jefe
- **Filtrado por estado**: Permite filtrar solicitudes por estado (pendiente, aprobado, rechazado)
- **Búsqueda**: Permite buscar solicitudes por nombre o documento del empleado
- **Aprobación/Rechazo**: Permite aprobar o rechazar solicitudes pendientes
- **Comentarios**: Permite agregar comentarios al aprobar o rechazar solicitudes
- **Estadísticas**: Muestra estadísticas de las solicitudes

## Estructura de Archivos

```
incapacidades-jefe/
├── incapacidades-jefe.component.ts      # Lógica del componente
├── incapacidades-jefe.component.html    # Template HTML
├── incapacidades-jefe.component.scss    # Estilos CSS
├── incapacidades-jefe.component.spec.ts # Pruebas unitarias
└── README.md                           # Documentación
```

## Dependencias

- **Angular Common**: Para directivas como `*ngFor`, `*ngIf`
- **Angular Forms**: Para formularios reactivos
- **MenuComponent**: Componente de navegación
- **IncapacidadesJefeService**: Servicio para comunicación con el backend

## Modelos de Datos

### SolicitudIncapacidadJefe
```typescript
interface SolicitudIncapacidadJefe {
  idIncapacidad?: number;
  descrip: string;
  archivo?: string;
  fechaInicio: string;
  fechaFinal: string;
  contratoId: number;
  estado?: 'pendiente' | 'aprobado' | 'rechazado';
  empleado?: {
    numDocumento: number;
    nombre: string;
    apellido: string;
  };
}
```

### RespuestaSolicitudIncapacidad
```typescript
interface RespuestaSolicitudIncapacidad {
  idIncapacidad: number;
  estado: 'aprobado' | 'rechazado';
  comentario?: string;
}
```

## Endpoints del Backend

- `GET /api/solicitudes-incapacidades-jefe` - Obtener todas las solicitudes
- `GET /api/solicitudes-incapacidades-jefe/estadisticas` - Obtener estadísticas
- `GET /api/solicitudes-incapacidades-jefe/{id}` - Obtener solicitud específica
- `PUT /api/solicitudes-incapacidades-jefe/{id}/aprobar` - Aprobar solicitud
- `PUT /api/solicitudes-incapacidades-jefe/{id}/rechazar` - Rechazar solicitud

## Uso

```html
<app-incapacidades-jefe></app-incapacidades-jefe>
```

## Características Responsive

- **Desktop**: Tabla completa con todas las columnas
- **Mobile**: Tarjetas individuales para cada solicitud

## Estados de las Solicitudes

- **Pendiente**: Solicitud en espera de revisión
- **Aprobado**: Solicitud aprobada por el jefe
- **Rechazado**: Solicitud rechazada por el jefe

## Notas Importantes

- El componente solo muestra solicitudes de empleados del área del jefe autenticado
- Las solicitudes se filtran automáticamente por el área del jefe
- El estado se maneja a nivel de frontend ya que la tabla `incapacidad` no tiene campo estado
- Se requiere autenticación JWT para acceder a las funcionalidades 