import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
export interface Notificacion {
  idNotificacion: number;
  tipo: string;
  accion: string;
  fecha: string;
  detalle: string;
  estado: number;
  usuarioId: number;
  areaId?: number;
  referenciaId: number;
}
@Injectable({
  providedIn: 'root'
})
export class NotificacionesService {
 private apiUrl = 'http://localhost:8000/api/notificaciones';
  constructor(private http: HttpClient) { }
  getAll(): Observable<{ Notificaciones: Notificacion[] }> {
    return this.http.get<{ Notificaciones: Notificacion[] }>(this.apiUrl);
  }

  actualizarEstado(id: number, nuevoEstado: number): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, { estado: nuevoEstado });
  }
}
