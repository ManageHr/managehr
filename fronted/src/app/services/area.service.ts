import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Areas {
 
  idArea: number;
  nombreArea: string;         
  jefePersonal: string;
  idJefe:number
  estado: number;
}
@Injectable({
  providedIn: 'root'
})
export class AreaService {
  private apiUrl = 'http://localhost:8000/api/area';
  constructor(private http:HttpClient) { }
  obtenerAreas(): Observable<any> {
    return this.http.get<any>(this.apiUrl).pipe(
      map(res => {
        console.log('Respuesta del backend:', res); 
        return res.areas;
      })
      
    );
  }
  getJefesDePersonal(): Observable<any> {
    return this.http.get('http://localhost:8000/api/jefespersonal');
  }

  obtenerAreaId(id:number){
    return this.http.get<any>(`http://localhost:8000/api/area/${id}`);
  }
  eliminarAreaId(id:number){
    return this.http.delete<any>(`http://localhost:8000/api/area/${id}`);
  }
  agregarArea(area: any) {
    return this.http.post<any>('http://localhost:8000/api/area', area);

  }
  
  obtenerNombre(nombre: string): Observable<any> {
    return this.http.get(`http://localhost:8000/api/area-nombre/${nombre}`);
  }
  
  actualizarArea(id: number, datos: any) {
    return this.http.put(`http://localhost:8000/api/area/${id}`, datos);
  }


}
