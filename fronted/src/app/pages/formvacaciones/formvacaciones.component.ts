import { Component, OnInit } from '@angular/core';
import { MenuComponent } from "../menu/menu.component";
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { SolicitudesVacacionesService } from '../../services/solicitudes-vacaciones.service'; // Importamos el nuevo servicio

interface SolicitudVacacionesDisplay {
    idVacaciones?: number;
    motivo: string;  // 🔹 Se cambió `descrip` por `motivo`
    fechaInicio: string;
    fechaFinal: string;
    dias: number;
    estado: 'pendiente' | 'rechazado' | 'aprobado';  // 🔹 Eliminamos `"desconocido"`
    contratoId?: number;
}

@Component({
  selector: 'app-formvacaciones',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule, HttpClientModule],
  templateUrl: './formvacaciones.component.html',
  styleUrls: ['./formvacaciones.component.scss'] 
})
export class FormvacacionesComponent implements OnInit {
  motivo: string = ''; // 🔹 Se cambió `descrip` por `motivo`
  fechaInicio: string = '';
  fechaFinal: string = '';
  dias: number = 0;
  estado: 'pendiente' = 'pendiente';
  contratoId: number | null = 1; // Simulación, modificar según lógica real

  solicitudesVacaciones: SolicitudVacacionesDisplay[] = [];

  constructor(private solicitudesVacacionesService: SolicitudesVacacionesService) {} // 🔹 Cambio de servicio

  ngOnInit(): void {}

  // Método para calcular días entre fechas
  calcularDias(): void {
    if (this.fechaInicio && this.fechaFinal) {
      const inicio = new Date(this.fechaInicio);
      const final = new Date(this.fechaFinal);
      this.dias = Math.max(Math.ceil((final.getTime() - inicio.getTime()) / (1000 * 60 * 60 * 24)), 0);
    } else {
      this.dias = 0;
    }
  }

  enviarSolicitud(): void {
    if (!this.contratoId) {
        alert("Error: No se pudo obtener la información del contrato.");
        return;
    }

    const solicitud: SolicitudVacacionesDisplay = {
      motivo: this.motivo,  // 🔹 Se cambió `descrip` por `motivo`
      fechaInicio: this.fechaInicio,
      fechaFinal: this.fechaFinal,
      dias: this.dias,
      estado: 'pendiente',
      contratoId: this.contratoId
    };

    this.solicitudesVacacionesService.enviarSolicitud(solicitud).subscribe(
      (response: SolicitudVacacionesDisplay) => {  // 🔹 Se define el tipo explícitamente
        console.log('Solicitud enviada con éxito:', response);
        this.solicitudesVacaciones.push(response);
        alert('Solicitud de vacaciones enviada.');
        this.limpiarFormulario();
      },
      (error: any) => {  // 🔹 Se tipifica correctamente `error`
        console.error('Error al enviar la solicitud:', error);
        alert('Hubo un error al enviar la solicitud.');
      }
    );
  }

  limpiarFormulario(): void {
    this.motivo = ''; // 🔹 Se cambió `descrip` por `motivo`
    this.fechaInicio = '';
    this.fechaFinal = '';
    this.dias = 0;
  }
}
