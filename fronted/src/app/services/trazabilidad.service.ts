import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

export interface Trazabilidad {
  idTrazabilidad: number;
  fechaModificacion: string;
  iP: string;
  usuarioanterior: string;
  usuarionuevo: string;
  claveAnterior: string;
  claveNueva: string;
  numDocumento: number;
}

@Injectable({
  providedIn: 'root'
})
export class TrazabilidadService {
  private apiUrl = 'http://localhost:8000/api/trazabilidad';

  constructor(private http: HttpClient) {}

  obtenerTrazabilidad(): Observable<Trazabilidad[]> {
    return this.http.get<any>('http://localhost:8000/api/trazabilidad').pipe(
      map(res => res.tipodocumento) 
    );
  }

  eliminarTrazabilidad(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
  obtenerTrazas(): Observable<Trazabilidad[]> {
    return this.http.get<any>('http://localhost:8000/api/trazabilidad').pipe(
      map(res => res.tipodocumento));
  }
}


