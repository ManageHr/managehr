import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SolicitudesIncapacidadService {
  private apiUrl = 'http://localhost:8000/api/solicitudes-incapacidades';

  constructor(private http: HttpClient) { }

  enviarSolicitudIncapacidad(formData: FormData): Observable<any> {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.post(this.apiUrl, formData, { headers });
  }

  /**
   * Obtiene las solicitudes de incapacidad del usuario actual.
   * El endpoint puede ser tipo: /api/solicitudes-incapacidades/user o similar según tu backend.
   * Si tu backend tiene otra ruta, ajústala aquí.
   */
obtenerSolicitudesIncapacidadUsuario(): Observable<any[]> {
  const token = localStorage.getItem('token');
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.get<any[]>(`http://localhost:8000/api/solicitudes-incapacidades`, { headers });
  // Quita el `/user` si ese endpoint no existe, usa la ruta real de tu backend
}
}
