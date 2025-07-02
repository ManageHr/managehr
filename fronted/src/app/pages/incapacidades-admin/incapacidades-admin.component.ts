import { Component, OnInit } from '@angular/core';
import { IncapacidadService } from 'src/app/services/incapacidad.service'; 
import { AuthService } from 'src/app/services/auth.service';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { MenuComponent } from '../menu/menu.component';
import { CommonModule } from '@angular/common';
import { FilterNamePipe } from 'src/app/shared/filter-name.pipe';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import { Incapacidad } from 'src/app/services/incapacidad.service';
import { forkJoin } from 'rxjs';

import * as XLSX from 'xlsx';

import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
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

@Component({
  selector: 'app-incapacidades-admin',
  templateUrl: './incapacidades-admin.component.html',
  
  styleUrls: ['./incapacidades-admin.component.scss'],
  imports: [MenuComponent,CommonModule,FormsModule,NgxPaginationModule]
})
export class IncapacidadesAdminComponent implements OnInit {
  
  graficoEstado: any;
  usuario: any = {};
  filtroNombre: string = "";
  itemsPerPage: number = 5;
  currentPage: number = 1;
  incapacidades: Incapacidad [] = [];

  constructor(private incapacidadService: IncapacidadService) {}

  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('Usuario logueado:', this.usuario);
    }
    this.cargarIncapacidades();
    console.log('Incapacidad', this.incapacidades); 
  }

  cargarIncapacidades(): void {
  this.incapacidadService.obtenerTodas().subscribe({
    next: (res: Incapacidad[]) => {
      console.log('Incapacidad', res); // debe mostrar el array
      this.incapacidades = res || [];
    },
    error: (err) => console.error('Error al cargar incapacidades', err)
  });
}

  abrirModalReporteArea(){

  }
  abrirModalReporteUsuarios(){
    
  }
  abrirModalAgregar(){

  }
  eliminarIncapacidad(id: number): void {
  if (confirm('¿Está seguro de eliminar esta incapacidad?')) {
    this.incapacidadService.eliminar(id).subscribe(() => {
      this.cargarIncapacidades(); // recarga después de eliminar
    });
  }
}

calcularDias(fechaInicio: string, fechaFinal: string): number {
  const inicio = new Date(fechaInicio);
  const final = new Date(fechaFinal);
  const diferencia = final.getTime() - inicio.getTime();
  return Math.ceil(diferencia / (1000 * 60 * 60 * 24)) + 1; // incluye ambos días
}


  // Aquí irán los métodos para generar gráfico, PDF, Excel
}
