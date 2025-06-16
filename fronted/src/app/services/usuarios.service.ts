import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

export interface Usuarios {
  id: number;
  numDocumento: number;
  primerNombre: string;
  segundoNombre: string;
  primerApellido: string;
  segundoApellido: string;
  telefono: string;
  email: string;
  email_confirmation: string;
  direccion: string;
  password: string;
  password_confirmation: string;
  nacionalidadId?: number;
  epsCodigo?: string;
  generoId?: number;
  tipoDocumentoId?: number;
  estadoCivilId?: number;
  pensionesCodigo?: string;
  rol: number;
  usersId: number;


  user?: {
    id: number;
    name: string;
    email: string;
    rol: number;
  };
  nombreRol?: string;

  tipoDocumento?: { nombreTipoDocumento: string };
  genero?: { abreviacionGenero: string };
  estadoCivil?: { nombreEstado: string };
  eps?: { nombreEps: string };
  pensiones?: { nombrePensiones: string };
  nacionalidad?: { nombre: string };

}

@Injectable({
  providedIn: 'root'
})
export class UsuariosService {
  private apiUrl = 'http://localhost:8000/api/usuarios';

  constructor(private http: HttpClient) { }

  obtenerUsuarios(): Observable<any> {
    return this.http.get<any>(this.apiUrl).pipe(
      map(res => {
        console.log('Respuesta del backend:', res);
        return res.usuario;
      })
    );
  }
  agregarUsuario(usuario: any) {
    console.log('Usuario que se enviará a Laravel:', usuario);

    return this.http.post<any>('http://localhost:8000/api/usuarios', usuario);
  }
  obtenerUsersId(id: number): Observable<any> {
    return this.http.get<any>(`http://localhost:8000/api/auth/${id}`);
  }
  obtenerRol(id: number): Observable<any> {
    return this.http.get<any>(`http://localhost:8000/api/rols/${id}`);
  }
  obtenerNacionalidades(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/nacionalidad').pipe(
      map(res => res.Nacionalidad)
    );
  }
  obtenerUsuarioPorDocumento(numDocumento: string) {
    return this.http.get<any>(`http://localhost:8000/api/usuarios/documento/${numDocumento}`);
  }

  actualizarUsuarioParcial(id: number, datos: Partial<Usuarios>): Observable<any> {
    return this.http.patch<any>(`http://localhost:8000/api/usuarios/${id}`, datos);
  }

  eliminarUsuario(id: number): Observable<any> {
    return this.http.delete<any>(`http://localhost:8000/api/usuarios/${id}`);
  }
  eliminarUser(id: number): Observable<any> {
    return this.http.delete<any>(`http://localhost:8000/api/login/${id}`);
  }

  obtenerUsuario(id: number): Observable<any> {
    return this.http.get<any>(`http://localhost:8000/api/usuarios/${id}`);
  }

  obtenerGeneros(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/genero').pipe(
      map(res => res.genero)
    );
  }
  obtenerUsuariosConRelaciones(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/usuarios-con-relaciones').pipe(
      map(res => res.usuarios)
    );
  }



  obtenerTiposDocumento(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/tipodocumento').pipe(
      map(res => res.tipodocumento)
    );
  }

  obtenerEstadosCiviles(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/estadocivil').pipe(
      map(res => res.estadocivil)
    );
  }

  obtenerEps(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/epss').pipe(
      map(res => res.eps)
    );
  }

  obtenerPensiones(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/pensiones').pipe(
      map(res => res.pensiones)
    );
  }
  obtenerRoles(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/rols').pipe(
      map(res => res.rol)
    );
  }
  obtenerRolId(id: number): Observable<any> {
    return this.http.get<any>(`http://localhost:8000/api/rols/${id}`);
  }
  verificarExistenciaUsuario(email: string, documento: number): Observable<boolean> {
    return this.http.get<{ existe: boolean }>('http://localhost:8000/api/verificar-usuario', {
      params: { email, documento }
    }).pipe(
      map(res => res.existe)
    );
  }
  actualizarRol(userId: number, nuevoRol: number, correo: string): Observable<any> {
    return this.http.patch(`http://localhost:8000/api/auth/rol/${userId}`, { rol: nuevoRol, email: correo });
  }
  actualizarRolId(userId: number, nuevoRol: number): Observable<any> {
    return this.http.patch(`http://localhost:8000/api/auth/rol/${userId}`, { rol: nuevoRol });
  }


  getUsuariosRolCinco(): Observable<any[]> {
    return this.http.get<any>('http://localhost:8000/api/auth/rolcinco').pipe(
      map(res => res.usuarios)
    );
  }



}

