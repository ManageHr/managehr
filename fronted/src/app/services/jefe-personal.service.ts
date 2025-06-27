import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

interface EmpleadosResponse {
  empleados: any[];
  area: string;
  message: string;
}

@Injectable({ providedIn: 'root' })
export class JefePersonalService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  obtenerEmpleados(): Observable<any> {
    return this.http.get(`${this.apiUrl}/jefe-personal/empleados`);
  }

  obtenerAreas(): Observable<any> {
    return this.http.get(`${this.apiUrl}/jefe-personal/areas`);
  }

  getEmpleadosPorJefe(jefeId: number): Observable<EmpleadosResponse> {
    return this.http.get<EmpleadosResponse>(`${this.apiUrl}/jefe-personal/empleados/${jefeId}`);
  }
} 