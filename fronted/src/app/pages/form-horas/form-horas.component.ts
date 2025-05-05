import { Component, OnInit } from '@angular/core';
import { MenuComponent } from "../menu/menu.component";
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { catchError, throwError } from 'rxjs';

// Interfaz para definir las solicitudes de horas extra
interface SolicitudHorasExtraDisplay {
    idHorasExtra?: number;
    descrip: string;
    fecha: string;
    nHorasExtra: number;
    tipohorasid: number;
    contratoId?: number | null;
}

// Interfaz corregida para cargar los tipos de horas del backend
interface TipoHora {
    idTipoHoras: number;
    nombreTipoHoras: string;
}

@Component({
  selector: 'app-form-horas',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule, HttpClientModule],
  templateUrl: './form-horas.component.html',
  styleUrls: ['./form-horas.component.scss']
})
export class FormHorasComponent implements OnInit {
  descrip: string = '';
  fecha: string = '';
  nHorasExtra: number | null = null;
  tipohorasid: number | null = null;
  contratoId: number | null = 1; // Valor provisional

  solicitudesHorasExtra: SolicitudHorasExtraDisplay[] = [];

  tiposHoras: TipoHora[] = [];

  constructor(private http: HttpClient) { }

  ngOnInit(): void {
    this.cargarTiposHoras();
  }

  enviarSolicitud(): void {
    if (this.contratoId === null) {
        console.error("El contratoId no está definido.");
        alert("Error: No se pudo obtener la información del contrato.");
        return;
    }

    // Formato JSON corregido
    const solicitud = {
      descrip: this.descrip,
      fecha: this.fecha,
      nHorasExtra: this.nHorasExtra,
      tipohorasid: this.tipohorasid,
      contratoId: this.contratoId
    };

    const backendUrl = 'http://localhost:8000/api/solicitudes-horas-extra';

    this.http.post<any>(backendUrl, solicitud).pipe(
      catchError((error: HttpErrorResponse) => {
        console.error('Error al enviar la solicitud:', error);
        let errorMessage = 'Hubo un error.';
        if (error.status === 422 && error.error?.errors) {
            errorMessage = 'Errores de validación:\n';
            for (const field in error.error.errors) {
                errorMessage += `${field}: ${error.error.errors[field].join(', ')}\n`;
            }
        } else if (error.status === 404) {
            errorMessage = `Error 404: No se encontró el recurso.`;
        } else if (error.error instanceof ErrorEvent) {
            errorMessage = `Error de red: ${error.error.message}`;
        } else {
            errorMessage = `Error del servidor: ${error.status} - ${error.statusText}`;
        }
        alert(errorMessage);
        return throwError(() => new Error('Error enviando solicitud de horas extra'));
      })
    ).subscribe(response => {
      console.log('Solicitud enviada exitosamente:', response);
      alert('Solicitud de horas extra enviada con éxito.');
      this.limpiarFormulario();
    });
  }

  limpiarFormulario(): void {
    this.descrip = '';
    this.fecha = '';
    this.nHorasExtra = null;
    this.tipohorasid = null;
  }

  cargarTiposHoras(): void {
    const urlTiposHoras = 'http://localhost:8000/api/tipos-horas';

    this.http.get<any>(urlTiposHoras).subscribe({
      next: (response) => {
        this.tiposHoras = response.tipohoras;
        console.log('Tipos de horas cargados:', this.tiposHoras);
      },
      error: (error) => {
        console.error('Error cargando tipos de horas:', error);
        alert('No se pudieron cargar los tipos de horas.');
      }
    });
  }
}