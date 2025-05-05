import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class HorasextraService {
  private apiUrl = 'http://localhost:8000/api/horasextra';  // Asegúrate de que esta URL sea correcta

  constructor(private http: HttpClient) {}

  // Obtener todas las horas extras
  getAll(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  // Crear una nueva hora extra
  create(data: any): Observable<any> {
    return this.http.post(this.apiUrl, data);
  }

  // Eliminar una hora extra por su ID
  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }

  // Actualizar una hora extra por su ID
  update(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, data);
  }
}
