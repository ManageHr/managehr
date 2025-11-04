import { Component, OnInit } from '@angular/core';
import { MenuComponent } from '../menu/menu.component';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import {
  HttpClient,
  HttpHeaders,
  HttpClientModule,
} from '@angular/common/http';
import Swal from 'sweetalert2';

import {
  SolicitudesVacacionesService,
  SolicitudVacaciones,
} from '../../services/solicitudes-vacaciones.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-formvacaciones',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule, HttpClientModule],
  templateUrl: './formvacaciones.component.html',
  styleUrls: ['./formvacaciones.component.scss'],
})
export class FormvacacionesComponent implements OnInit {
  motivo = '';
  fechaInicio = '';
  fechaFinal = '';
  dias = 0;
  contratoId: number | null = null;

  solicitudesVacaciones: SolicitudVacaciones[] = [];

  constructor(
    private router: Router,
    private http: HttpClient,
    private solicitudesVacacionesService: SolicitudesVacacionesService
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
    this.obtenerContratoId();
    this.cargarMisSolicitudes();
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
  obtenerContratoId(): void {
    const usuario = JSON.parse(localStorage.getItem('usuario') || '{}');
    console.log('Usuario cargado:', usuario);

    const numDocumento =
      usuario.perfil?.usuarioNumDocumento ??
      usuario.perfil?.numDocumento ??
      usuario.numDocumento;

    if (!numDocumento) {
      Swal.fire('Error', 'No se pudo identificar al usuario.', 'error');
      return;
    }

    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
    });

    this.http
      .get<{ contrato: { idContrato: number } }>(
        `https://www.evensoft21.com/managehr/api/public/api/contrato-usuario/${numDocumento}`,
        { headers }
      )
      .subscribe({
        next: (res) => {
          console.log('Respuesta del backend al buscar contrato:', res);
          this.contratoId = res.contrato?.idContrato ?? null;

          if (!this.contratoId) {
            Swal.fire(
              'Error',
              'No se encontró contrato válido para el usuario.',
              'error'
            );
          }
        },
        error: (err) => {
          console.error('Error al obtener contrato:', err);
          Swal.fire(
            'Error',
            'No se encontró contrato para el usuario.',
            'error'
          );
        },
      });
  }

  cargarMisSolicitudes(): void {
    this.solicitudesVacacionesService.obtenerSolicitudesUsuario().subscribe({
      next: (data) => {
        console.log('Solicitudes recibidas:', data);
        this.solicitudesVacaciones = data;
      },
      error: (err) => {
        console.error('Error al cargar solicitudes de usuario:', err);
      },
    });
  }

  calcularDias(): void {
    if (this.fechaInicio && this.fechaFinal) {
      const inicio = new Date(this.fechaInicio);
      const fin = new Date(this.fechaFinal);
      const diffMs = fin.getTime() - inicio.getTime();
      this.dias = Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1;
    } else {
      this.dias = 0;
    }
  }

  enviarSolicitud(): void {
    this.calcularDias();

    if (
      !this.motivo.trim() ||
      !this.fechaInicio ||
      !this.fechaFinal ||
      this.dias <= 0 ||
      this.contratoId === null
    ) {
      Swal.fire(
        'Error',
        'Todos los campos deben estar completos y válidos.',
        'error'
      );
      return;
    }

    const solicitud: SolicitudVacaciones = {
      motivo: this.motivo,
      fechaInicio: this.fechaInicio,
      fechaFinal: this.fechaFinal,
      dias: this.dias,
      contratoId: this.contratoId,
    };

    console.log('Datos enviados:', solicitud);

    this.solicitudesVacacionesService.enviarSolicitud(solicitud).subscribe({
      next: (response) => {
        this.solicitudesVacaciones.unshift(response);
        this.limpiarFormulario();
        Swal.fire(
          'Éxito',
          'Solicitud de vacaciones enviada correctamente.',
          'success'
        );
        this.cargarMisSolicitudes();
      },
      error: (error) => {
        console.error('Error al enviar solicitud:', error);
        Swal.fire(
          'Error',
          error.status === 422
            ? 'Verifica los campos del formulario.'
            : 'Ocurrió un error al enviar la solicitud.',
          'error'
        );
      },
    });
  }

  limpiarFormulario(): void {
    this.motivo = '';
    this.fechaInicio = '';
    this.fechaFinal = '';
    this.dias = 0;
  }

  get puedeEnviar(): boolean {
    return (
      this.motivo.trim().length > 0 &&
      this.fechaInicio !== '' &&
      this.fechaFinal !== '' &&
      this.dias > 0 &&
      this.contratoId !== null
    );
  }
}
