import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import Swal from 'sweetalert2'; // Importamos Swal

// --- Importación del servicio y la interfaz ---
// La interfaz Categoria DEBE venir de aquí, NO declarada localmente en este archivo.
// **VERIFICA ESTA RUTA POR ÚLTIMA VEZ**
import { Categoria, CategoriaService } from '../../../services/categoria.service';

// --- Importación del Pipe de Filtrado ---
// Verifica también esta ruta
import { FilterCategoriaPipe } from './filter-categoria';

import { AuthService } from '../../../services/auth.service';
import { MenuComponent } from '../../menu/menu.component';

// --- IMPORTA HttpClientModule ---

import { catchError } from 'rxjs/operators'; // Importa catchError para manejo de errores en subscribe
import { throwError } from 'rxjs'; // Importa throwError


@Component({
  selector: 'app-categoria-vacantes',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MenuComponent,
    FilterCategoriaPipe,
    // <-- ¡ASEGÚRATE DE INCLUIR HttpClientModule AQUÍ!
  ],
  templateUrl: './categoria-vacantes.component.html',
  styleUrls: ['./categoria-vacantes.component.scss']
})
export class CategoriaVacantesComponent implements OnInit {

  // --- Propiedad para la lista de categorías ---
  // Usa la interfaz importada del servicio
  categorias: Categoria[] = [];

  // --- Propiedad para la categoría seleccionada ---
  // Inicializa con nomCategoria, según la interfaz importada
  categoriaSeleccionada: Categoria = { nomCategoria: '' };

  usuario: any = {};

  // --- Propiedad para el filtro ---
  filtroNombre: string = "";

  // --- INYECCIÓN DEL SERVICIO ---
  constructor(
    public authService: AuthService,
    // Asegúrate de que CategoriaService está correctamente importado y exportado
    private categoriaService: CategoriaService // Inyección del servicio
  ) { }

  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('Usuario logueado:', this.usuario);
    }

    // === LLAMADA REAL AL SERVICIO AL INICIAR EL COMPONENTE ===
    this.cargarCategorias();
  }

  // --- Método para cargar categorías (AHORA LLAMA AL SERVICIO) ---
  cargarCategorias(): void {
    console.log('Llamando al servicio para cargar categorías...');
    this.categoriaService.getCategorias().pipe(
        catchError(error => {
            console.error('Error en la suscripción al cargar categorías:', error);
             Swal.fire('Error', 'No se pudieron cargar las categorías.', 'error');
            return throwError(() => new Error(error.message || 'Error desconocido al cargar categorías'));
        })
    )
    .subscribe({
      next: (data) => {
        this.categorias = data;
        console.log('Categorías cargadas:', data);
      },
      error: (error) => {
        // El catchError de arriba ya maneja el Swal, aquí podrías hacer algo adicional si necesitas
        console.error('Suscripción de cargarCategorias completada con error.', error);
      }
    });
  }

  // --- Método para abrir modal de agregar ---
  abrirModalAgregarCategoria(): void {
    this.categoriaSeleccionada = { nomCategoria: '' };
    console.log('Abriendo modal para agregar categoría...');
    // Aquí normalmente abrirías tu modal/formulario
  }

  // --- Método para editar categoría ---
  // Recibe el objeto Categoria completo
  editarCategoria(categoria: Categoria): void {
    this.categoriaSeleccionada = { ...categoria };
    console.log('Editando categoría:', categoria);
    // Aquí normalmente abrirías tu modal/formulario con los datos cargados
    // Opcional: Obtener datos frescos de la API para editar si es necesario
    // if (categoria.idCatVac !== undefined) {
    //   this.categoriaService.getCategoria(categoria.idCatVac).subscribe(data => {
    //     this.categoriaSeleccionada = data;
    //   });
    // }
  }

  // --- Método para confirmar eliminación (AHORA LLAMA AL SERVICIO) ---
  // Recibe el objeto Categoria completo
  confirmDeleteCategoria(categoria: Categoria): void {
     if (categoria.idCatVac === undefined) {
         console.error('No se puede eliminar una categoría sin ID.');
         Swal.fire('Error', 'Datos de categoría inválidos para eliminar.', 'error');
         return;
     }

    Swal.fire({
      title: `¿Eliminar la categoría "${categoria.nomCategoria}"?`,
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      customClass: {
        confirmButton: 'btn btn-danger',
        cancelButton: 'btn btn-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        console.log(`Llamando al servicio para eliminar categoría con idCatVac: ${categoria.idCatVac}...`);
        // ** Implementación de llamada al servicio - CORREGIDA CON EL OPERADOR ! **
        // Usamos categoria.idCatVac! para asegurar a TypeScript que no es undefined aquí.
        this.categoriaService.deleteCategoria(categoria.idCatVac!).pipe(
             catchError(error => {
                  console.error('Error en la suscripción al eliminar categoría:', error);
                  Swal.fire('Error', 'No se pudo eliminar la categoría.', 'error');
                  return throwError(() => new Error(error.message || 'Error desconocido al eliminar categoría'));
             })
        )
        .subscribe({
          next: () => {
            console.log('Categoría eliminada con éxito');
            Swal.fire('¡Eliminada!', `La categoría "${categoria.nomCategoria}" fue eliminada correctamente.`, 'success');
            this.cargarCategorias(); // === RECARGAR LA LISTA DESPUÉS DEL ÉXITO ===
          },
          error: (error) => {
            // El catchError de arriba ya maneja el Swal
            console.error('Suscripción de deleteCategoria completada con error.', error);
          }
        });
      }
    });
  }

  // --- Método para guardar (crear o actualizar) categoría (AHORA LLAMA AL SERVICIO) ---
  // Usa la interfaz Categoria con idCatVac y nomCategoria
  guardarCategoria(): void {
    console.log('Llamando al servicio para guardar categoría:', this.categoriaSeleccionada);

    if (!this.categoriaSeleccionada.nomCategoria) {
      Swal.fire('Error', 'El nombre de la categoría es obligatorio.', 'error');
      return;
    }

    // Decide si crear o actualizar basado en si tiene idCatVac
    if (this.categoriaSeleccionada.idCatVac !== undefined) {
        console.log('Llamando al servicio para actualizar categoría:', this.categoriaSeleccionada.idCatVac);
        // ** Implementación de llamada al servicio (Actualizar) - DESCOMENTADO **
        this.categoriaService.updateCategoria(this.categoriaSeleccionada).pipe(
             catchError(error => {
                  console.error('Error en la suscripción al actualizar categoría:', error);
                  Swal.fire('Error', 'No se pudo actualizar la categoría.', 'error');
                  return throwError(() => new Error(error.message || 'Error desconocido al actualizar categoría'));
             })
        )
        .subscribe({
           next: (updatedCategoria) => {
               console.log('Categoría actualizada con éxito:', updatedCategoria);
               Swal.fire('¡Actualizada!', 'La categoría fue editada exitosamente.', 'success');
               this.cargarCategorias(); // === RECARGAR LA LISTA DESPUÉS DEL ÉXITO ===
               // Aquí normalmente cerrarías tu modal/formulario
           },
           error: (error) => {
               // El catchError de arriba ya maneja el Swal
               console.error('Suscripción de updateCategoria completada con error.', error);
           }
        });

    } else { // Crear nueva categoría
        console.log('Llamando al servicio para crear nueva categoría...');
         // ** Implementación de llamada al servicio (Crear) - DESCOMENTADO **
         this.categoriaService.createCategoria(this.categoriaSeleccionada).pipe(
              catchError(error => {
                   console.error('Error en la suscripción al crear categoría:', error);
                   Swal.fire('Error', 'No se pudo crear la categoría.', 'error');
                   return throwError(() => new Error(error.message || 'Error desconocido al crear categoría'));
              })
         )
         .subscribe({
            next: (newCategoria) => {
               console.log('Categoría creada con éxito:', newCategoria);
               Swal.fire('¡Creada!', 'La categoría ha sido guardada correctamente.', 'success');
               this.categoriaSeleccionada = { nomCategoria: '' }; // Limpiar formulario
               this.cargarCategorias(); // === RECARGAR LA LISTA DESPUÉS DEL ÉXITO ===
               // Aquí normalmente cerrarías tu modal/formulario
            },
            error: (error) => {
                 // El catchError de arriba ya maneja el Swal
                 console.error('Suscripción de createCategoria completada con error.', error);
            }
         });
    }
  }

  // Si consolidaste la lógica en guardarCategoria(), este método ya no es necesario
  // guardarCambiosCategoria(): void { ... }
}