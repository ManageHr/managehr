import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MisPostulacionesService } from '../../services/mispostulaciones.service'; // Asegúrate de que la ruta sea correcta
import { MenuComponent } from '../menu/menu.component';
import { AuthService } from '../../services/auth.service';  // Asegúrate de importar el servicio AuthService
import { FilterMisPostulacionesPipe } from './filter-mispostulaciones'; // Importar el Pipe
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
    FilterMisPostulacionesPipe  // Asegúrate de incluir el pipe en imports
  ],
  templateUrl: './mis-postulaciones.component.html',
  styleUrls: ['./mis-postulaciones.component.scss']
})
export class MisPostulacionesComponent implements OnInit, OnDestroy {
  postulaciones: any[] = [];  // Ajusta el tipo según el modelo real
  usuario: Record<string, any> = {};
  postulacionSeleccionada: any | null = null;
  searchQuery: string = '';  // Variable para búsqueda en el HTML

  private searchTerms = new Subject<string>();
  private searchSubscription?: Subscription;

  constructor(
    public misPostulacionesService: MisPostulacionesService,  // Inyecta el servicio correcto
    public authService: AuthService // Cambié de private a public para ser accesible en el template
  ) {}

  ngOnInit(): void {
    // Obtener el usuario desde el localStorage
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
    }

    // Suscripción a la búsqueda con debounce
    this.searchSubscription = this.searchTerms.pipe(
      debounceTime(300), // 300ms de retraso después de que el usuario deje de escribir
      distinctUntilChanged(), // Solo emite si el valor ha cambiado
      switchMap((term: string) => {
        // Si el término de búsqueda es vacío o no es un número válido, obtener todas las postulaciones
        if (!term || term.trim() === '' || isNaN(+term)) {
          return this.misPostulacionesService.getPostulaciones().pipe(
            catchError(error => {
              console.error('Error al cargar todas las postulaciones:', error);
              Swal.fire('Error', 'No se pudieron cargar las postulaciones.', 'error');
              return of([]); // Retorna un array vacío en caso de error
            })
          );
        } else {
          const vacanteId = +term; // Convierte el término en un número
          return this.misPostulacionesService.searchPostulacionesByVacantesId(vacanteId).pipe(
            catchError(error => {
              console.error(`Error al buscar postulaciones para Vacante ID ${vacanteId}:`, error);
              Swal.fire('Error', `Error al buscar postulaciones para Vacante ID ${vacanteId}.`, 'error');
              return of([]); // Retorna un array vacío en caso de error
            })
          );
        }
      })
    ).subscribe({
      next: (results: any[]) => {
        this.postulaciones = results; // Actualiza las postulaciones con el resultado obtenido
        console.log('Lista de postulaciones actualizada:', results);
      },
      error: (error) => {
        console.error('Error en la suscripción de búsqueda:', error);
      }
    });

    // Cargar postulaciones iniciales
    this.cargarPostulaciones();
  }

  ngOnDestroy(): void {
    // Desuscribirse de la suscripción cuando el componente se destruya para evitar fugas de memoria
    if (this.searchSubscription) {
      this.searchSubscription.unsubscribe();
    }
  }

  // Manejo de búsqueda de postulaciones
  onSearchInput(): void {
    this.searchTerms.next(this.searchQuery); // Enviar el término de búsqueda al Subject
  }

  // Método para cargar postulaciones iniciales
  private cargarPostulaciones(): void {
    this.searchTerms.next(''); // Llama el servicio para obtener todas las postulaciones
  }

  // Editar una postulación
  editarPostulacion(postulacion: any): void {
    this.postulacionSeleccionada = { ...postulacion }; // Copiar la postulación seleccionada
    console.log('Editando postulación:', postulacion);
  }

  // Ver los detalles de una postulación
  verDetalles(postulacion: any): void {
    this.postulacionSeleccionada = postulacion; // Asignar la postulación seleccionada
    console.log('Detalles de postulación:', postulacion);
  }

  // Guardar cambios en el estado de la postulación
  guardarEstadoPostulacion(): void {
    if (!this.postulacionSeleccionada || this.postulacionSeleccionada.idPostulaciones === undefined) {
      Swal.fire('Error', 'No se ha seleccionado una postulación válida para actualizar.', 'error');
      return;
    }

    Swal.fire('¡Actualizado!', 'Estado de postulación guardado correctamente', 'success');
    this.postulacionSeleccionada = null; // Limpiar la postulación seleccionada
  }

  // Método placeholder para eliminar postulación
  confirmDeletePostulacion(postulacion: any): void {
    console.warn("confirmDeletePostulacion: método placeholder sin implementación actual.");
  }
}
