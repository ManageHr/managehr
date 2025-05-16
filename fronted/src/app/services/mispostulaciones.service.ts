import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class MisPostulacionesService {
  private apiUrl = 'http://127.0.0.1:8000/api/postulaciones';

  constructor(private http: HttpClient) {}

  // Obtener todas las postulaciones del usuario autenticado o todos si es admin
  getPostulaciones(): Observable<any[]> {
    return this.http.get<{ data: any[] }>(this.apiUrl).pipe(
      map(response => response.data ?? [])
    );
  }

  // Buscar postulaciones por ID de vacante
  searchPostulacionesByVacantesId(vacanteId: number): Observable<any[]> {
    const url = `${this.apiUrl}/search?vacantesId=${vacanteId}`;
    return this.http.get<{ results: any[] }>(url).pipe(
      map(response => response.results ?? [])
    );
  }

  // Actualizar estado de una postulación por ID
  actualizarEstadoPostulacion(idPostulaciones: number, estado: string): Observable<any> {
    const url = `${this.apiUrl}/estado/${idPostulaciones}`;
    return this.http.put(url, { estado });
  }
}
