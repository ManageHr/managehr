import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MenuComponent } from '../menu/menu.component';
import { NotificacionesService, Notificacion, NotificacionesResponse } from '../../services/notificaciones.service';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import Swal from 'sweetalert2';

declare var bootstrap: any;

@Component({
  selector: 'app-notificaciones-admin',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent, NgxPaginationModule],
  templateUrl: './notificaciones-admin.component.html',
  styleUrls: ['./notificaciones-admin.component.scss']
})
export class NotificacionesAdminComponent implements OnInit {
  // Notificaciones pendientes (estado "0")
  notificacionesPendientes: Notificacion[] = [];

  // Notificaciones aceptadas (estado "1") para el modal
  notificacionesAceptadas: Notificacion[] = [];

  // Variables de estado
  notificacionSeleccionada: Notificacion | null = null;
  usuario: any = {};

  // Filtros y paginación
  filtroUsuarios: string = '';
  itemsPerPage: number = 5;
  paginaPendientes: number = 1;
  itemsPorPaginaAceptadas: number = 5;
  paginaAceptadas: number = 1;

  constructor(private notificacionesService: NotificacionesService) {}

  ngOnInit(): void {
    this.cargarUsuario();
    this.cargarNotificaciones();
  }

  cargarUsuario(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('🔍 USUARIO CARGADO:', {
        rol: this.usuario.rol,
        userId: this.usuario.id,
        perfil: this.usuario.perfil,
        areaId: this.usuario.areaId,
        contratoId: this.usuario.contratoId
      });
    }
  }

  cargarNotificaciones(): void {
    this.notificacionesService.getAll().subscribe({
      next: (response: NotificacionesResponse) => {
        const todas = response.Notificaciones || [];
        console.log('📋 Total notificaciones en BD:', todas.length);

        // Debug: mostrar estructura de las notificaciones
        if (todas.length > 0) {
          console.log('📝 Estructura de primera notificación:', {
            id: todas[0].idNotificacion,
            contratoId: todas[0].contratoId,
            estado: todas[0].estado,
            tipo: todas[0].tipo
          });
        }

        this.filtrarNotificacionesPorRol(todas);
      },
      error: (error) => {
        console.error('Error al cargar notificaciones:', error);
        Swal.fire('Error', 'No se pudieron cargar las notificaciones', 'error');
      }
    });
  }

  filtrarNotificacionesPorRol(todas: Notificacion[]): void {
    this.notificacionesPendientes = [];
    this.notificacionesAceptadas = [];

    const rol = this.usuario.rol;
    const contratoIdUsuario = this.getContratoIdUsuario();

    console.log(`🎯 Filtrando para rol ${rol}, contratoId:`, contratoIdUsuario);

    if ([1, 4].includes(rol)) {
      // Admin - ve todo
      console.log('👑 Mostrando TODAS las notificaciones (Admin)');
      this.notificacionesPendientes = todas.filter(n => n.estado === "0");
      this.notificacionesAceptadas = todas.filter(n => n.estado === "1");
    }
    else if (rol === 2) {
      // Jefe - solo su área
      const areaId = this.usuario.areaId;
      console.log('👔 Filtrando por área:', areaId);

      // Filtrar notificaciones que tienen contrato y área
      this.notificacionesPendientes = todas.filter(n =>
        n.estado === "0" &&
        n.contrato?.area?.idArea === areaId
      );
      this.notificacionesAceptadas = todas.filter(n =>
        n.estado === "1" &&
        n.contrato?.area?.idArea === areaId
      );
    }
    else if ([3, 5].includes(rol)) {
      // Empleado - SOLO las suyas por contratoId
      if (!contratoIdUsuario) {
        console.error('❌ No se puede filtrar: contratoId del usuario no encontrado');
        Swal.fire('Info', 'No se encontró información de contrato', 'info');
        return;
      }

      console.log('👤 Filtrando por contratoId:', contratoIdUsuario);

      // CORREGIDO: usar contratoId (string) y comparar como string
      this.notificacionesPendientes = todas.filter(n =>
        n.estado === "0" && n.contratoId === contratoIdUsuario.toString()
      );

      this.notificacionesAceptadas = todas.filter(n =>
        n.estado === "1" && n.contratoId === contratoIdUsuario.toString()
      );

      console.log('📊 Resultados para empleado:');
      console.log('- Pendientes (estado 0):', this.notificacionesPendientes.length);
      console.log('- Aceptadas (estado 1):', this.notificacionesAceptadas.length);
    }

    console.log('✅ Filtrado completado:');
    console.log('- Pendientes:', this.notificacionesPendientes.length);
    console.log('- Aceptadas:', this.notificacionesAceptadas.length);
  }

  private getContratoIdUsuario(): number | null {
    // Buscar contratoId en diferentes ubicaciones posibles
    if (this.usuario.contratoId) {
      return this.usuario.contratoId;
    }
    if (this.usuario.perfil?.contratoId) {
      return this.usuario.perfil.contratoId;
    }
    if (this.usuario.contrato?.idContrato) {
      return this.usuario.contrato.idContrato;
    }

    // Para Admin, no necesitamos contratoId
    if ([1, 4].includes(this.usuario.rol)) {
      console.log('👑 Admin - no requiere contratoId');
      return null;
    }

    console.error('❌ No se encontró contratoId en:', this.usuario);
    return null;
  }

  // ACCIONES
  aceptarNotificacion(n: Notificacion): void {
    console.log('✅ Aceptando notificación:', n.idNotificacion);

    this.notificacionesService.actualizarEstado(n.idNotificacion, 1).subscribe({
      next: () => {
        console.log('✔️ Notificación aceptada correctamente');
        this.cargarNotificaciones();
        Swal.fire({
          icon: 'success',
          title: 'Aceptada',
          text: 'La notificación fue aceptada correctamente',
          timer: 2000,
          showConfirmButton: false
        });
      },
      error: (error) => {
        console.error('❌ Error al aceptar:', error);
        Swal.fire('Error', 'No se pudo aceptar la notificación', 'error');
      }
    });
  }

  rechazarNotificacion(n: Notificacion): void {
    console.log('❌ Rechazando notificación:', n.idNotificacion);

    this.notificacionesService.actualizarEstado(n.idNotificacion, 2).subscribe({
      next: () => {
        console.log('✔️ Notificación rechazada correctamente');
        this.cargarNotificaciones();
        Swal.fire({
          icon: 'info',
          title: 'Rechazada',
          text: 'La notificación fue rechazada',
          timer: 2000,
          showConfirmButton: false
        });
      },
      error: (error) => {
        console.error('❌ Error al rechazar:', error);
        Swal.fire('Error', 'No se pudo rechazar la notificación', 'error');
      }
    });
  }

  // FILTROS
  get notificacionesPendientesFiltradas() {
    if (!this.filtroUsuarios) return this.notificacionesPendientes;

    const filtro = this.filtroUsuarios.toLowerCase();
    return this.notificacionesPendientes.filter((n) =>
      n.detalle?.toLowerCase().includes(filtro) ||
      n.tipo?.toLowerCase().includes(filtro) ||
      this.getNombreUsuarioDesdeContrato(n).toLowerCase().includes(filtro)
    );
  }

  get notificacionesAceptadasFiltradas() {
    if (!this.filtroUsuarios) return this.notificacionesAceptadas;

    const filtro = this.filtroUsuarios.toLowerCase();
    return this.notificacionesAceptadas.filter((n) =>
      n.detalle?.toLowerCase().includes(filtro) ||
      n.tipo?.toLowerCase().includes(filtro) ||
      this.getNombreUsuarioDesdeContrato(n).toLowerCase().includes(filtro)
    );
  }

  // MODALES
  verNotificacion(n: Notificacion): void {
    this.notificacionSeleccionada = n;
    setTimeout(() => {
      const modalElement = document.getElementById('modalVerDetalle');
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement, {
          backdrop: 'static',
          keyboard: true
        });
        modal.show();
      }
    });
  }

  abrirHistorialAceptadas(): void {
    this.paginaAceptadas = 1;
    setTimeout(() => {
      const modalElement = document.getElementById('modalAceptadas');
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement, {
          backdrop: 'static',
          keyboard: true
        });
        modal.show();
      }
    });
  }

  // HELPERS
  getNombreUsuarioDesdeContrato(n: Notificacion): string {
    if (!n.contrato?.hoja_de_vida?.usuario) {
      return 'Usuario no disponible';
    }

    const usuario = n.contrato.hoja_de_vida.usuario;
    return `${usuario.primerNombre || ''} ${usuario.primerApellido || ''}`.trim() || 'Usuario sin nombre';
  }

  getNombreAreaDesdeContrato(n: Notificacion): string {
    return n?.contrato?.area?.nombreArea || 'Sin área';
  }

  getEstadoTexto(estado: string): string {
    switch (estado) {
      case "0": return 'Pendiente';
      case "1": return 'Aceptada';
      case "2": return 'Rechazada';
      default: return 'Desconocido';
    }
  }

  getEstadoClase(estado: string): string {
    switch (estado) {
      case "0": return 'badge bg-warning';
      case "1": return 'badge bg-success';
      case "2": return 'badge bg-danger';
      default: return 'badge bg-dark';
    }
  }

  puedeVerNotificaciones(): boolean {
    return [1, 2, 4].includes(this.usuario?.rol);
  }

  getMensajeSinNotificaciones(): string {
    if (!this.puedeVerNotificaciones()) return 'No tienes permisos para ver notificaciones';

    if (this.notificacionesPendientes.length === 0) {
      switch (this.usuario.rol) {
        case 1: case 4: return 'No hay notificaciones pendientes en el sistema';
        case 2: return 'No hay notificaciones pendientes en tu área';
        case 3: case 5: return 'No tienes notificaciones pendientes';
        default: return 'No hay notificaciones';
      }
    }
    return '';
  }
}
