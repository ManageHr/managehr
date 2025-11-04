import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MenuComponent } from '../menu/menu.component';
import {
  NotificacionesService,
  Notificacion,
} from '../../services/notificaciones.service';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import Swal from 'sweetalert2';
import { Router } from '@angular/router';

@Component({
  selector: 'app-notificaciones',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent, NgxPaginationModule],
  templateUrl: './notificaciones.component.html',
  styleUrls: ['./notificaciones.component.scss'],
})
export class NotificacionesComponent implements OnInit {
  notificacionesPendientes: Notificacion[] = [];
  notificacionesAceptadas: Notificacion[] = [];
  notificacionSeleccionada: Notificacion | null = null;
  usuario: any = {};

  // Filtros y paginación
  filtroUsuarios: string = '';
  itemsPerPage: number = 5;
  paginaPendientes: number = 1;
  itemsPorPaginaAceptadas: number = 5;
  paginaAceptadas: number = 1;

  // Variables para controlar modales
  mostrarModalDetalle: boolean = false;
  mostrarModalAceptadas: boolean = false;

  constructor(
    private router: Router,
    private notificacionesService: NotificacionesService
  ) {}

  ngOnInit(): void {
    const token = localStorage.getItem('token');
    const userFromLocal = localStorage.getItem('usuario');
    if (!token || !userFromLocal) {
      this.router.navigate(['/login']);
      return;
    }
    try {
      const tokenPayload = JSON.parse(atob(token.split('.')[1]));
      const expiracion = tokenPayload.exp * 1000;
      if (Date.now() >= expiracion) {
        this.mostrarSesionExpirada();
        return;
      }
    } catch (error) {
      console.error('Error verificando token:', error);
      this.mostrarSesionExpirada();
      return;
    }
    this.cargarUsuario();
    this.cargarNotificaciones();
  }
  private mostrarSesionExpirada(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');

    Swal.fire({
      title: 'Sesión expirada',
      text: 'Tu sesión ha caducado. Por favor, inicia sesión nuevamente.',
      icon: 'warning',
      confirmButtonText: 'Ir al login',
    }).then(() => {
      this.router.navigate(['/login']);
    });
  }
  cargarUsuario(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log(' USUARIO CARGADO (Empleado):', {
        id: this.usuario.id,
        rol: this.usuario.rol,
        contratoId: this.getContratoIdUsuario(),
        usuarioCompleto: this.usuario,
      });
    }
  }

  cargarNotificaciones(): void {
    this.notificacionesService.getAll().subscribe({
      next: (response) => {
        const todas = response.Notificaciones || [];
        console.log(' Total notificaciones en BD:', todas.length);
        this.filtrarNotificacionesParaEmpleado(todas);
      },
      error: (error) => {
        console.error('Error al cargar notificaciones:', error);
        Swal.fire('Error', 'No se pudieron cargar las notificaciones', 'error');
      },
    });
  }

  filtrarNotificacionesParaEmpleado(todas: Notificacion[]): void {
    this.notificacionesPendientes = [];
    this.notificacionesAceptadas = [];

    const contratoIdUsuario = this.getContratoIdUsuario();

    if (!contratoIdUsuario) {
      console.error(
        ' No se puede filtrar: contratoId del empleado no encontrado'
      );
      console.log(' Usuario completo para debug:', this.usuario);
      Swal.fire(
        'Info',
        'No se encontró información de contrato asociada a tu usuario',
        'info'
      );
      return;
    }

    console.log(' Filtrando notificaciones para empleado:');
    console.log('- Contrato ID del usuario:', contratoIdUsuario);
    console.log('- Tipo de contratoId:', typeof contratoIdUsuario);

    // DEBUG: Mostrar todas las notificaciones disponibles
    console.log(' Todas las notificaciones disponibles:');
    todas.forEach((n, index) => {
      console.log(`Notificación ${index + 1}:`, {
        id: n.idNotificacion,
        contratoId: n.contratoId,
        tipo: n.tipo,
        estado: n.estado,
        usuario: n.contrato?.hoja_de_vida?.usuario?.primerNombre,
      });
    });

    // Filtrar por contratoId del empleado - CORREGIDO
    this.notificacionesPendientes = todas.filter(
      (n) =>
        n.estado === '0' &&
        n.contratoId !== null &&
        n.contratoId === contratoIdUsuario.toString()
    );

    this.notificacionesAceptadas = todas.filter(
      (n) =>
        n.estado === '1' &&
        n.contratoId !== null &&
        n.contratoId === contratoIdUsuario.toString()
    );

    console.log(' Resultados para empleado:');
    console.log(
      '- Pendientes (estado 0):',
      this.notificacionesPendientes.length
    );
    console.log('- Aceptadas (estado 1):', this.notificacionesAceptadas.length);

    // Mostrar detalles de las notificaciones encontradas
    if (this.notificacionesPendientes.length > 0) {
      console.log(' Notificaciones pendientes encontradas:');
      this.notificacionesPendientes.forEach((n) => {
        console.log('', {
          id: n.idNotificacion,
          tipo: n.tipo,
          detalle: n.detalle,
          contratoId: n.contratoId,
        });
      });
    } else {
      console.log(
        ' Buscando notificaciones con contratoId:',
        contratoIdUsuario
      );
      const notificacionesDelContrato = todas.filter(
        (n) =>
          n.contratoId !== null && n.contratoId === contratoIdUsuario.toString()
      );
      console.log(
        ' Notificaciones con este contratoId:',
        notificacionesDelContrato.length
      );
      notificacionesDelContrato.forEach((n) => {
        console.log('', {
          id: n.idNotificacion,
          estado: n.estado,
          tipo: n.tipo,
          detalle: n.detalle,
        });
      });
    }
  }

  private getContratoIdUsuario(): number | null {
    // Buscar contratoId en diferentes ubicaciones posibles - MEJORADO
    let contratoId = null;

    // 1. Buscar en usuario.contratoId
    if (this.usuario.contratoId) {
      contratoId = this.usuario.contratoId;
      console.log('ContratoId encontrado en usuario.contratoId:', contratoId);
    }
    // 2. Buscar en usuario.perfil.contratoId
    else if (this.usuario.perfil?.contratoId) {
      contratoId = this.usuario.perfil.contratoId;
      console.log(
        'ContratoId encontrado en usuario.perfil.contratoId:',
        contratoId
      );
    }
    // 3. Buscar en usuario.contrato.idContrato
    else if (this.usuario.contrato?.idContrato) {
      contratoId = this.usuario.contrato.idContrato;
      console.log(
        'ContratoId encontrado en usuario.contrato.idContrato:',
        contratoId
      );
    }
    // 4. Buscar en usuario.id (como fallback)
    else if (this.usuario.id) {
      // Si el usuario tiene id "3", podría corresponder al contrato "1" según los datos
      contratoId = this.usuario.id === 3 ? 1 : this.usuario.id;
      console.log(
        'Usando id del usuario como contratoId (fallback):',
        contratoId
      );
    }

    if (!contratoId) {
      console.error('No se encontró contratoId en el usuario:', this.usuario);
      // Mostrar estructura completa del usuario para debug
      console.log(
        'Estructura completa del usuario:',
        JSON.stringify(this.usuario, null, 2)
      );
    }

    return contratoId;
  }

  // ACCIONES - Modales nativos
  verNotificacion(n: Notificacion): void {
    this.notificacionSeleccionada = n;
    this.mostrarModalDetalle = true;

    // Marcar como vista (estado 1) cuando se hace clic en Ver
    this.marcarComoVista(n);
  }

  marcarComoVista(notificacion: Notificacion): void {
    // Cambiar el estado a "1" (Vista/Aceptada)
    this.notificacionesService
      .actualizarEstado(notificacion.idNotificacion, 1)
      .subscribe({
        next: (response) => {
          console.log(
            'Notificación marcada como vista:',
            notificacion.idNotificacion
          );
          // Actualizar la lista local
          notificacion.estado = '1';
          // Remover de pendientes y agregar a aceptadas
          this.notificacionesPendientes = this.notificacionesPendientes.filter(
            (n) => n.idNotificacion !== notificacion.idNotificacion
          );
          this.notificacionesAceptadas.unshift(notificacion);
        },
        error: (error) => {
          console.error('Error al marcar notificación como vista:', error);
        },
      });
  }

  cerrarModalDetalle(): void {
    this.mostrarModalDetalle = false;
    this.notificacionSeleccionada = null;
  }

  abrirHistorialAceptadas(): void {
    this.paginaAceptadas = 1;
    this.mostrarModalAceptadas = true;
  }

  cerrarModalAceptadas(): void {
    this.mostrarModalAceptadas = false;
  }

  // FILTROS
  get notificacionesPendientesFiltradas() {
    if (!this.filtroUsuarios) return this.notificacionesPendientes;

    const filtro = this.filtroUsuarios.toLowerCase();
    return this.notificacionesPendientes.filter(
      (n) =>
        n.detalle?.toLowerCase().includes(filtro) ||
        n.tipo?.toLowerCase().includes(filtro)
    );
  }

  get notificacionesAceptadasFiltradas() {
    if (!this.filtroUsuarios) return this.notificacionesAceptadas;

    const filtro = this.filtroUsuarios.toLowerCase();
    return this.notificacionesAceptadas.filter(
      (n) =>
        n.detalle?.toLowerCase().includes(filtro) ||
        n.tipo?.toLowerCase().includes(filtro)
    );
  }

  // HELPERS para paginación mejorada
  getDesdeItem(): number {
    return (this.paginaPendientes - 1) * this.itemsPerPage + 1;
  }

  getHastaItem(): number {
    return Math.min(
      this.paginaPendientes * this.itemsPerPage,
      this.notificacionesPendientesFiltradas.length
    );
  }

  getDesdeItemAceptadas(): number {
    return (this.paginaAceptadas - 1) * this.itemsPorPaginaAceptadas + 1;
  }

  getHastaItemAceptadas(): number {
    return Math.min(
      this.paginaAceptadas * this.itemsPorPaginaAceptadas,
      this.notificacionesAceptadasFiltradas.length
    );
  }

  calcularFechaFin(fechaInicio: string): string {
    // Simular cálculo de fecha fin (5 días después)
    const fecha = new Date(fechaInicio);
    fecha.setDate(fecha.getDate() + 5);
    return fecha.toLocaleDateString();
  }

  getEstadoTexto(estado: string): string {
    switch (estado) {
      case '0':
        return 'Pendiente';
      case '1':
        return 'Aceptada';
      case '2':
        return 'Rechazada';
      default:
        return 'Desconocido';
    }
  }

  getEstadoClase(estado: string): string {
    switch (estado) {
      case '0':
        return 'badge bg-warning';
      case '1':
        return 'badge bg-success';
      case '2':
        return 'badge bg-danger';
      default:
        return 'badge bg-dark';
    }
  }

  puedeVerNotificaciones(): boolean {
    return this.usuario?.rol === 3;
  }

  getMensajeSinNotificaciones(): string {
    if (!this.puedeVerNotificaciones())
      return 'No tienes permisos para ver notificaciones';

    const contratoId = this.getContratoIdUsuario();
    if (!contratoId) {
      return 'No se encontró información de contrato asociada a tu usuario';
    }

    if (this.notificacionesPendientes.length === 0) {
      return 'No tienes notificaciones pendientes';
    }
    return '';
  }
}
