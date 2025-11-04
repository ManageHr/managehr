import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MenuComponent } from '../menu/menu.component';
import { IncapacidadesJefeService } from '../../services/incapacidades-jefe.service';
import {
  SolicitudIncapacidadJefe,
  RespuestaSolicitudIncapacidad,
} from '../../models/solicitud-incapacidad-jefe';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-incapacidades-jefe',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent],
  templateUrl: './incapacidades-jefe.component.html',
  styleUrl: './incapacidades-jefe.component.scss',
})
export class IncapacidadesJefeComponent implements OnInit {
  usuario: any = {};
  solicitudes: SolicitudIncapacidadJefe[] = [];
  solicitudesFiltradas: SolicitudIncapacidadJefe[] = [];
  loading = false;
  error = '';
  filtroEstado = 'todos';
  terminoBusqueda = '';
  estadisticas: any = {};
  solicitudSeleccionada: SolicitudIncapacidadJefe | null = null;
  comentario = '';

  constructor(
    private router: Router,
    private incapacidadesJefeService: IncapacidadesJefeService
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
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      //console.log('Usuario logueado:', this.usuario);
    }
    this.cargarSolicitudes();
    this.cargarEstadisticas();
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
  cargarSolicitudes(): void {
    this.loading = true;
    this.error = '';

    this.incapacidadesJefeService.obtenerSolicitudesIncapacidades().subscribe({
      next: (data) => {
        this.solicitudes = data;
        this.aplicarFiltros();
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Error al cargar las solicitudes de incapacidades';
        this.loading = false;
        console.error('Error:', err);
      },
    });
  }

  cargarEstadisticas(): void {
    this.incapacidadesJefeService.obtenerEstadisticas().subscribe({
      next: (data) => {
        this.estadisticas = data;
      },
      error: (err) => {
        console.error('Error al cargar estadísticas:', err);
      },
    });
  }

  aplicarFiltros(): void {
    let filtradas = [...this.solicitudes];

    // Filtrar por estado
    if (this.filtroEstado !== 'todos') {
      filtradas = filtradas.filter((s) => s.estado === this.filtroEstado);
    }

    // Filtrar por término de búsqueda (nombre o documento)
    if (this.terminoBusqueda.trim()) {
      const termino = this.terminoBusqueda.toLowerCase();
      filtradas = filtradas.filter(
        (s) =>
          s.empleado?.nombre?.toLowerCase().includes(termino) ||
          s.empleado?.apellido?.toLowerCase().includes(termino) ||
          s.empleado?.numDocumento?.toString().includes(termino)
      );
    }

    this.solicitudesFiltradas = filtradas;
  }

  onFiltroEstadoChange(): void {
    this.aplicarFiltros();
  }

  onBusquedaChange(): void {
    this.aplicarFiltros();
  }

  aprobarSolicitud(solicitud: SolicitudIncapacidadJefe): void {
    if (!solicitud.idIncapacidad) return;

    this.incapacidadesJefeService
      .actualizarEstado(solicitud.idIncapacidad, 'Aprobado')
      .subscribe({
        next: () => {
          this.cargarSolicitudes();
          this.cargarEstadisticas();
          this.comentario = '';
          this.solicitudSeleccionada = null;
        },
        error: (err) => {
          this.error = 'Error al aprobar la solicitud';
          console.error('Error:', err);
        },
      });
  }

  rechazarSolicitud(solicitud: SolicitudIncapacidadJefe): void {
    if (!solicitud.idIncapacidad) return;

    this.incapacidadesJefeService
      .actualizarEstado(solicitud.idIncapacidad, 'Rechazado')
      .subscribe({
        next: () => {
          this.cargarSolicitudes();
          this.cargarEstadisticas();
          this.comentario = '';
          this.solicitudSeleccionada = null;
        },
        error: (err) => {
          this.error = 'Error al rechazar la solicitud';
          console.error('Error:', err);
        },
      });
  }

  seleccionarSolicitud(solicitud: SolicitudIncapacidadJefe): void {
    this.solicitudSeleccionada = solicitud;
  }

  cancelarAccion(): void {
    this.solicitudSeleccionada = null;
    this.comentario = '';
  }

  getEstadoClass(estado: string | undefined): string {
    switch (estado) {
      case 'pendiente':
        return 'badge-warning';
      case 'aprobado':
        return 'badge-success';
      case 'rechazado':
        return 'badge-danger';
      default:
        return 'badge-secondary';
    }
  }

  getEstadoText(estado: string | undefined): string {
    switch (estado) {
      case 'pendiente':
        return 'Pendiente';
      case 'aprobado':
        return 'Aprobado';
      case 'rechazado':
        return 'Rechazado';
      default:
        return 'Desconocido';
    }
  }

  formatearFecha(fecha: string): string {
    return new Date(fecha).toLocaleDateString('es-ES');
  }

  calcularDias(fechaInicio: string, fechaFin: string): number {
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    const diffTime = Math.abs(fin.getTime() - inicio.getTime());
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays + 1; // Incluir el día de inicio
  }
}
