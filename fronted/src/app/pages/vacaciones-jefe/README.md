# Componente Vacaciones Jefe

## Descripción
Este componente permite al jefe de personal gestionar las solicitudes de vacaciones de los empleados a su cargo. Proporciona una interfaz completa para ver, filtrar, buscar, aprobar y rechazar solicitudes de vacaciones.

## Funcionalidades

### 📊 Dashboard de Estadísticas
- Muestra el número total de solicitudes
- Contador de solicitudes pendientes
- Contador de solicitudes aprobadas
- Contador de solicitudes rechazadas

### 🔍 Filtros y Búsqueda
- Filtro por estado (Todos, Pendientes, Aprobadas, Rechazadas)
- Búsqueda por nombre, documento, cargo o área del empleado
- Botón de actualización para refrescar datos

### 📋 Tabla de Solicitudes
- Información completa del empleado (nombre, documento, cargo, área)
- Detalles de la solicitud (motivo, fechas, días solicitados)
- Estado visual con colores diferenciados
- Acciones para aprobar/rechazar solicitudes pendientes

### ✅ Gestión de Solicitudes
- Modal para aprobar solicitudes con comentario opcional
- Modal para rechazar solicitudes con comentario opcional
- Actualización automática de estadísticas después de cada acción

## Estructura de Archivos

```
vacaciones-jefe/
├── vacaciones-jefe.component.ts      # Lógica del componente
├── vacaciones-jefe.component.html    # Template HTML
├── vacaciones-jefe.component.scss    # Estilos CSS
├── vacaciones-jefe.component.spec.ts # Tests unitarios
└── README.md                         # Esta documentación
```

## Modelos de Datos

### SolicitudVacacionesJefe
```typescript
interface SolicitudVacacionesJefe {
  idVacaciones?: number;
  motivo: string;
  fechaInicio: string;
  fechaFinal: string;
  dias: number;
  contratoId: number;
  estado?: 'pendiente' | 'aprobado' | 'rechazado';
  empleado?: {
    id: number;
    nombre: string;
    apellido: string;
    numDocumento: string;
    cargo: string;
    area: string;
  };
  contrato?: {
    id: number;
    tipoContrato: string;
    fechaInicio: string;
    fechaFin?: string;
  };
  createdAt?: string;
  updatedAt?: string;
}
```

### RespuestaSolicitud
```typescript
interface RespuestaSolicitud {
  idVacaciones: number;
  estado: 'aprobado' | 'rechazado';
  comentario?: string;
}
```

## Servicios Utilizados

### VacacionesJefeService
- `obtenerSolicitudesVacaciones()`: Obtiene todas las solicitudes
- `obtenerSolicitud(id)`: Obtiene una solicitud específica
- `aprobarSolicitud(respuesta)`: Aprueba una solicitud
- `rechazarSolicitud(respuesta)`: Rechaza una solicitud
- `obtenerEstadisticas()`: Obtiene estadísticas generales
- `filtrarPorEstado(estado)`: Filtra por estado
- `buscarPorEmpleado(termino)`: Busca por empleado

## Rutas

```typescript
{ path: 'vacaciones-jefe', component: VacacionesJefeComponent, canActivate: [AuthGuard] }
```

## Dependencias

- **Bootstrap 5.3.3**: Para el diseño responsive
- **Font Awesome 6.0.0**: Para los iconos
- **Angular Forms**: Para el manejo de formularios
- **Angular Common**: Para directivas comunes

## Uso

1. Navegar a `/vacaciones-jefe`
2. Ver las estadísticas en el dashboard
3. Usar los filtros para encontrar solicitudes específicas
4. Hacer clic en los botones de acción (✓ o ✗) para gestionar solicitudes
5. Agregar comentarios opcionales en el modal
6. Confirmar la acción

## Características Técnicas

- **Responsive**: Se adapta a diferentes tamaños de pantalla
- **Real-time**: Actualiza datos automáticamente
- **Accessible**: Incluye atributos ARIA y navegación por teclado
- **Type-safe**: Utiliza TypeScript para type safety
- **Modular**: Componente standalone con sus propias dependencias

## Estados Visuales

- **Pendiente**: Fondo amarillo claro, badge amarillo
- **Aprobado**: Fondo verde claro, badge verde
- **Rechazado**: Fondo rojo claro, badge rojo

## Notas de Desarrollo

- El componente requiere autenticación (AuthGuard)
- Las solicitudes se cargan automáticamente al inicializar
- Los filtros se aplican en tiempo real
- Las acciones de aprobar/rechazar actualizan inmediatamente la interfaz
- Se incluyen animaciones CSS para mejor UX 