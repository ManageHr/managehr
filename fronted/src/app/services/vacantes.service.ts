import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Vacante {
  idVacantes: number; 
  nomVacante: string; 
  descripVacante: string;
  salario: string;
  expMinima: string; 
  cargoVacante: string;
  catVacId: number;
}

@Injectable({
  providedIn: 'root'
})
export class VacantesService {

  private apiUrl = 'http://localhost:8000/api/vacantesuser'; 

  constructor(private http: HttpClient) { }


  getVacantes(): Observable<Vacante[]> {
    return this.http.get<Vacante[]>(this.apiUrl);
  }

  
}