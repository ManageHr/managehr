import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',  // Esto asegura que el servicio sea accesible globalmente
})
export class MisPostulacionesService {
  private apiUrl = 'https://api.ejemplo.com/postulaciones';  // Cambia esta URL por la de tu API

  constructor(private http: HttpClient) {}

  // Método para obtener todas las postulaciones
  getPostulaciones(): Observable<any[]> {
    return this.http.get<any[]>(this.apiUrl);  // Realiza una solicitud GET a la API
  }

  // Método para buscar postulaciones por el ID de la vacante
  searchPostulacionesByVacantesId(vacanteId: number): Observable<any[]> {
    const url = `${this.apiUrl}/search?vacanteId=${vacanteId}`;  // Modifica según la API
    return this.http.get<any[]>(url);  // Realiza una solicitud GET con el ID de la vacante
  }

  // Método para obtener los detalles de una postulación por su ID
  getPostulacionById(id: number): Observable<any> {
    const url = `${this.apiUrl}/${id}`;  // URL para obtener los detalles de una postulación por ID
    return this.http.get<any>(url);  // Realiza una solicitud GET a la API
  }

  // Método para crear una nueva postulación
  createPostulacion(postulacion: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, postulacion);  // Realiza una solicitud POST para crear una nueva postulación
  }

  // Método para actualizar una postulación existente
  updatePostulacion(id: number, postulacion: any): Observable<any> {
    const url = `${this.apiUrl}/${id}`;  // URL para actualizar una postulación por ID
    return this.http.put<any>(url, postulacion);  // Realiza una solicitud PUT para actualizar la postulación
  }

  // Método para eliminar una postulación
  deletePostulacion(id: number): Observable<any> {
    const url = `${this.apiUrl}/${id}`;  // URL para eliminar una postulación por ID
    return this.http.delete<any>(url);  // Realiza una solicitud DELETE para eliminar la postulación
  }
}
