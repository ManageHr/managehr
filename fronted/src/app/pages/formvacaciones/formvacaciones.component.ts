import { Component, OnInit } from '@angular/core';
import { MenuComponent } from "../menu/menu.component";
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { SolicitudesVacacionesService } from '../../services/solicitudes-vacaciones.service';

interface SolicitudVacacionesDisplay {
    idVacaciones?: number;
    motivo: string;
    fechaInicio: string;
    fechaFinal: string;
    dias: number;
    estado: 'pendiente' | 'rechazado' | 'aprobado';
    contratoId: number;
}

@Component({
  selector: 'app-formvacaciones',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule, HttpClientModule],
  templateUrl: './formvacaciones.component.html',
  styleUrls: ['./formvacaciones.component.scss'] 
})
export class FormvacacionesComponent implements OnInit {
  motivo: string = '';
  fechaInicio: string = '';
  fechaFinal: string = '';
  dias: number = 0;
  estado: 'pendiente' = 'pendiente';
  contratoId: number = 1; // 🔹 Se eliminó `null`

  solicitudesVacaciones: SolicitudVacacionesDisplay[] = [];

  constructor(private solicitudesVacacionesService: SolicitudesVacacionesService) {}

  ngOnInit(): void {}

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
    // Validación básica antes de enviar
    if (!this.motivo || !this.fechaInicio || !this.fechaFinal || this.dias <= 0 || !this.contratoId) {
        alert("Error: Todos los campos deben estar completos.");
        return;
    }

    // Formateo de fechas en `YYYY-MM-DD`
    this.fechaInicio = new Date(this.fechaInicio).toISOString().split('T')[0];
    this.fechaFinal = new Date(this.fechaFinal).toISOString().split('T')[0];

    const solicitud: SolicitudVacacionesDisplay = {
      motivo: this.motivo,
      fechaInicio: this.fechaInicio,
      fechaFinal: this.fechaFinal,
      dias: this.dias,
      estado: 'pendiente',
      contratoId: this.contratoId
    };

    this.solicitudesVacacionesService.enviarSolicitud(solicitud).subscribe(
      (response: SolicitudVacacionesDisplay) => {
        console.log('Solicitud enviada con éxito:', response);
        this.solicitudesVacaciones.push(response);
        alert('Solicitud de vacaciones enviada.');
        this.limpiarFormulario();
      },
      (error: any) => {
        console.error('Error al enviar la solicitud:', error);
        alert('Hubo un error al enviar la solicitud.');
      }
    );
  }

  limpiarFormulario(): void {
    this.motivo = '';
    this.fechaInicio = '';
    this.fechaFinal = '';
    this.dias = 0;
  }
}
