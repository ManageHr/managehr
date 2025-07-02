import { Component, OnInit} from '@angular/core';
import { CommonModule } from '@angular/common';
import { MenuComponent } from '../menu/menu.component';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { NotificacionesService } from '../../services/notificaciones.service';
import { Notificacion } from '../../services/notificaciones.service';
import { Usuarios }from '../../services/usuarios.service';
import { UsuariosService }from '../../services/usuarios.service';
import { Pipe, PipeTransform } from '@angular/core';
import { forkJoin } from 'rxjs';
import { ReactiveFormsModule } from '@angular/forms';
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';
import { SafeUrlPipe } from 'src/app/shared/safe-url.pipe';
import { FilterNamePipe } from 'src/app/shared/filter-name.pipe';
import { FiltroPersonalizadoPipe } from 'src/app/shared/filtro-personalizado.pipe';
import { FormsModule,FormBuilder,FormGroup,Validators } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import { AuthService } from 'src/app/services/auth.service';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend,
  registerables
} from 'chart.js';

Chart.register(
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
);

declare var bootstrap: any;
interface NotificationItem {
  id: number;
  name: string;
  description: string;
  date: string; 
  extraDetails?: any; 
}


@Component({
  selector: 'app-notificaciones-admin',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent, FontAwesomeModule,NgxPaginationModule,FiltroPersonalizadoPipe],
  templateUrl: './notificaciones-admin.component.html',
  styleUrls: ['./notificaciones-admin.component.scss']
})
export class NotificacionesAdminComponent implements OnInit {
  
  notificacionesSinAutorizar: Notificacion[] = [];
  notificacionesAutorizadas: Notificacion[] = [];
  selectedNotification: any = null;
  modalVisible: boolean = false;
  usuario: any = {};
  tienePermiso: boolean = false;
  archivoActual: string | null = null;
  graficoEstado: any;
  notificaciones: Notificacion [] = [];
  filtroNombre: string = "";
  itemsPerPage: number = 5;
  currentPage: number = 1;
  paginaSinAutorizadas: number = 1;
  paginaAutorizadas: number = 1;
  filtroUsuarios: string = '';
  filtroExternos: string = '';
  formhorasextra!: FormGroup;
  archivoSeleccionado!: File | null;
  usuariosExternos: any[] = [];
  usuarios: any[] = [];
   contratos: any []= [];
 contratoId: any = {};
    contratoNombre: any = {};
    graficoUsuario: Chart | undefined;
    graficoArea: Chart | undefined;
  
    totalPages1: number[] = [];
  constructor(
    private notificacionesService: NotificacionesService,
    private formBuilder: FormBuilder,
    private fb: FormBuilder,
  ) {}
  
  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      this.tienePermiso = [1, 4].includes(this.usuario?.rol);
      console.log('Usuario logueado:', this.usuario);
    }
    this.cargarNotificaciones();
  }
  cargarNotificaciones(): void {
    this.notificacionesService.getAll().subscribe(response => {
      const todas = response.Notificaciones;
      this.notificacionesSinAutorizar = todas.filter(n => n.estado === 0);
      this.notificacionesAutorizadas = todas.filter(n => n.estado !== 0);
    });
  }
  aceptarNotificacion(n: Notificacion): void {
    this.notificacionesService.actualizarEstado(n.idNotificacion, 1).subscribe(() => {
      this.cargarNotificaciones();
    });
  }

  rechazarNotificacion(n: Notificacion): void {
    this.notificacionesService.actualizarEstado(n.idNotificacion, 2).subscribe(() => {
      this.cargarNotificaciones();
    });
  }
  get notificacionesFiltradas() {
        const filtroLower = this.filtroNombre.toLowerCase();
    
        return this.notificaciones.filter(horasextra => {
          const usuario = horasextra.usuarioId;
          
          const usuarioTodos =this.usuario.obtenerUsuarios();
          if (!usuario) return false;
    
          const nombreCompleto = `${usuarioTodos.primerNombre ?? ''} ${usuarioTodos.segundoNombre ?? ''} ${usuarioTodos.primerApellido ?? ''} ${usuarioTodos.segundoApellido ?? ''}`.toLowerCase();
          const documento = usuarioTodos.numDocumento.toString();
    
          return (
            nombreCompleto.includes(filtroLower) ||
            documento.includes(filtroLower)
          );
        });
      }
    onArchivoSeleccionado(event: any): void {
      this.archivoSeleccionado = event.target.files[0] ?? null;
    }  
    verificarPermiso(): boolean {
      return [1, 4].includes(this.usuario?.rol);
    }
    get notificacionesSinAutorizadasFiltradas() {
  return this.notificacionesSinAutorizar.filter(n =>
    n.detalle?.toLowerCase().includes(this.filtroUsuarios.toLowerCase())
  );
}

get notificacionesAutorizadasFiltradas() {
  return this.notificacionesAutorizadas.filter(n =>
    n.detalle?.toLowerCase().includes(this.filtroExternos.toLowerCase())
  );
}


    
    

}

