import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { JefePersonalService } from '../../services/jefe-personal.service';
import { FilterNombre } from '../directorio/usuarios/filter-nombre'; // Ajusta la ruta si es necesario
import { MenuComponent } from '../menu/menu.component';

interface EmpleadosResponse {
  empleados: any[];
  area: string;
  message: string;
}

@Component({
  selector: 'app-jefe-personal',
  standalone: true,
  imports: [CommonModule, FormsModule,  MenuComponent],
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
  areaNombre: string = '';

  constructor(private jefePersonalService: JefePersonalService) {}

  ngOnInit() {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      const usuario = JSON.parse(userFromLocal);
      const jefeId = usuario.id || usuario.idUsuario; // Ajusta según tu backend
      console.log('Jefe ID:', jefeId);
      this.jefePersonalService.getEmpleadosPorJefe(jefeId).subscribe(
        (response: EmpleadosResponse) => {
          this.empleados = response.empleados || [];
          this.areaNombre = response.area || '';
          this.filtrarEmpleados();
          console.log('Empleados cargados:', this.empleados);
        },
        (error) => {
          console.error('Error al obtener empleados:', error);
        }
      );
    } else {
      console.error('No se encontró el usuario en localStorage');
    }
  }

  filtrarEmpleados(): void {
    if (this.filtroNombre.trim() === '') {
      this.empleadosFiltrados = this.empleados;
    } else {
      const filtro = this.filtroNombre.toLowerCase();
      this.empleadosFiltrados = this.empleados.filter(emp =>
        (emp.name && emp.name.toLowerCase().includes(filtro)) ||
        (emp.perfil?.primerApellido && emp.perfil.primerApellido.toLowerCase().includes(filtro)) ||
        (emp.perfil?.primerNombre && emp.perfil.primerNombre.toLowerCase().includes(filtro))
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
    alert('Ver detalles de: ' + (empleado.name || empleado.perfil?.primerNombre));
  }

  editarEmpleado(empleado: any) {
    // Aquí puedes navegar a la vista de edición o mostrar un modal
    alert('Editar empleado: ' + (empleado.name || empleado.perfil?.primerNombre));
  }

  cambiarPagina(pagina: number) {
    this.currentPage = pagina;
  }
}