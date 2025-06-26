import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ExperienciaService {
  private url = 'http://localhost:8000/api/experiencia';

  constructor(private http: HttpClient) {}

  getPorHojaDeVida(idHoja: number): Observable<any> {
    return this.http.get(`${this.url}/hoja/${idHoja}`);
  }

  create(data: any): Observable<any> {
    return this.http.post(this.url, data);
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.url}/${id}`);
  }
}
