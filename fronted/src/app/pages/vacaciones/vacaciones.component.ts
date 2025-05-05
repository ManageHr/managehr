import { Component, OnInit, OnDestroy, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MenuComponent } from '../menu/menu.component';
import { FormBuilder, FormGroup, FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { VacacionesService } from 'src/app/services/vacaciones.service';
import { AuthService } from 'src/app/services/auth.service';
import { FilterNamePipe } from 'src/app/shared/filter-name.pipe';
import Swal from 'sweetalert2';

// Definimos la interfaz para los datos de vacaciones
interface Vacaciones {
  id: number;
  nombre: string;
  cargo: string;
  
  estado: 'Completado' | 'Rechazado' | 'Pendiente';
  fechaInicio: string;
  fechaFin: string;
  isExpanded?: boolean; // Propiedad para el estado del acordeón
}

// Definimos las columnas para usar como etiquetas en el acordeón
interface ColumnHeader {
    key: keyof Vacaciones| 'accion';
    label: string;
}


@Component({
  selector: 'app-vacaciones',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent,HttpClientModule,FilterNamePipe],
  templateUrl: './vacaciones.component.html',
  styleUrls: ['./vacaciones.component.scss']
})
export class VacacionesComponent implements OnInit {
  usuario: any = {};
  vacaciones: Vacaciones[] = [];
  vacacionSeleccionada: any = {
    nombre: '',
    cargo: '',
    estado: 'Pendiente',
    fechaInicio: '',
    fechaFin: ''
  };
  modoEdicion: boolean = false;
  formVacacion: FormGroup;
  editando = false;
  vacacionSeleccionadaId: number | null = null;
  filtroNombre: string = '';
  currentPage = 1;
  itemsPerPage = 5;
  totalPages=5;
  constructor(
    private vacacionesService: VacacionesService,
    private fb: FormBuilder,
    private authService: AuthService
  ) {
    this.formVacacion = this.fb.group({
      fecha_inicio: [''],
      fecha_fin: [''],
      motivo: [''],
      user_id: ['']
    });
  }
  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('Usuario logueado:', this.usuario);
    }
    this.cargarVacaciones();
  }
  cargarVacaciones(): void {
      this.vacacionesService.getVacaciones().subscribe({
        next: (data) => {
          this.vacacionSeleccionada = data;
          this.totalPages = Math.ceil(this.vacaciones.length / this.itemsPerPage);
        },
        error: (err) => {
          console.error('Error al cargar trazabilidad', err);
        }
      });
    }
    
    
    
    
    confirmDelete(id: number): void {
      Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.vacacionesService.eliminarVacacion(id).subscribe({
            next: () => {
              Swal.fire('Eliminado', 'Registro eliminado con éxito.', 'success');
              this.cargarVacaciones();
            },
            error: () => {
              Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
            }
          });
        }
      });
    }
  
    get trazabilidadFiltrada(): Vacaciones[] {
      return this.vacaciones.filter(t =>
        t.id.toString().includes(this.filtroNombre) ||
        t.fechaInicio.toLowerCase().includes(this.filtroNombre.toLowerCase())
      );
    }
    cambiarPagina(pagina: number): void {
      if (pagina >= 1 && pagina <= this.itemsPerPage) {
        this.currentPage = pagina;
      }
    }

  
}

