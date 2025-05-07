import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = 'https://tu-api.com/api/profile'; // Endpoint para obtener el perfil
  private updateUrl = 'https://tu-api.com/api/profile/update'; // Endpoint para actualizar el perfil

  constructor(private http: HttpClient) {}

  /**
   * Método para obtener el perfil del usuario autenticado.
   */
  getUserProfile(): Observable<User> {
    const token = localStorage.getItem('token'); 
    if (!token) {
      throw new Error('Token no encontrado en localStorage'); // Manejo de error si no hay token
    }
    
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    return this.http.get<User>(this.apiUrl, { headers });
  }

  /**
   * Método para actualizar el perfil del usuario.
   * @param user Datos actualizados del usuario
   */
  updateUserProfile(user: User): Observable<any> {
    const token = localStorage.getItem('token'); 
    if (!token) {
      throw new Error('Token no encontrado en localStorage'); // Manejo de error si no hay token
    }

    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    return this.http.put(this.updateUrl, user, { headers });
  }
}
