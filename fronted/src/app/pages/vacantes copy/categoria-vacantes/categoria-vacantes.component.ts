import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import Swal from 'sweetalert2'; 


import { Categoria, CategoriaService } from '../../../services/categoria.service';

import { FilterCategoriaPipe } from './filter-categoria';

import { AuthService } from '../../../services/auth.service';
import { MenuComponent } from '../../menu/menu.component';



import { catchError } from 'rxjs/operators'; 
import { throwError } from 'rxjs'; 


@Component({
  selector: 'app-categoria-vacantes',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MenuComponent,
    
    
  ],
  templateUrl: './categoria-vacantes.component.html',
  styleUrls: ['./categoria-vacantes.component.scss']
})
export class CategoriaVacantesComponent implements OnInit {

 
  categorias: Categoria[] = [];

  
  categoriaSeleccionada: Categoria = { nomCategoria: '' };

  usuario: any = {};

  paginaActual: number = 1;
  categoriasPorPagina: number = 5;
 
  filtroNombre: string = "";

  
  constructor(
    public authService: AuthService,
    
    private categoriaService: CategoriaService 
  ) { }

  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('Usuario logueado:', this.usuario);
    }

    
    this.cargarCategorias();
  }

  
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
        
        console.error('Suscripción de cargarCategorias completada con error.', error);
      }
    });
  }


  abrirModalAgregarCategoria(): void {
    this.categoriaSeleccionada = { nomCategoria: '' };
    console.log('Abriendo modal para agregar categoría...');
   
  }

 
  editarCategoria(categoria: Categoria): void {
    this.categoriaSeleccionada = { ...categoria };
    console.log('Editando categoría:', categoria);
    
  }

  
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
            this.cargarCategorias(); 
          },
          error: (error) => {
            
            console.error('Suscripción de deleteCategoria completada con error.', error);
          }
        });
      }
    });
  }

 
  guardarCategoria(): void {
    console.log('Llamando al servicio para guardar categoría:', this.categoriaSeleccionada);

    if (!this.categoriaSeleccionada.nomCategoria) {
      Swal.fire('Error', 'El nombre de la categoría es obligatorio.', 'error');
      return;
    }

    
    if (this.categoriaSeleccionada.idCatVac !== undefined) {
        console.log('Llamando al servicio para actualizar categoría:', this.categoriaSeleccionada.idCatVac);
        
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
               this.cargarCategorias();
              
           },
           error: (error) => {
               
               console.error('Suscripción de updateCategoria completada con error.', error);
           }
        });

    } else {
        console.log('Llamando al servicio para crear nueva categoría...');
        
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
               this.categoriaSeleccionada = { nomCategoria: '' }; 
               this.cargarCategorias(); 
               
            },
            error: (error) => {
                 
                 console.error('Suscripción de createCategoria completada con error.', error);
            }
         });
    }
  }
  get categoriasFiltradas(): Categoria[] {
    return this.categorias.filter(cat =>
      cat.nomCategoria.toLowerCase().includes(this.filtroNombre.toLowerCase())
    );
  }

  get categoriasPaginadas(): Categoria[] {
    const inicio = (this.paginaActual - 1) * this.categoriasPorPagina;
    return this.categoriasFiltradas.slice(inicio, inicio + this.categoriasPorPagina);
  }

  get totalPaginas(): number {
    return Math.ceil(this.categoriasFiltradas.length / this.categoriasPorPagina);
  }

  cambiarPagina(pagina: number): void {
    if (pagina >= 1 && pagina <= this.totalPaginas) {
      this.paginaActual = pagina;
    }
  }

}