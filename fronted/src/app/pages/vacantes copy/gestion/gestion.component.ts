// src/app/pages/vacantes copy/gestion/gestion.component.ts
// --> Asegúrate de que este archivo está en esta ruta o similar <--

import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common'; // Necesario para *ngIf, *ngFor
import { FormsModule } from '@angular/forms'; // Necesario para [(ngModel)]
import Swal from 'sweetalert2'; // Para mensajes emergentes

// === DEPENDENCIAS EXTERNAS ===
// Necesitas estos archivos y deben estar bien ubicados y exportados

// Importa la Interfaz Vacante y la Clase VacanteService
// *** VERIFICA y AJUSTA ESTA RUTA para que apunte a tu archivo gestion.service.ts ***
// *** IMPORTANTE: Asegúrate de importar también 'CategoriaVacante' desde aquí ***
import { Vacante, VacanteService, CategoriaVacante } from '../../../services/gestion.service';


// Importa la Clase FilterVacantePipe
// *** VERIFICA y AJUSTA ESTA RUTA para que apunte a tu archivo filter-gestion.ts ***
import { FilterVacantePipe } from './filter-gestion'; // Si está en la misma carpeta

// Otros servicios o componentes que uses
import { AuthService } from '../../../services/auth.service';
import { MenuComponent } from '../../menu/menu.component';

// Módulo HttpClient (necesario para el servicio)
import { HttpClientModule } from '@angular/common/http';

// Operadores de RxJS
import { catchError } from 'rxjs/operators';
import { throwError, Observable } from 'rxjs'; // Importa Observable si no está


// === DEFINICIÓN DEL COMPONENTE ===
@Component({
  selector: 'app-gestion',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MenuComponent,
    FilterVacantePipe,
    HttpClientModule, // Si es necesario aquí
  ],
  templateUrl: './gestion.component.html',
  styleUrls: ['./gestion.component.scss']
})
export class GestionComponent implements OnInit {

  vacantes: Vacante[] = []; // Lista para la tabla

  // --- vacanteSeleccionada: USA LOS NUEVOS NOMBRES DE PROPIEDADES DE LA IMAGEN ---
  vacanteSeleccionada: Vacante = {
      // --> Inicializa con las propiedades de la INTERFAZ VACANTE (deben coincidir con la imagen/API) <--
      idVacantes: undefined, // Usando el nombre de la imagen para el ID
      nomVacante: '',        // Usando el nombre de la imagen para el Nombre
      descripVacante: '',    // Añadido desde la imagen
      salario: 0,            // Añadido desde la imagen (asumo número, ajusta el tipo)
      expMinima: '',         // Añadido desde la imagen (asumo string, ajusta el tipo)
      cargoVacante: '',      // Añadido desde la imagen
      catVacId: undefined,   // Añadido desde la imagen (asumo ID, ajusta el tipo)
      // Si en el formulario usas otras propiedades de la imagen, inicialízalas aquí.
      // Si 'codigo', 'categoria', 'estado' de los formularios antiguos mapean a algo, ajústalos o quítalos si ya no aplican.
  };

  usuario: any = {};

  // El filtro sigue siendo por nombre, que ahora es nomVacante
  filtroNombreVacante: string = "";

  // *** NUEVA PROPIEDAD: Lista para guardar las categorías para el selector ***
  categorias: CategoriaVacante[] = []; // Asumiendo que CategoriaVacante está definida en el servicio

  // --- CONSTRUCTOR ---
  constructor(
    public authService: AuthService,
    private vacanteService: VacanteService // Aquí se inyecta el servicio
  ) { }

  // --- ngOnInit ---
  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      try {
          this.usuario = JSON.parse(userFromLocal);
      } catch (e) {
          console.error('Error al parsear usuario de localStorage:', e);
          this.usuario = {};
      }
    }
    // Cargar vacantes al iniciar
    this.cargarVacantes();

    // *** NUEVA LLAMADA: Cargar la lista de categorías al iniciar el componente ***
    this.cargarCategoriasForaneas(); // <-- Llama al método para cargar categorías
  }

  // --- MÉTODOS ---

  cargarVacantes(): void {
    this.vacanteService.getVacantes().pipe(
      catchError(error => {
        console.error('Error al cargar vacantes:', error);
        const apiErrorMessage = error.error?.message || error.message || 'Error desconocido.';
        Swal.fire('Error', `No se pudieron cargar las vacantes. ${apiErrorMessage}`, 'error');
        return throwError(() => new Error(error.message || 'Error desconocido al cargar vacantes'));
      })
    )
    .subscribe({
      next: (data) => {
        this.vacantes = data;
      },
      error: (error) => {
        console.error('La carga de vacantes falló completamente después del catch.', error);
      },
      complete: () => {}
    });
  }

  // *** NUEVO MÉTODO: Cargar la lista de categorías desde el servicio ***
  cargarCategoriasForaneas(): void {
    // Llama al método en el servicio para obtener la lista de categorías
    this.vacanteService.obtenerCategoriasVacante().pipe(
      catchError(error => {
        console.error('Error al cargar categorías de vacante:', error);
        // Puedes añadir un mensaje al usuario si falla
        Swal.fire('Error', 'No se pudieron cargar las categorías de vacante.', 'error');
        return throwError(() => new Error(error.message || 'Error desconocido al cargar categorías'));
      })
    )
    .subscribe({
      next: (data) => {
        this.categorias = data; // Asigna los datos recibidos a la propiedad categorias
        console.log('Categorías de vacante cargadas con éxito:', data); // Opcional para depurar
      },
      error: (error) => {
        console.error('La carga de categorías falló completamente después del catch.', error);
      },
      complete: () => {}
    });
  }


  abrirModalAgregarVacante(): void {
    // --- Reinicializa vacanteSeleccionada con los NUEVOS NOMBRES de propiedades ---
    this.vacanteSeleccionada = {
        idVacantes: undefined,
        nomVacante: '',
        descripVacante: '',
        salario: 0,   // O undefined, dependiendo si es number o number | undefined en tu interfaz
        expMinima: '',
        cargoVacante: '',
        catVacId: undefined, // O null, dependiendo si es number | null | undefined en tu interfaz
        // Reinicializa aquí el resto de propiedades si las usas en el formulario
    };
     // Opcional: Si quieres que las categorías se refrezquen cada vez que abres el modal, llama aquí:
     // this.cargarCategoriasForaneas();
  }

  // Recibe vacante (que ahora tiene las nuevas propiedades)
  editarVacante(vacante: Vacante): void {
     // Copia vacante (con las nuevas propiedades)
     this.vacanteSeleccionada = { ...vacante };
     // Opcional: Si quieres que las categorías se refrezquen cada vez que abres el modal de edición, llama aquí:
     // this.cargarCategoriasForaneas();
  }

  // Recibe vacante (que ahora tiene las nuevas propiedades)
  confirmDeleteVacante(vacante: Vacante): void {
    // --- Verifica el ID usando el NUEVO nombre: idVacantes ---
    if (vacante.idVacantes === undefined || vacante.idVacantes === null) {
      console.error('Error: No tiene ID válido para eliminar.');
      Swal.fire('Error', 'Datos de vacante inválidos.', 'error');
      return;
    }
    Swal.fire({
      // --- Usa el NUEVO nombre para el nombre en el mensaje: nomVacante ---
      title: `¿Eliminar "${vacante.nomVacante || 'seleccionada'}"?`,
      text: 'Esta acción no se puede deshacer.', icon: 'warning',
      showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
      customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-secondary' }, buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        // --- Llama al servicio con el NUEVO ID: idVacantes ---
        this.vacanteService.deleteVacante(vacante.idVacantes!).pipe( // Asegúrate de que deleteVacante en el servicio acepte number|string según el tipo de idVacantes
          catchError(error => {
            console.error('Error al eliminar vacante:', error);
            const apiErrorMessage = error.error?.message || error.message || 'Error desconocido.';
            Swal.fire('Error', `No se pudo eliminar. ${apiErrorMessage}`, 'error');
            return throwError(() => new Error(error.message || 'Error desconocido al eliminar vacante'));
          })
        ).subscribe({
          next: () => {
            // --- Usa el NUEVO nombre para el nombre en el mensaje: nomVacante ---
            Swal.fire('¡Eliminada!', `La vacante "${vacante.nomVacante || ''}" fue eliminada.`, 'success');
            this.cargarVacantes();
          },
          error: (error) => { console.error('La eliminación falló después del catch.', error); },
          complete: () => {}
        });
      }
    });
  }

  // Guarda (crear o actualizar) vacante
  guardarVacante(): void {
    // --- Validar: Usa los NUEVOS nombres de propiedades que sean obligatorios ---
    // Aquí asumimos nomVacante y catVacId son obligatorios para guardar una vacante válida
    if (!this.vacanteSeleccionada.nomVacante || this.vacanteSeleccionada.catVacId === undefined || this.vacanteSeleccionada.catVacId === null) {
      Swal.fire('Error', 'El nombre de la vacante y la categoría son obligatorios.', 'error'); // Mensaje de validación
      return;
    }

    let request$; // Observable de la petición al servicio
    let successMessage = '';
    let errorMessage = '';

    // --- Decidir si es actualizar (tiene idVacantes) o crear (no tiene idVacantes) ---
    if (this.vacanteSeleccionada.idVacantes !== undefined && this.vacanteSeleccionada.idVacantes !== null) {
      // Si tiene idVacantes, es una actualización
      request$ = this.vacanteService.updateVacante(this.vacanteSeleccionada); // Llama actualizar con el objeto completo
      successMessage = '¡Actualizada!'; errorMessage = 'No se pudo actualizar.';
    } else {
      // Si NO tiene idVacantes, es una creación
      // Crea una copia sin idVacantes si tu API no lo espera al crear
      const { idVacantes, ...vacanteParaCrear } = this.vacanteSeleccionada;
      request$ = this.vacanteService.createVacante(vacanteParaCrear as Vacante); // Llama crear
      successMessage = '¡Creada!'; errorMessage = 'No se pudo crear.';
    }

    // Ejecutar la petición y manejar respuesta
    request$.pipe(
        catchError(error => {
            console.error('Error al guardar vacante:', error);
            const apiErrorMessage = error.error?.message || error.message || 'Error desconocido.';
            Swal.fire('Error', `${errorMessage} ${apiErrorMessage}`, 'error');
            return throwError(() => new Error(error.message || `Error desconocido al guardar vacante`));
        })
    ).subscribe({
        next: (responseVacante) => {
            Swal.fire(successMessage, `La vacante ha sido guardada.`, 'success');
            // Limpiar formulario solo si fue crear (verifica si idVacantes era undefined/null antes de guardar)
            if (this.vacanteSeleccionada.idVacantes === undefined || this.vacanteSeleccionada.idVacantes === null) {
                 // --- Limpiar con los NUEVOS NOMBRES de propiedades ---
                 this.vacanteSeleccionada = {
                     idVacantes: undefined, nomVacante: '', descripVacante: '', salario: 0, expMinima: '', cargoVacante: '', catVacId: undefined // Reinicializa catVacId también
                     // Limpia aquí el resto de propiedades si las usas en el formulario
                    };
             // Opcional: si quieres que el selector se limpie a la opción por defecto (null),
             // asegúrate de que catVacId se inicializa a null o undefined.
             }
            this.cargarVacantes(); // Recargar lista
        },
        error: (error) => { console.error('La petición de guardar falló después del catch.', error); },
        complete: () => {}
    });
  }
}