import { Component, OnInit } from '@angular/core';
import { MenuComponent } from "../menu/menu.component";
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { Observable, catchError, throwError } from 'rxjs';

// Interfaz para mostrar solicitudes localmente
interface IncapacidadRequestDisplay {
  idIncapacidad?: number;
  descrip: string;
  archivo: File | null;
  fechaInicio: string;
  fechaFinal: string;
  contratoId?: number | null;
  estado: 'Pendiente' | 'Aprobada' | 'Rechazada' | 'desconocido';
}

@Component({
  selector: 'app-form-incapacidades',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule, HttpClientModule],
  templateUrl: './form-incapacidades.component.html',
  styleUrls: ['./form-incapacidades.component.scss']
})
export class FormIncapacidadesComponent implements OnInit {

  descrip: string = '';
  archivo: File | null = null;
  fechaInicio: string = '';
  fechaFinal: string = '';
  
  // Se inicializa contratoId fijo a 1 de manera provisional
  contratoId: number | null = 1; // <-- CORREGIDO, valor por defecto temporal

  solicitudesIncapacidades: IncapacidadRequestDisplay[] = [];

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    // Aquí podrías cargar el contratoId real o solicitudes existentes
  }

  onFileSelected(event: Event): void {
    const element = event.target as HTMLInputElement;
    if (element.files && element.files.length > 0) {
      this.archivo = element.files[0];
      console.log('Archivo seleccionado:', this.archivo);
    }
  }

  enviarSolicitud(): void {
    if (this.contratoId === null) {
      console.error("El contratoId no está definido.");
      alert("Error: No se pudo obtener la información del contrato.");
      return;
    }

    const formData = new FormData();
    formData.append('descrip', this.descrip);
    formData.append('fechaInicio', this.fechaInicio);
    formData.append('fechaFinal', this.fechaFinal);
    formData.append('contratoId', this.contratoId.toString());

    if (this.archivo) {
      formData.append('archivo', this.archivo, this.archivo.name);
    } else {
      console.log("No se seleccionó ningún archivo adjunto.");
    }

    const backendUrl = 'http://localhost:8000/api/solicitudes-incapacidades';

    this.http.post<any>(backendUrl, formData).pipe(
      catchError((error: HttpErrorResponse) => {
        console.error('Error al enviar la solicitud:', error);
        let errorMessage = 'Hubo un error al enviar la solicitud de incapacidad.';

        if (error.status === 422 && error.error?.errors) {
          errorMessage = 'Errores de validación:\n';
          for (const field in error.error.errors) {
            if (error.error.errors.hasOwnProperty(field)) {
              errorMessage += `${field}: ${error.error.errors[field].join(', ')}\n`;
            }
          }
        } else if (error.status === 404) {
          errorMessage = `Error 404: No se encontró el recurso en ${backendUrl}`;
        } else if (error.error instanceof ErrorEvent) {
          errorMessage = `Error de red o cliente: ${error.error.message}`;
        } else {
          errorMessage = `Error del servidor: ${error.status} - ${error.statusText || ''}`;
          if (error.error?.message) {
            errorMessage += `\nMensaje del backend: ${error.error.message}`;
          }
        }

        alert(errorMessage);
        return throwError(() => new Error('Error enviando solicitud de incapacidad'));
      })
    ).subscribe(response => {
      console.log('Solicitud enviada con éxito:', response);

      const solicitudGuardadaLocal: IncapacidadRequestDisplay = {
        idIncapacidad: response.data?.idIncapacidad,
        descrip: this.descrip,
        archivo: this.archivo,
        fechaInicio: this.fechaInicio,
        fechaFinal: this.fechaFinal,
        contratoId: this.contratoId,
        estado: response.data?.estado || 'Pendiente'
      };

      if (response.status === 201 || (response.message && response.message.includes('guardada con éxito'))) {
        this.solicitudesIncapacidades.push(solicitudGuardadaLocal);
        alert('Solicitud de incapacidad enviada con éxito.');
        this.limpiarFormulario();
      } else {
        console.warn('Respuesta inesperada:', response);
        alert('Solicitud enviada, pero el servidor no devolvió la respuesta esperada.');
      }
    });
  }

  limpiarFormulario(): void {
    this.descrip = '';
    this.archivo = null;
    this.fechaInicio = '';
    this.fechaFinal = '';

    const fileInput = document.getElementById('archivo') as HTMLInputElement;
    if (fileInput) {
      fileInput.value = '';
    }
  }

  cargarContratoIdUsuarioActual(): void {
    console.log("TODO: Implementar carga real del contratoId.");
  }

  cargarSolicitudesExistentes(): void {
    console.log("TODO: Implementar carga de solicitudes existentes.");
  }

}
