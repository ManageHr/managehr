import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class JefePersonalService {
  private apiUrl = 'http://localhost:8000/api/jefe-personal';

  constructor(private http: HttpClient) {}

  obtenerEmpleados(): Observable<any> {
    return this.http.get(`${this.apiUrl}/empleados`);
  }

  obtenerAreas(): Observable<any> {
    return this.http.get(`${this.apiUrl}/areas`);
  }
} 