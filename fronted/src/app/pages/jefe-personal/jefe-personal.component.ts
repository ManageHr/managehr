import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { JefePersonalService } from '../../services/jefe-personal.service';
import { FilterNombre } from '../directorio/usuarios/filter-nombre'; // Ajusta la ruta si es necesario
import { MenuComponent } from '../menu/menu.component';


@Component({
  selector: 'app-jefe-personal',
  standalone: true,
  imports: [CommonModule, FormsModule, FilterNombre, MenuComponent],
  templateUrl: './jefe-personal.component.html',
  styleUrls: ['./jefe-personal.component.scss']
})
export class JefePersonalComponent implements OnInit {
  empleados: any[] = [];
  empleadosFiltrados: any[] = [];
  filtroNombre: string = '';
  currentPage = 1;
  itemsPerPage = 5;
  totalPages = 1;

  constructor(private jefePersonalService: JefePersonalService) {}

  ngOnInit() {
    this.cargarEmpleados();
  }

  cargarEmpleados(): void {
    this.jefePersonalService.obtenerEmpleados().subscribe({
      next: (data) => {
        this.empleados = data.empleados || [];
        this.filtrarEmpleados();
      },
      error: (err) => {
        console.error('Error al cargar empleados', err);
      }
    });
  }

  filtrarEmpleados(): void {
    if (this.filtroNombre.trim() === '') {
      this.empleadosFiltrados = this.empleados;
    } else {
      const filtro = this.filtroNombre.toLowerCase();
      this.empleadosFiltrados = this.empleados.filter(emp =>
        (emp.name && emp.name.toLowerCase().includes(filtro)) ||
        (emp.perfil?.primerApellido && emp.perfil.primerApellido.toLowerCase().includes(filtro))
      );
    }
    this.totalPages = Math.ceil(this.empleadosFiltrados.length / this.itemsPerPage);
    this.currentPage = 1;
  }

  get paginatedEmpleados() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.empleadosFiltrados.slice(start, start + this.itemsPerPage);
  }

  verEmpleado(empleado: any) {
    // Aquí puedes navegar a la vista de detalle o mostrar un modal
    alert('Ver detalles de: ' + empleado.name);
  }

  editarEmpleado(empleado: any) {
    // Aquí puedes navegar a la vista de edición o mostrar un modal
    alert('Editar empleado: ' + empleado.name);
  }

  cambiarPagina(pagina: number) {
    this.currentPage = pagina;
  }
}