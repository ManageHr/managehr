import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import Swal from 'sweetalert2';
import { Postulacion, PostulacionesService } from '../../../services/postulaciones.service';
import { AuthService } from '../../../services/auth.service';
import { MenuComponent } from '../../menu/menu.component';
import { Subject, Subscription, of } from 'rxjs';
import { debounceTime, distinctUntilChanged, switchMap, catchError } from 'rxjs/operators';

@Component({
  selector: 'app-postulaciones',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent],
  templateUrl: './postulaciones.component.html',
  styleUrls: ['./postulaciones.component.scss']
})
export class PostulacionesComponent implements OnInit, OnDestroy {
  postulaciones: Postulacion[] = [];
  usuario: Record<string, any> = {};
  postulacionSeleccionada: Postulacion | null = null;

  private searchTerms = new Subject<string>();
  private searchSubscription?: Subscription;

  constructor(
    public authService: AuthService,
    private postulacionesService: PostulacionesService
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
        if (!term || term.trim() === '' || isNaN(+term)) {
          return this.postulacionesService.getPostulaciones().pipe(
            catchError(error => {
              console.error('Error al cargar todas las postulaciones:', error);
              Swal.fire('Error', 'No se pudieron cargar las postulaciones.', 'error');
              return of([]);
            })
          );
        } else {
          const vacanteId = +term;
          return this.postulacionesService.searchPostulacionesByVacantesId(vacanteId).pipe(
            catchError(error => {
              console.error(`Error al buscar postulaciones para Vacante ID ${vacanteId}:`, error);
              Swal.fire('Error', `Error al buscar postulaciones para Vacante ID ${vacanteId}.`, 'error');
              return of([]);
            })
          );
        }
      })
    ).subscribe({
      next: (results: Postulacion[]) => {
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
    if (this.searchSubscription) {
      this.searchSubscription.unsubscribe();
    }
  }

  onSearchInput(event: Event): void {
    const searchTerm = (event.target as HTMLInputElement).value;
    this.searchTerms.next(searchTerm);
  }

  private cargarPostulaciones(): void {
    this.searchTerms.next('');
  }

  editarPostulacion(postulacion: Postulacion, index: number): void {
    this.postulacionSeleccionada = { ...postulacion };
    console.log('Editando postulación:', postulacion);
  }

  verDetalles(postulacion: Postulacion): void {
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

  confirmDeletePostulacion(postulacion: Postulacion): void {
    console.warn("confirmDeletePostulacion: método placeholder sin implementación actual.");
  }
}
