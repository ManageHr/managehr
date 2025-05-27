import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
export interface Contratos {
 
  idContrato: number;
  numDocumento: number;         
  tipoContratoId: number;
  estado: number;
  fechaIngreso: string;
  fechaFinal: string;
  documento: string;
  areaId: number;
}
  

@Injectable({
  providedIn: 'root'
})
export class ContratosService {
  
  private apiUrl = 'http://localhost:8000/api/contrato';

  constructor(private http: HttpClient) {}
  
  obtenerContratos(): Observable<any> {
    return this.http.get<any>(this.apiUrl).pipe(
      map(res => {
        console.log('Respuesta del backend:', res); 
        return res.contrato;
      })
    );
  }

 obtenerContratoPorDocumento(numDocumento: number): Observable<any> {
  const token = localStorage.getItem('token');
  const headers = {
    'Authorization': `Bearer ${token}`
  };
  return this.http.get<any>(
    `http://localhost:8000/api/contrato-usuario/${numDocumento}`,
    { headers }
  );
}


  agregarContrato(contrato: any) {
    return this.http.post<Contratos>('http://localhost:8000/api/contrato', contrato);
  }
  obtenerTiposContrato(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/tipocontrato').pipe(
      map(res => res.tipocontrato) 
    );
  }
  
  obtenerAreas():Observable<any[]>{
    return this.http.get<any>('http://localhost:8000/api/area').pipe(
      map(res => res.area)
    );
  }
  obtenerNacionalidades(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/nacionalidad').pipe(
      map(res => res.Nacionalidad) 
    );
  }
 
actualizarContratoParcial(id: number, formData: FormData) {
  return this.http.post(`http://localhost:8000/api/contrato/${id}/actualizar`, formData);
}

  
  
  obtenerEps(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/epss').pipe(
      map(res => res.Eps) 
    );
  }
  eliminarContrato(id: number): Observable<any> {
    return this.http.delete<any>(`http://localhost:8000/api/contrato/${id}`);
  }
  obtenerUsuarioPorDocumento(documento: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${documento}`);
  }
  
  obtenerContrato(id: number): Observable<any> {
    return this.http.get<any>(`http://localhost:8000/api/contrato/${id}`);
  }
  
  obtenerGeneros(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/genero').pipe(
      map(res => res.Genero) 
    );
  }
  
  obtenerTiposDocumento(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/tipodocumento').pipe(
      map(res => res.TipoDocumento) 
    );
  }
  
  obtenerEstadosCiviles(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/estadocivil').pipe(
      map(res => res.EstadoCivil) 
    );
  }
  
  obtenerPensiones(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/pensiones').pipe(
      map(res => res.Pensiones) 
    );
  }
}
