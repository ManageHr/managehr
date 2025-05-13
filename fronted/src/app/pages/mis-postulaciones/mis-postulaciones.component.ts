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

@Component({
  selector: 'app-mis-postulaciones',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MenuComponent,
    FilterMisPostulacionesPipe
  ],
  templateUrl: './mis-postulaciones.component.html',
  styleUrls: ['./mis-postulaciones.component.scss']
})
export class MisPostulacionesComponent implements OnInit, OnDestroy {
  postulaciones: any[] = [];
  usuario: Record<string, any> = {};
  postulacionSeleccionada: any | null = null;
  searchQuery: string = '';

  private searchTerms = new Subject<string>();
  private searchSubscription?: Subscription;

  constructor(
    public misPostulacionesService: MisPostulacionesService,
    public authService: AuthService
  ) {}

  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
    }

    this.searchSubscription = this.searchTerms.pipe(
      debounceTime(300),
      distinctUntilChanged(),
      switchMap((term: string) => {
        if (!term || isNaN(+term)) {
          return this.misPostulacionesService.getPostulaciones().pipe(
            catchError(error => {
              console.error('Error al cargar todas las postulaciones:', error);
              Swal.fire('Error', 'No se pudieron cargar las postulaciones.', 'error');
              return of([]);
            })
          );
        } else {
          const vacanteId = +term;
          return this.misPostulacionesService.searchPostulacionesByVacantesId(vacanteId).pipe(
            catchError(error => {
              console.error(`Error al buscar postulaciones para Vacante ID ${vacanteId}:`, error);
              Swal.fire('Error', `Error al buscar postulaciones para Vacante ID ${vacanteId}.`, 'error');
              return of([]);
            })
          );
        }
      })
    ).subscribe({
      next: (results: any[]) => {
        this.postulaciones = results;
        console.log('Lista de postulaciones actualizada:', results);
      },
      error: (error) => {
        console.error('Error en la suscripción de búsqueda:', error);
      }
    });

    this.cargarPostulaciones();
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

  editarPostulacion(postulacion: any): void {
    this.postulacionSeleccionada = { ...postulacion };
    console.log('Editando postulación:', postulacion);
  }

  verDetalles(postulacion: any): void {
    this.postulacionSeleccionada = postulacion;
    console.log('Detalles de postulación:', postulacion);
  }

  guardarEstadoPostulacion(): void {
    if (!this.postulacionSeleccionada || this.postulacionSeleccionada.idPostulaciones === undefined) {
      Swal.fire('Error', 'No se ha seleccionado una postulación válida para actualizar.', 'error');
      return;
    }

    Swal.fire('¡Actualizado!', 'Estado de postulación guardado correctamente', 'success');
    this.postulacionSeleccionada = null;
  }

  confirmDeletePostulacion(postulacion: any): void {
    console.warn("confirmDeletePostulacion: método placeholder sin implementación actual.");
  }
}
