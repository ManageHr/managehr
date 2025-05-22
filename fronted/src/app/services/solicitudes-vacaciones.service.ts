import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface SolicitudVacaciones {
  idVacaciones?: number;
  motivo: string;
  fechaInicio: string;
  fechaFinal: string;
  dias: number;
  contratoId: number;
  estado?: 'pendiente' | 'aprobado' | 'rechazado'; // ✅ ahora es opcional
}

@Injectable({
  providedIn: 'root'
})
export class SolicitudesVacacionesService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }

  enviarSolicitud(solicitud: SolicitudVacaciones): Observable<SolicitudVacaciones> {
    return this.http.post<SolicitudVacaciones>(
      `${this.apiUrl}/solicitudes-vacaciones-con-archivo`,
      solicitud,
      { headers: this.getHeaders() }
    );
  }

  obtenerSolicitudes(): Observable<SolicitudVacaciones[]> {
    return this.http.get<SolicitudVacaciones[]>(
      `${this.apiUrl}/solicitudes-vacaciones-con-archivo`,
      { headers: this.getHeaders() }
    );
  }

  obtenerContratoDelUsuario(numDocumento: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/contrato-usuario/${numDocumento}`, {
      headers: this.getHeaders()
    });
  }
}
