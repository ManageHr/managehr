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

 
obtenerSolicitudesIncapacidadUsuario(): Observable<any[]> {
  const token = localStorage.getItem('token');
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.get<any[]>(`http://localhost:8000/api/solicitudes-incapacidades`, { headers });

}
}
