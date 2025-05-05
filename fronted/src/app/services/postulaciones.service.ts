import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

// Definimos la interfaz Postulacion aquí
export interface Postulacion {
  idPostulaciones?: number;
  fechaPostulacion: string;
  estado: 'Aceptado' | 'Rechazado' | 'Pendiente';
  vacantesId: number;
  // Puedes añadir otras propiedades si tu API las devuelve (ej: id del usuario, nombre, etc.)
  userId?: number; // Ejemplo: si tu backend asocia la postulación a un usuario
}

@Injectable({
  providedIn: 'root'
})
export class PostulacionesService {

  // === URL BASE DE TU API EN LARAVEL ===
  private apiUrl = 'http://localhost:8000/api/postulaciones'; // Esta URL es la que usaremos para crear
  private searchApiUrl = 'http://localhost:8000/api/postulaciones/buscar/vacante';

  constructor(private http: HttpClient) { }

  /**
   * Obtiene el listado de todas las postulaciones desde la API (para admin).
   * @returns Un Observable con el arreglo de postulaciones.
   */
  getPostulaciones(): Observable<Postulacion[]> {
    console.log(`[PostulacionesService] Llamando a GET ${this.apiUrl}`);
    return this.http.get<any>(this.apiUrl).pipe(
      map(response => {
        const postulacionesApi = response.data || response.postulaciones || [];
        console.log('[PostulacionesService] Datos recibidos de getPostulaciones:', postulacionesApi);
        return postulacionesApi.map((p: any) => ({
          idPostulaciones: p.idPostulaciones,
          fechaPostulacion: p.fechaPostulacion,
          estado: this.mapEstadoFromBackend(p.estado),
          vacantesId: p.vacantesId,
          // Mapear otros campos si existen en la respuesta
          // userId: p.userId,
        }));
      })
    );
  }

  /**
   * Busca postulaciones por ID de vacante (para admin).
   * @param vacantesId El ID de la vacante.
   * @returns Un Observable con el arreglo de postulaciones para esa vacante.
   */
  searchPostulacionesByVacantesId(vacantesId: number): Observable<Postulacion[]> {
    const url = `${this.searchApiUrl}/${vacantesId}`;
    console.log(`[PostulacionesService] Llamando a GET ${url}`);
    return this.http.get<any>(url).pipe(
      map(response => {
        const resultadosApi = response.results || response.data || [];
        console.log('[PostulacionesService] Datos recibidos de searchPostulacionesByVacantesId:', resultadosApi);
        return resultadosApi.map((p: any) => ({
          idPostulaciones: p.idPostulaciones,
          fechaPostulacion: p.fechaPostulacion,
          estado: this.mapEstadoFromBackend(p.estado),
          vacantesId: p.vacantesId,
          // Mapear otros campos
          // userId: p.userId,
        }));
      })
    );
  }

  /**
   * Actualiza el estado de una postulación existente (para admin).
   * @param postulacionId El ID de la postulación a actualizar.
   * @param newStatus El nuevo estado ('Aceptado', 'Rechazado', 'Pendiente').
   * @returns Un Observable con la postulación actualizada.
   */
  updatePostulacionStatus(postulacionId: number, newStatus: Postulacion['estado']): Observable<Postulacion> {
    console.log(`[PostulacionesService] Llamando a PUT/PATCH para actualizar estado de postulación ${postulacionId} a ${newStatus}`);
    const estadoBackend = this.mapEstadoToBackend(newStatus);
    // <<<< Asegúrate de que esta URL de actualización sea correcta en tu backend >>>>
    // Si tu ruta PUT/PATCH es solo /api/postulaciones/{id}, usa `${this.apiUrl}/${postulacionId}`
    const updateUrl = `${this.apiUrl}/${postulacionId}/status`; // Ejemplo de URL si tienes un endpoint específico para status
    return this.http.put<any>(updateUrl, { estado: estadoBackend }).pipe( // O .patch<any>(...)
      map(response => {
        // Asegúrate de que la respuesta de actualización contenga los datos de la postulación actualizada
        const updatedPostulacionApi = response.postulacion || response.data;
        console.log('[PostulacionesService] Datos recibidos de updatePostulacionStatus:', updatedPostulacionApi);
        if (!updatedPostulacionApi) throw new Error('Respuesta inválida de la API de actualización de estado');
        return {
          idPostulaciones: updatedPostulacionApi.idPostulaciones,
          fechaPostulacion: updatedPostulacionApi.fechaPostulacion,
          estado: this.mapEstadoFromBackend(updatedPostulacionApi.estado),
          vacantesId: updatedPostulacionApi.vacantesId,
          // Mapear otros campos
          // userId: updatedPostulacionApi.userId,
        };
      })
    );
  }

  /**
   * <<<< NUEVO MÉTODO: Crea una nueva postulación >>>>
   * Envía una petición POST al backend para registrar una nueva postulación.
   * @param postulacionData Los datos de la postulación (mínimo vacantesId, posiblemente userId).
   * @returns Un Observable con la postulación creada según la respuesta del backend.
   */
   crearPostulacion(postulacionData: { vacantesId: number /*, userId?: number */ }): Observable<Postulacion> {
     console.log(`[PostulacionesService] Llamando a POST ${this.apiUrl} con datos:`, postulacionData);
     // Hacemos la petición POST a la URL base de postulaciones
     // El backend se encargará de añadir la fecha y el estado 'pendiente'.
     return this.http.post<Postulacion>(this.apiUrl, postulacionData).pipe(
       map(response => {
          // Opcional: Si el backend devuelve la postulación creada con todos sus datos (id, fecha, estado)
          // puedes mapear la respuesta aquí si es necesario, de forma similar a los otros métodos.
          // console.log('[PostulacionesService] Postulación creada recibida:', response);
          return response; // Asumiendo que el backend devuelve la Postulacion creada
       })
       // Puedes añadir catchError aquí si quieres manejar errores a nivel de servicio
       /*
       catchError(error => {
          console.error('[PostulacionesService] Error al crear postulación:', error);
          return throwError(error); // Re-lanzar el error para que el componente lo maneje
       })
       */
     );
   }


  private mapEstadoFromBackend(backendEstado: number): Postulacion['estado'] {
    switch (backendEstado) {
      case 1: return 'Aceptado';
      case 2: return 'Rechazado';
      case 0:
      default: return 'Pendiente';
    }
  }

  private mapEstadoToBackend(frontendEstado: Postulacion['estado']): number {
    switch (frontendEstado) {
      case 'Aceptado': return 1;
      case 'Rechazado': return 2;
      case 'Pendiente':
      default: return 0;
    }
  }

}