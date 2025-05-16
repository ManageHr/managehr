import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface SolicitudVacaciones {
  idVacaciones?: number;
  motivo: string;
  fechaInicio: string;
  fechaFinal: string;
  dias: number;
  estado: 'pendiente' | 'aprobado' | 'rechazado';
  contratoId: number;
}

@Injectable({
  providedIn: 'root'
})
export class SolicitudesVacacionesService {
  private apiUrl = 'http://127.0.0.1:8000/api/solicitudes-vacaciones-con-archivo'; // Usa `127.0.0.1` si hay problemas con `localhost`




  constructor(private http: HttpClient) {}

  enviarSolicitud(solicitud: SolicitudVacaciones): Observable<SolicitudVacaciones> {
    return this.http.post<SolicitudVacaciones>(this.apiUrl, solicitud);
  }

  obtenerSolicitudes(): Observable<SolicitudVacaciones[]> {
    return this.http.get<SolicitudVacaciones[]>(this.apiUrl);
  }
}
