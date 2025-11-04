import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MisPostulacionesService } from '../../services/mispostulaciones.service';
import { MenuComponent } from '../menu/menu.component';
import { AuthService } from '../../services/auth.service';
import { FilterMisPostulacionesPipe } from './filter-mispostulaciones';
import Swal from 'sweetalert2';
import { Subject, Subscription, of } from 'rxjs';
import { debounceTime, distinctUntilChanged, switchMap, catchError } from 'rxjs/operators';
import { Router } from '@angular/router';

interface Usuario {
  id: number;
  name: string;
  email: string;
  rol: number;
  perfil: {
    numDocumento: number;
    [key: string]: any;
  };
}

@Component({
  selector: 'app-mis-postulaciones',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MenuComponent,
    //FilterMisPostulacionesPipe
  ],
  templateUrl: './mis-postulaciones.component.html',
  styleUrls: ['./mis-postulaciones.component.scss']
})
export class MisPostulacionesComponent implements OnInit, OnDestroy {
  postulaciones: any[] = [];
  usuario: Usuario = {} as Usuario;
  usuarioCargado = false;
  postulacionSeleccionada: any | null = null;
  searchQuery: string = '';

  private searchTerms = new Subject<string>();
  private searchSubscription?: Subscription;

  constructor(
    private router: Router,
    public misPostulacionesService: MisPostulacionesService,
    public authService: AuthService
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
      this.usuarioCargado = true;
    }

    this.searchSubscription = this.searchTerms.pipe(
      debounceTime(300),
      distinctUntilChanged(),
      switchMap((term: string) => {
        if (!term || isNaN(+term)) {
          return this.misPostulacionesService.getPostulaciones().pipe(
            catchError(error => {
              Swal.fire('Error', 'No se pudieron cargar las postulaciones.', 'error');
              return of([]);
            })
          );
        } else {
          const vacanteId = +term;
          return this.misPostulacionesService.searchPostulacionesByVacantesId(vacanteId).pipe(
            catchError(error => {
              Swal.fire('Error', `Error al buscar postulaciones para Vacante ID ${vacanteId}.`, 'error');
              return of([]);
            })
          );
        }
      })
    ).subscribe({
      next: (resultados: any[]) => {
        this.postulaciones = this.filtrarPostulacionesPorUsuario(resultados);
      }
    });

    this.cargarPostulaciones();
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
  ngOnDestroy(): void {
    this.searchSubscription?.unsubscribe();
  }

  onSearchInput(): void {
    this.searchTerms.next(this.searchQuery);
  }

  private cargarPostulaciones(): void {
    this.searchTerms.next('');
  }

  private filtrarPostulacionesPorUsuario(postulaciones: any[]): any[] {
    if (this.usuario.rol === 1) {
      return postulaciones;
    }
    return postulaciones.filter(p => p.numDocumento === this.usuario.perfil.numDocumento);
  }

  editarPostulacion(postulacion: any): void {
    this.postulacionSeleccionada = { ...postulacion };
  }

  verDetalles(postulacion: any): void {
    this.postulacionSeleccionada = postulacion;
  }

  guardarEstadoPostulacion(): void {
    if (!this.postulacionSeleccionada || this.postulacionSeleccionada.idPostulaciones === undefined) {
      Swal.fire('Error', 'No se ha seleccionado una postulación válida para actualizar.', 'error');
      return;
    }

    this.misPostulacionesService.actualizarEstadoPostulacion(
      this.postulacionSeleccionada.idPostulaciones,
      this.postulacionSeleccionada.estado
    ).subscribe({
      next: () => {
        Swal.fire('¡Actualizado!', 'Estado actualizado correctamente.', 'success');
        this.postulacionSeleccionada = null;
        this.cargarPostulaciones();
      },
      error: () => {
        Swal.fire('Error', 'No se pudo actualizar el estado.', 'error');
      }
    });
  }

  confirmDeletePostulacion(postulacion: any): void {
    console.warn("Método no implementado aún.");
  }

  trackById(index: number, item: any): number {
  return item.idPostulaciones;
}

getNombreEstado(estado: number): string {
  switch (estado) {
    case 1: return 'Pendiente';
    case 2: return 'Aceptado';
    case 3: return 'Rechazado';
    default: return 'Desconocido';
  }
}

}
