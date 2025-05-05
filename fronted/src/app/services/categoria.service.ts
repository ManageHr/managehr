import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';

export interface Categoria {
  idCatVac?: number;
  nomCategoria: string;
}

@Injectable({
  providedIn: 'root'
})
export class CategoriaService {

  private categoriasEndpointUrl = 'http://localhost:8000/api/categoriavacantes';

  constructor(private http: HttpClient) { }

  getCategorias(): Observable<Categoria[]> {
    console.log(`[CategoriaService] Fetching categories from: ${this.categoriasEndpointUrl}`);
    return this.http.get<any>(this.categoriasEndpointUrl).pipe(
      map(res => {
        console.log('[CategoriaService] API Response (GET List):', res);
        if (res && Array.isArray(res.categoriavacantes)) {
          return res.categoriavacantes as Categoria[];
        } else {
           console.error('[CategoriaService] Unexpected API response format for GET categories:', res);
           throw new Error('Invalid API response format: Expected an array under "categoriavacantes".');
        }
      }),
      catchError(error => {
         console.error('[CategoriaService] Error fetching categories:', error);
         return throwError(() => new Error('Failed to fetch categories. Please try again later.'));
      })
    );
  }

  getCategoria(idCatVac: number): Observable<Categoria> {
     const url = `${this.categoriasEndpointUrl}/${idCatVac}`;
     console.log(`[CategoriaService] Fetching category with idCatVac: ${idCatVac} from: ${url}`);
     return this.http.get<any>(url).pipe(
        map(res => {
             console.log(`[CategoriaService] API Response (GET id ${idCatVac}):`, res);
             return res as Categoria;
        }),
         catchError(error => {
             console.error(`[CategoriaService] Error fetching category id ${idCatVac}:`, error);
             return throwError(() => new Error(`Failed to fetch category ${idCatVac}.`));
         })
     );
  }

  createCategoria(categoria: Categoria): Observable<Categoria> {
     console.log('[CategoriaService] Creating category:', categoria);
     const dataToSend = { nomCategoria: categoria.nomCategoria };
     return this.http.post<any>(this.categoriasEndpointUrl, dataToSend).pipe(
         map(res => {
             console.log('[CategoriaService] API Response (POST):', res);
             return res as Categoria;
         }),
          catchError(error => {
              console.error('[CategoriaService] Error creating category:', error);
              let errorMessage = 'Failed to create category.';
               if (error.error && error.error.message) {
                   errorMessage = error.error.message;
               } else if (error.error && error.error.errors) {
                    errorMessage += ' Validation failed: ' + JSON.stringify(error.error.errors);
               }
              return throwError(() => new Error(errorMessage));
          })
     );
  }

  updateCategoria(categoria: Categoria): Observable<Categoria> {
    if (categoria.idCatVac === undefined) {
      const errorMsg = 'Cannot update category without idCatVac';
      console.error(`[CategoriaService] ${errorMsg}`);
      return throwError(() => new Error(errorMsg));
    }
    const url = `${this.categoriasEndpointUrl}/${categoria.idCatVac}`;
    console.log(`[CategoriaService] Updating category with idCatVac: ${categoria.idCatVac}`, categoria);
    const dataToSend = { nomCategoria: categoria.nomCategoria };
    return this.http.put<any>(url, dataToSend).pipe(
         map(res => {
             console.log('[CategoriaService] API Response (PUT):', res);
             return res as Categoria; 
         }),
         catchError(error => {
             console.error(`[CategoriaService] Error updating category id ${categoria.idCatVac}:`, error);
              let errorMessage = `Failed to update category ${categoria.idCatVac}.`;
              if (error.error && error.error.message) {
                  errorMessage = error.error.message;
              } else if (error.error && error.error.errors) {
                   errorMessage += ' Validation failed: ' + JSON.stringify(error.error.errors);
              }
             return throwError(() => new Error(errorMessage));
         })
    );
  }

  deleteCategoria(idCatVac: number): Observable<void> {
    const url = `${this.categoriasEndpointUrl}/${idCatVac}`;
    console.log(`[CategoriaService] Deleting category with idCatVac: ${idCatVac} from: ${url}`);
    return this.http.delete<void>(url).pipe(
         catchError(error => {
             console.error(`[CategoriaService] Error deleting category id ${idCatVac}:`, error);
              let errorMessage = `Failed to delete category ${idCatVac}.`;
              if (error.error && error.error.message) {
                  errorMessage = error.error.message;
              }
             return throwError(() => new Error(errorMessage));
         })
    );
  }
}
