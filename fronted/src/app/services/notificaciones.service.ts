import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Notificacion {

  idNotificacion: number;
  tipo: string;
  accion: string;
  fecha: string;
  detalle: string;
  estado: string;
  referenciaId: string;
  contratoId: string | null;


  areaId?: number;
  usuarioId?: number;

  contrato?: {
    idContrato: number;
    tipoContratoId: string;
    hojaDeVida: string;
    area: {
      idArea: number;
      nombreArea: string;
      jefePersonal: string;
      idJefe: string | null;
      estado: string;
    };
    cargoArea: string;
    fechaIngreso: string;
    fechaFinalizacion: string;
    archivo: string;
    estado: string;
    hoja_de_vida: {
      idHojaDeVida: number;
      claseLibretaMilitar: string;
      numeroLibretaMilitar: string;
      usuarioNumDocumento: string;
      usuario: {
        numDocumento: string;
        primerNombre: string;
        segundoNombre: string | null;
        primerApellido: string;
        segundoApellido: string | null;
        password: string;
        fechaNac: string;
        numHijos: string;
        contactoEmergencia: string;
        numContactoEmergencia: string;
        email: string;
        direccion: string;
        telefono: string;
        nacionalidadId: string;
        epsCodigo: string;
        generoId: string;
        tipoDocumentoId: string;
        estadoCivilId: string;
        pensionesCodigo: string;
        usersId: string;
        [key: string]: any;
      };
    };
  } | null;
}

export interface NotificacionesResponse {
  Notificaciones: Notificacion[];
  status: number;
}

@Injectable({
  providedIn: 'root'
})
export class NotificacionesService {
  private apiUrl = 'https://www.evensoft21.com/managehr/api/public/api/notificaciones';

  constructor(private http: HttpClient) { }

  getAll(): Observable<NotificacionesResponse> {
    return this.http.get<NotificacionesResponse>(this.apiUrl);
  }

  actualizarEstado(id: number, nuevoEstado: number): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}/estado`, { estado: nuevoEstado });
  }
}
