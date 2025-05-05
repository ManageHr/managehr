import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Vacacion {
  id?: number;
  nombre: string;
  cargo: string;
  estado: 'Completado' | 'Rechazado' | 'Pendiente';
  fechaInicio: string;
  fechaFin: string;
}
@Injectable({
  providedIn: 'root'
})
export class VacacionesService {

  private apiUrl = 'http://localhost:8000/api/vacaciones'; // Asegúrate que tu backend use prefix /api

  constructor(private http: HttpClient) {}

  getVacaciones(): Observable<Vacacion[]> {
    return this.http.get<Vacacion[]>(this.apiUrl);
  }

  getVacacion(id: number): Observable<Vacacion> {
    return this.http.get<Vacacion>(`${this.apiUrl}/${id}`);
  }

  agregarVacacion(data: Vacacion): Observable<Vacacion> {
    return this.http.post<Vacacion>(this.apiUrl, data);
  }

  actualizarVacacion(id: number, data: Vacacion): Observable<Vacacion> {
    return this.http.put<Vacacion>(`${this.apiUrl}/${id}`, data);
  }

  eliminarVacacion(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
