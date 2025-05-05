import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Define una interfaz para la estructura de una vacante si conoces cómo son los datos.
// Esto ayuda a tener mejor tipado y autocompletado. Si no estás seguro, puedes usar 'any' por ahora.
export interface Vacante {
  idVacantes: number; // Usar el nombre de la clave primaria del backend
  nomVacante: string; // Usar el nombre del campo del backend para el título
  descripVacante: string; // Usar el nombre del campo del backend para la descripción
  salario: string; // Este ya coincidía
  expMinima: string; // Añadir el campo de experiencia mínima
  cargoVacante: string; // Añadir el campo de cargo
  catVacId: number;
}

@Injectable({
  providedIn: 'root' // Esto registra el servicio a nivel global en la aplicación
})
export class VacantesService {

  // Reemplaza esta URL con la URL base de tu API de vacantes
  private apiUrl = 'http://localhost:8000/api/vacantesuser'; // Ejemplo: 'http://localhost:3000/api/vacantes'

  constructor(private http: HttpClient) { }

  /**
   * Obtiene el listado de todas las vacantes desde la API.
   * @returns Un Observable con el arreglo de vacantes.
   */
  getVacantes(): Observable<Vacante[]> {
    return this.http.get<Vacante[]>(this.apiUrl);
  }

  
}