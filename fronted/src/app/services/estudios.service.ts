import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class EstudiosService {
  private estudiosUrl = 'http://localhost:8000/api/estudios';
  private relacionesUrl = 'http://localhost:8000/api/hojasvidahasestudios';

  constructor(private http: HttpClient) {}

  getPorHojaDeVida(idHoja: number): Observable<any> {
    return this.http.get(`${this.estudiosUrl}/hoja/${idHoja}`);
  }

  create(data: any): Observable<any> {
    return this.http.post(this.estudiosUrl, data);
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.estudiosUrl}/${id}`);
  }

  createRelacionEstudio(payload: any): Observable<any> {
    return this.http.post<any>(this.relacionesUrl, payload);
  }
}
